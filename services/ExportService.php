<?php

namespace Craft;

/**
 * Export service.
 *
 * Handles common export logics.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */
class ExportService extends BaseApplicationComponent
{
    /**
     * Contains the working export service's name.
     *
     * @var string
     */
    private $_service;

    /**
     * Contains the default delimiter
     * TODO: Make this configurable.
     *
     * @var string
     */
    public $delimiter = ExportModel::DelimiterComma;

    /**
     * Saves an export map to the database.
     *
     * @param array $settings
     * @param array $map
     */
    public function saveMap(array $settings, array $map)
    {
        // Set criteria
        $criteria = new \CDbCriteria();
        $criteria->condition = 'settings = :settings';
        $criteria->params = array(
            ':settings' => JsonHelper::encode($settings),
        );

        // Check if we have a map already
        $mapRecord = Export_MapRecord::model()->find($criteria);

        if (!count($mapRecord) || $mapRecord->settings != $settings) {

            // Save settings and map to database
            $mapRecord           = new Export_MapRecord();
            $mapRecord->settings = $settings;
        }

        // Save new map to db
        $mapRecord->map = $map;
        $mapRecord->save(false);
    }

    /**
     * Download the export csv.
     *
     * @param array $settings
     *
     * @return string
     */
    public function download(array $settings)
    {
        // Get max power
        craft()->config->maxPowerCaptain();

        // Check what service we're gonna need
        $this->_service = 'export_'.strtolower($settings['type']);

        // Create the export template
        $export = '';

        // Get data
        $data = $this->getData($settings);

        // If there is data, process
        if (count($data)) {

            // Count rows
            $rows = 0;

            // Loop trough data
            foreach ($data as $element) {
                $row = '';

                // Get fields
                $fields = $this->parseFields($settings, $element);

                // Put down columns
                if (!$rows) {
                    $row .= $this->parseColumns($settings);
                }

                // Loop trough the fields
                foreach ($fields as $handle => $data) {

                    // Parse element data
                    $data = $this->parseElementData($handle, $data);

                    // Parse field data
                    $data = $this->parseFieldData($handle, $data);

                    // Put in quotes and escape
                    $row .= '"'.addcslashes($data, '"').'"'.$this->delimiter;
                }

                // Remove last comma
                $row = substr($row, 0, -1);

                // Encode row
                $row = StringHelper::convertToUTF8($row);

                // And start a new line
                $row = $row."\r\n";

                // Append to data
                $export .= $row;

                // Count rows
                $rows++;
            }
        }

        // Return the data to controller
        return $export;
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

        // If no data from source, get data by ourselves
        if (!count($data)) {

            // Find data
            $service = $this->_service;
            $criteria = craft()->$service->setCriteria($settings);

            // Gather data
            $data = $criteria->find();
        }

        return $data;
    }

    /**
     * Parse fields.
     *
     * @param array $settings
     * @param       $element
     *
     * @return array
     */
    protected function parseFields(array $settings, $element)
    {
        $fields = array();

        // Only get element attributes and content attributes
        if ($element instanceof BaseElementModel) {

            // Get service
            $service = $this->_service;
            $attributes = craft()->$service->getAttributes($settings['map'], $element);
        } else {

            // No element, i.e. from export source
            $attributes = $element;
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
     * @param array $settings [description]
     *
     * @return string
     */
    protected function parseColumns(array $settings)
    {
        $columns = '';

        // Loop trough map
        foreach ($settings['map'] as $handle => $data) {

            // If checked
            if ($data['checked'] == 1) {

                // Add column
                $columns .= '"'.addcslashes($data['label'], '"').'"'.$this->delimiter;
            }
        }

        // Remove last comma
        $columns = substr($columns, 0, -1);

        // Encode columns
        $columns = StringHelper::convertToUTF8($columns);

        // And start a new line
        $columns = $columns."\r\n";

        return $columns;
    }

    /**
     * Parse reserved element values.
     *
     * @param  $handle
     * @param  $data
     *
     * @return string
     */
    protected function parseElementData($handle, $data)
    {
        switch ($handle) {

            case ExportModel::HandleAuthor:

                // Get username of author
                $data = craft()->users->getUserById($data)->username;

                break;

            case ExportModel::HandleEnabled:

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
    protected function parseFieldData($handle, $data)
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
                        foreach ($data as $row) {
                            foreach ($row as $column => $value) {
                                $table[] = $field->settings['columns'][$column]['type'] == 'checkbox' ? ($value == 1 ? Craft::t('Yes') : Craft::t('No')) : $value;
                            }
                        }

                        // Return parsed data as array
                        $data = $table;

                        break;

                    case ExportModel::FieldTypeRichText:

                        // Resolve to string
                        $data = (string) $data;

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
