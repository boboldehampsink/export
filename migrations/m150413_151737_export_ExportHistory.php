<?php

namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName.
 */
class m150413_151737_export_ExportHistory extends BaseMigration
{
    /**
     * Any migration code in here is wrapped inside of a transaction.
     *
     * @return bool
     */
    public function safeUp()
    {
        // Create the craft_export_history table
        craft()->db->createCommand()->createTable('export_history', array(
            'userId' => array('column' => 'integer', 'required' => false),
            'type'   => array(),
            'file'   => array('maxLength' => 255, 'column' => 'varchar'),
            'rows'   => array('maxLength' => 11, 'decimals' => 0, 'unsigned' => false, 'length' => 10, 'column' => 'integer'),
            'status' => array('values' => array('started', 'finished'), 'column' => 'enum'),
        ), null, true);

        // Add foreign keys to craft_export_history
        craft()->db->createCommand()->addForeignKey('export_history', 'userId', 'users', 'id', 'CASCADE', null);

        return true;
    }
}
