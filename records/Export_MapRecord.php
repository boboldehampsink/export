<?php

namespace Craft;

/**
 * Export Map Record.
 *
 * Represents the export_map database table
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */
class Export_MapRecord extends BaseRecord
{
    /**
     * Get table name.
     *
     * @return string
     */
    public function getTableName()
    {
        return 'export_map';
    }

    /**
     * Define table attributes.
     *
     * @return array
     */
    protected function defineAttributes()
    {
        return array(
            'settings' => AttributeType::Mixed,
            'map'      => AttributeType::Mixed,
        );
    }
}
