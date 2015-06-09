<?php

namespace Craft;

/**
 * Export variable.
 *
 * Acts as a bridge between services and templates.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */
class ExportVariable
{
    /**
     * Return "groups" (section, groups, etc.).
     *
     * @param string $elementType
     *
     * @return array|bool
     */
    public function getGroups($elementType)
    {
        // Check if elementtype can be imported
        if ($service = craft()->export->getService($elementType)) {

            // Return "groups" (section, groups, etc.)
            return $service->getGroups();
        }

        return false;
    }

    /**
     * Get template for service.
     *
     * @param string $elementType
     *
     * @return array|bool
     */
    public function getTemplate($elementType)
    {
        // Check if elementtype can be imported
        if ($service = craft()->export->getService($elementType)) {

            // Return template
            return $service->getTemplate();
        }

        return false;
    }

    /**
     * Get fields of elementType.
     *
     * @param string $elementType
     * @param bool   $reset
     *
     * @return array
     */
    public function getFields($elementType, $reset)
    {
        // Get export vars
        $export = craft()->request->getParam('export');
        
        // Unset non-map settings
        unset($export['limit'], $export['offset']);
        ksort($export);

        // Check if elementtype can be imported
        if ($service = craft()->export->getService($elementType)) {

            // Return fields of elementType
            return $service->getFields($export, $reset);
        }

        return false;
    }
}
