<?php

namespace Craft;

/**
 * Export User Service.
 *
 * Handles export users.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */
class Export_UserService extends BaseApplicationComponent implements IExportElementType
{
    /**
     * Return export template.
     *
     * @return string
     */
    public function getTemplate()
    {
        return 'export/sources/_user';
    }

    /**
     * Get user groups.
     *
     * @return array|bool
     */
    public function getGroups()
    {
        // Check if usergroups are allowed in this installation
        if (isset(craft()->userGroups)) {

            // Get usergroups
            $groups = craft()->userGroups->getAllGroups();

            // Return when groups found
            if (count($groups)) {
                return $groups;
            }

            // Still return true when no groups found
            return true;
        }

        // Else, dont proceed with the user element
        return false;
    }

    /**
     * Return user fields.
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
                ExportModel::HandleId                   => array('name' => Craft::t('ID'), 'checked' => 0),
                ExportModel::HandleUsername             => array('name' => Craft::t('Username'), 'checked' => 1),
                ExportModel::HandleFirstName            => array('name' => Craft::t('First Name'), 'checked' => 1),
                ExportModel::HandleLastName             => array('name' => Craft::t('Last Name'), 'checked' => 1),
                ExportModel::HandleEmail                => array('name' => Craft::t('Email'), 'checked' => 1),
                ExportModel::HandlePreferredLocale      => array('name' => Craft::t('Preferred Locale'), 'checked' => 0),
                ExportModel::HandleWeekStartDay         => array('name' => Craft::t('Week Start Day'), 'checked' => 0),
                ExportModel::HandleStatus               => array('name' => Craft::t('Status'), 'checked' => 0),
                ExportModel::HandleLastLoginDate        => array('name' => Craft::t('Last Login Date'), 'checked' => 0),
                ExportModel::HandleInvalidLoginCount    => array('name' => Craft::t('Invalid Login Count'), 'checked' => 0),
                ExportModel::HandleLastInvalidLoginDate => array('name' => Craft::t('Last Invalid Login Date'), 'checked' => 0),
            );

            // Set the dynamic fields for this type
            foreach (craft()->fields->getLayoutByType(ElementType::User)->getFields() as $field) {
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
     * Set user criteria.
     *
     * @param array $settings
     *
     * @return ElementCriteriaModel
     */
    public function setCriteria(array $settings)
    {
        // Get users by criteria
        $criteria = craft()->elements->getCriteria(ElementType::User);
        $criteria->order = 'id '.$settings['sort'];
        $criteria->offset = $settings['offset'];
        $criteria->limit = $settings['limit'];
        $criteria->status = isset($settings['map']['status']) ? $settings['map']['status'] : null;

        // Get by group
        $criteria->groupId = $settings['elementvars']['groups'];

        return $criteria;
    }

    /**
     * Get user attributes.
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

        return $attributes;
    }
}
