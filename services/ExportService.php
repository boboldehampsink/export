<?php

namespace Craft;

/**
 * Export service.
 *
 * Handles common export logics.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@nerds.company>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   MIT
 *
 * @link      http://github.com/boboldehampsink
 */
class ExportService extends BaseApplicationComponent
{
    /**
     * Contains the working export service's name.
     *
     * @var IExportElementType|bool
     */
    private $_service;

    /**
     * Custom <tr> paths.
     *
     * @var array
     */
    public $customTableRowPaths = array();

    /**
     * Whether custom table row paths have been loaded.
     *
     * @var bool
     */
    private $_loadedTableRowPaths = false;

    /**
     * Saves an export map to the database.
     *
     * @param array $settings
     * @param array $map
     */
    public function saveMap(array $settings, array $map)
    {
        // Unset non-map settings
        unset($settings['limit'], $settings['offset']);
        ksort($settings);

        // Set criteria
        $criteria = new \CDbCriteria();
        $criteria->condition = 'settings = :settings';
        $criteria->params = array(
            ':settings' => JsonHelper::encode($settings),
        );

        // Check if we have a map already
        $mapRecord = $this->findMap($criteria);

        if (!count($mapRecord) || $mapRecord->settings != $settings) {

            // Save settings and map to database
            $mapRecord = $this->getNewMap();
            $mapRecord->settings = $settings;
        }

        // Save new map to db
        $mapRecord->map = $map;
        $mapRecord->save(false);
    }

    /**
     * @codeCoverageIgnore
     *
     * @param \CDbCriteria $criteria
     *
     * @return Export_MapRecord|array|null
     */
    public function findMap(\CDbCriteria $criteria)
    {
        return Export_MapRecord::model()->find($criteria);
    }

    /**
     * @codeCoverageIgnore
     *
     * @return Export_MapRecord
     */
    protected function getNewMap()
    {
        return new Export_MapRecord();
    }

    /**
     * Download the export csv.
     *
     * @param array $settings
     *
     * @return string
     *
     * @throws Exception
     */
    public function download(array $settings)
    {
        // Get max power
        craft()->config->maxPowerCaptain();

        // Check what service we're gonna need
        if (!($this->_service = $this->getService($settings['type']))) {
            throw new Exception(Craft::t('Unknown Element Type Service called.'));
        }

        // Get delimiter
        $delimiter = craft()->plugins->callFirst('registerExportCsvDelimiter');
        $delimiter = is_null($delimiter) ? ',' : $delimiter;

        // Open output buffer
        ob_start();

        // Write to output stream
        $export = fopen('php://output', 'w');

        // Get data
        $data = $this->getData($settings);

        // If there is data, process
        if (is_array($data) && count($data)) {

            // Put down columns
            fputcsv($export, $this->parseColumns($settings), $delimiter);

            // Loop through data
            foreach ($data as $element) {

                // Fetch element in case of element id
                if (is_numeric($element)) {
                    $element = craft()->elements->getElementById($element, $settings['type']);
                }

                // Get fields
                $fields = $this->parseFields($settings, $element);

                // Gather row data
                $rows = array();

                // Loop trough the fields
                foreach ($fields as $handle => $data) {

                    // Parse element data
                    $data = $this->parseElementData($handle, $data);

                    // Parse field data
                    $data = $this->parseFieldData($handle, $data);

                    // Encode and add to rows
                    $rows[] = StringHelper::convertToUTF8($data);
                }

                // Add rows to export
                fputcsv($export, $rows, $delimiter);
            }
        }

        // Close buffer and return data
        fclose($export);
        $data = ob_get_clean();

        // Use windows friendly newlines
        $data = str_replace("\n", "\r\n", $data);

        // Return the data to controller
        return $data;
    }

    /**
     * Get service to use for exporting.
     *
     * @param string $elementType
     *
     * @return object|bool
     */
    public function getService($elementType)
    {
        // Check if there's a service for this element type elsewhere
        $service = craft()->plugins->callFirst('registerExportService', array(
            'elementType' => $elementType,
        ));

        // If not, do internal check
        if ($service == null) {

            // Get from right elementType
            $service = 'export_'.strtolower($elementType);
        }

        // Check if elementtype can be imported
        if (isset(craft()->$service) && craft()->$service instanceof IExportElementType) {

            // Return this service
            return craft()->$service;
        }

        return false;
    }

    /**
     * Get path to fieldtype's custom <tr> template.
     *
     * @param string $fieldHandle
     *
     * @return string|bool
     */
    public function getCustomTableRow($fieldHandle)
    {
        // If table row paths haven't been loaded
        if (!$this->_loadedTableRowPaths) {

            // Call hook for all plugins
            $responses = craft()->plugins->call('registerExportTableRowPaths');

            // Loop through responses from each plugin
            foreach ($responses as $customPaths) {

                // Append custom paths to master list
                $this->customTableRowPaths = array_merge($this->customTableRowPaths, $customPaths);
            }

            // Table row paths have been loaded
            $this->_loadedTableRowPaths = true;
        }

        // If fieldtype has been registered and is not falsey
        if (array_key_exists($fieldHandle, $this->customTableRowPaths) && $this->customTableRowPaths[$fieldHandle]) {

            // Return specified custom path
            return $this->customTableRowPaths[$fieldHandle];
        }

        return false;
    }

    /**
     * Get data from sources.
     *
     * @param array $settings
     *
     * @return array
     */
    protected function getData(array $settings)
    {
        // Get other sources
        $sources = craft()->plugins->call('registerExportSource', array($settings));

        // Loop through sources, see if we can get any data
        $data = array();
        foreach ($sources as $plugin) {
            if (is_array($plugin)) {
                foreach ($plugin as $source) {
                    $data[] = $source;
                }
            }
        }

        // Cut up data from source
        if (array_key_exists('offset', $settings) && is_numeric($settings['offset'])) {
            $data = array_slice($data, $settings['offset'], $settings['limit']);
        }

        // If no data from source, get data by ourselves
        if (!count($data)) {

            // Find data
            $criteria = $this->_service->setCriteria($settings);

            // Gather element ids
            $data = $criteria->ids();
        }

        return $data;
    }

    /**
     * Parse fields.
     *
     * @param array $settings
     * @param mixed $element
     *
     * @return array
     */
    protected function parseFields(array $settings, $element)
    {
        $fields = array();

        // Only get element attributes and content attributes
        $attributes = $element;
        if ($element instanceof BaseElementModel) {

            // Get service
            $attributes = $this->_service->getAttributes($settings['map'], $element);
        }

        // Loop through the map
        foreach ($settings['map'] as $handle => $data) {

            // Only get checked fields
            if ($data['checked'] == '1' && (array_key_exists($handle, $attributes) || array_key_exists(substr($handle, 0, 5), $attributes))) {

                // Fill them with data
                $fields[$handle] = $attributes[$handle];
            }
        }

        return $fields;
    }

    /**
     * Parse column names.
     *
     * @param array $settings
     *
     * @return string
     */
    protected function parseColumns(array $settings)
    {
        $columns = array();

        // Loop through map
        foreach ($settings['map'] as $handle => $data) {

            // If checked
            if ($data['checked'] == 1) {

                // Add column
                $columns[] = StringHelper::convertToUTF8($data['label']);
            }
        }

        return $columns;
    }

    /**
     * Parse reserved element values.
     *
     * @param string $handle
     * @param string $data
     *
     * @return string
     */
    protected function parseElementData($handle, $data)
    {
        switch ($handle) {

            // Get username of author
            case ExportModel::HandleAuthor:
                return craft()->users->getUserById($data)->username;

            // Make data human readable
            case ExportModel::HandleEnabled:
                return $data == '0' ? Craft::t('No') : Craft::t('Yes');

            case ExportModel::HandlePostDate:
            case ExportModel::HandleExpiryDate:
                return (string) $data;

        }

        return $data;
    }

    /**
     * Parse field values.
     *
     * @param string $handle
     * @param mixed  $data
     *
     * @return string
     */
    public function parseFieldData($handle, $data)
    {

        // Do we have any data at all
        if (!is_null($data)) {

            // Get field info
            $field = craft()->fields->getFieldByHandle($handle);

            // If it's a field ofcourse
            if (!is_null($field)) {

                // For some fieldtypes the're special rules
                switch ($field->type) {

                    case ExportModel::FieldTypeEntries:
                    case ExportModel::FieldTypeCategories:
                    case ExportModel::FieldTypeAssets:
                    case ExportModel::FieldTypeUsers:
                    case ExportModel::FieldTypeTags:
                        // Show names
                        $data = $data instanceof ElementCriteriaModel ? implode(', ', $data->find()) : $data;
                        break;

                    case ExportModel::FieldTypeLightswitch:
                        // Make data human readable
                        switch ($data) {

                            case '0':
                                $data = Craft::t('No');
                                break;

                            case '1':
                                $data = Craft::t('Yes');
                                break;

                        }
                        break;

                    case ExportModel::FieldTypeTable:
                        // Parse table checkboxes
                        $table = array();
                        if (is_array($data)) {
                            foreach ($data as $row) {

                                // Keep track of column #
                                $i = 1;

                                // Loop through columns
                                foreach ($row as $column => $value) {

                                    // Get column
                                    $column = isset($field->settings['columns'][$column]) ? $field->settings['columns'][$column] : (isset($field->settings['columns']['col'.$i]) ? $field->settings['columns']['col'.$i] : array('type' => 'dummy'));

                                    // Keep track of column #
                                    ++$i;

                                    // Parse
                                    $table[] = $column['type'] == 'checkbox' ? ($value == 1 ? Craft::t('Yes') : Craft::t('No')) : $value;
                                }
                            }
                        }

                        // Return parsed data as array
                        $data = $table;
                        break;

                    case ExportModel::FieldTypeRichText:
                    case ExportModel::FieldTypeDate:
                    case ExportModel::FieldTypeRadioButtons:
                    case ExportModel::FieldTypeDropdown:
                        // Resolve to string
                        $data = (string) $data;
                        break;

                    case ExportModel::FieldTypeCheckboxes:
                    case ExportModel::FieldTypeMultiSelect:
                        // Parse multi select values
                        $multi = array();
                        if (is_array($data)) {
                            foreach ($data as $row) {
                                $multi[] = $row->value;
                            }
                        }

                        // Return parsed data as array
                        $data = $multi;
                        break;

                }
            }

            // Get other operations
            craft()->plugins->call('registerExportOperation', array(&$data, $handle));
        } else {

            // Don't return null, return empty
            $data = '';
        }

        // If it's an object or an array, make it a string
        if (is_array($data)) {
            $data = StringHelper::arrayToString(ArrayHelper::filterEmptyStringsFromArray(ArrayHelper::flattenArray($data)), ', ');
        }

        // If it's an object, make it a string
        if (is_object($data)) {
            $data = StringHelper::arrayToString(ArrayHelper::filterEmptyStringsFromArray(ArrayHelper::flattenArray(get_object_vars($data))), ', ');
        }

        return $data;
    }
}
