<?php

namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@nerds.company>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   MIT
 *
 * @link http://github.com/boboldehampsink
 */
class m140924_111621_export_CreateExportMap extends BaseMigration
{
    /**
     * Any migration code in here is wrapped inside of a transaction.
     *
     * @return bool
     */
    public function safeUp()
    {
        // Create the craft_export_map table
        craft()->db->createCommand()->createTable('export_map', array(
            'settings' => array('column' => 'text'),
            'map' => array('column' => 'text'),
        ), null, true);

        return true;
    }
}
