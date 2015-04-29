<?php

namespace Craft;

/**
 * Export Category Service.
 *
 * Handles exporting categories
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */
class Export_CategoryService extends BaseApplicationComponent implements IExportElementType
{
    /**
     * Return export template.
     *
     * @return string
     */
    public function getTemplate()
    {
        return 'export/sources/_category';
    }

    /**
     * Get category groups.
     *
     * @return array|bool
     */
    public function getGroups()
    {
        // Return editable groups for user
        return craft()->categories->getEditableGroups();
    }

    /**
     * Return category fields.
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

            // Set the static fields for this type
            $fields = array(
                ExportModel::HandleId        => array('name' => Craft::t('ID'), 'checked' => 0),
                ExportModel::HandleTitle     => array('name' => Craft::t('Title'), 'checked' => 1),
                ExportModel::HandleSlug      => array('name' => Craft::t('Slug'), 'checked' => 0),
                ExportModel::HandleParent    => array('name' => Craft::t('Parent'), 'checked' => 0),
                ExportModel::HandleAncestors => array('name' => Craft::t('Ancestors'), 'checked' => 0),
            );

            // Set the dynamic fields for this type
            foreach (craft()->fields->getLayoutByType(ElementType::Category)->getFields() as $field) {
                $data = $field->getField();
                $fields[$data->handle] = array('name' => $data->name, 'checked' => 1);
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
        // Match with current data
        $criteria = craft()->elements->getCriteria(ElementType::Category);
        $criteria->order = 'id '.$settings['sort'];
        $criteria->offset = $settings['offset'];
        $criteria->limit = $settings['limit'];
        $criteria->status = isset($settings['map']['status']) ? $settings['map']['status'] : null;

        // Look in same group when replacing
        $criteria->groupId = $settings['elementvars']['group'];

        return $criteria;
    }

    /**
     * Get category attributes.
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
            if ($data['checked']) {
                $attributes[$handle] = $element->$handle;
            }
        }

        // Get parent for categories
        if (array_key_exists(ExportModel::HandleParent, $map)) {
            if ($element->getAncestors()) {
                $attributes[ExportModel::HandleParent] = $element->getAncestors(1)->first();
            }
        }

        // Get ancestors for categories
        if (array_key_exists(ExportModel::HandleAncestors, $map)) {
            if ($element->getAncestors()) {
                $attributes[ExportModel::HandleAncestors] = implode('/', $element->getAncestors()->find());
            }
        }

        return $attributes;
    }
}
