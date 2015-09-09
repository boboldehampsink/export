<?php

namespace Craft;

/**
 * Export Plugin.
 *
 * Plugin that allows you to export data to CSV files.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */
class ExportPlugin extends BasePlugin
{
    /**
     * Get plugin name.
     *
     * @return string
     */
    public function getName()
    {
        return Craft::t('Export');
    }

    /**
     * Get plugin version.
     *
     * @return string
     */
    public function getVersion()
    {
        return '0.5.7';
    }

    /**
     * Get plugin developer.
     *
     * @return string
     */
    public function getDeveloper()
    {
        return 'Bob Olde Hampsink';
    }

    /**
     * Get plugin developer url.
     *
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'http://www.itmundi.nl';
    }

    /**
     * This plugin has a Control Panel section.
     *
     * @return bool
     */
    public function hasCpSection()
    {
        return true;
    }

    /**
     * Register user permissions.
     *
     * @return array
     */
    public function registerUserPermissions()
    {
        return array(
            'reset' => array('label' => Craft::t('Reset export map')),
        );
    }

    /**
     * Run on plugin initialisation.
     */
    public function init()
    {
        // Import Export Element Type Interface
        Craft::import('plugins.export.services.IExportElementType');
    }
}
