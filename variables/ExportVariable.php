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
        // Get from right elementType
        $service = 'export_'.strtolower($elementType);

        // Check if elementtype can be imported
        if (isset(craft()->$service)) {

            // Return "groups" (section, groups, etc.)
            return craft()->$service->getGroups();
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
        // Get from right elementType
        $service = 'export_'.strtolower($elementType);

        // Get export vars
        $export = craft()->request->getParam('export');

        // Return fields of elementType
        return craft()->$service->getFields($export, $reset);
    }
}
