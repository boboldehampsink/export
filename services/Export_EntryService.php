<?php

namespace Craft;

/**
 * Export Entry Service.
 *
 * Handles exporting entries.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */
class Export_EntryService extends BaseApplicationComponent implements IExportElementType
{
    /**
     * Return export template.
     *
     * @return string
     */
    public function getTemplate()
    {
        return 'export/sources/_entry';
    }

    /**
     * Get entry sections.
     *
     * @return array|bool
     */
    public function getGroups()
    {
        // Get editable sections for user
        $editable = craft()->sections->getEditableSections();

        // Get sections but not singles
        $sections = array();
        foreach ($editable as $section) {
            if ($section->type != SectionType::Single) {
                $sections[] = $section;
            }
        }

        return $sections;
    }

    /**
     * Return entry fields.
     *
     * @param array $settings
     * @param bool  $reset
     *
     * @return array
     */
    public function getFields(array $settings, $reset)
    {
        // Set criteria
        $criteria = new \CDbCriteria();
        $criteria->condition = 'settings = :settings';
        $criteria->params = array(
            ':settings' => JsonHelper::encode($settings),
        );

        // Check if we have a map already
        $stored = Export_MapRecord::model()->find($criteria);

        if (!count($stored) || $reset) {

            // Get section id
            $section = $settings['elementvars']['section'];

            // Get entrytype id(s)
            $entrytype = $settings['elementvars']['entrytype'];

            // If "All"
            if (empty($entrytype)) {

                // Get entrytype models
                $entrytypes = craft()->sections->getEntryTypesBySectionId($section);
            } else {

                // Get entrytype model
                $entrytypes = array(craft()->sections->getEntryTypeById($entrytype));
            }

            // Create a nice field map
            $fields = array();

            // With multiple or one entry type
            foreach ($entrytypes as $entrytype) {

                // Set the static fields for this type
                $layout = array(
                    ExportModel::HandleId         => array('name' => Craft::t('ID'), 'checked' => 0),
                    ExportModel::HandleTitle.'_'.$entrytype->id => array('name' => $entrytype->hasTitleField ? $entrytype->titleLabel : Craft::t('Title'), 'checked' => 1, 'entrytype' => $entrytype->id),
                    ExportModel::HandleSlug       => array('name' => Craft::t('Slug'), 'checked' => 0),
                    ExportModel::HandleParent     => array('name' => Craft::t('Parent'), 'checked' => 0),
                    ExportModel::HandleAncestors  => array('name' => Craft::t('Ancestors'), 'checked' => 0),
                    ExportModel::HandleAuthor     => array('name' => Craft::t('Author'), 'checked' => 0),
                    ExportModel::HandlePostDate   => array('name' => Craft::t('Post Date'), 'checked' => 0),
                    ExportModel::HandleExpiryDate => array('name' => Craft::t('Expiry Date'), 'checked' => 0),
                    ExportModel::HandleEnabled    => array('name' => Craft::t('Enabled'), 'checked' => 0),
                    ExportModel::HandleStatus     => array('name' => Craft::t('Status'), 'checked' => 0),
                );

                // Set the dynamic fields for this type
                $tabs = craft()->fields->getLayoutById($entrytype->fieldLayoutId)->getTabs();
                foreach ($tabs as $tab) {
                    $fieldData = array();
                    foreach ($tab->getFields() as $field) {
                        $data = $field->getField();
                        $fieldData[$data->handle] = array('name' => $data->name, 'checked' => 1);
                    }
                    $layout += $fieldData;
                }

                // Set the static fields also
                $fields += $layout;
            }
        } else {

            // Get the stored map
            $fields = $stored->map;
        }

        // Return fields
        return $fields;
    }

    /**
     * Set entry criteria.
     *
     * @param array $settings
     *
     * @return ElementCriteriaModel
     */
    public function setCriteria(array $settings)
    {

        // Get entries by criteria
        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->order = 'id '.$settings['sort'];
        $criteria->offset = $settings['offset'];
        $criteria->limit = $settings['limit'];
        $criteria->status = isset($settings['map']['status']) ? $settings['map']['status'] : null;

        // Get by section and entrytype
        $criteria->sectionId = $settings['elementvars']['section'];
        $criteria->type      = $settings['elementvars']['entrytype'];

        return $criteria;
    }

    /**
     * Get entry attributes.
     *
     * @param array            $map
     * @param BaseElementModel $element
     *
     * @return array
     */
    public function getAttributes(array $map, BaseElementModel $element)
    {
        $attributes = array();

        // Try to parse checked fields through prepValue
        foreach ($map as $handle => $data) {
            if ($data['checked'] && !strstr($handle, ExportModel::HandleTitle)) {
                $attributes[$handle] = $element->$handle;
            }
        }

        // Title placeholder for all element types
        foreach (craft()->sections->getEntryTypesBySectionId($element->sectionId) as $entrytype) {

            // Set title
            $attributes[ExportModel::HandleTitle.'_'.$entrytype->id] = $entrytype->id == $element->typeId ? $element->{ExportModel::HandleTitle} : '';
        }

        // Get parent for structures
        if (array_key_exists(ExportModel::HandleParent, $map)) {
            if ($element->getAncestors()) {
                $attributes[ExportModel::HandleParent] = $element->getAncestors(1)->first();
            }
        }

        // Get ancestors for structures
        if (array_key_exists(ExportModel::HandleAncestors, $map)) {
            if ($element->getAncestors()) {
                $attributes[ExportModel::HandleAncestors] = implode('/', $element->getAncestors()->find());
            }
        }

        return $attributes;
    }
}
