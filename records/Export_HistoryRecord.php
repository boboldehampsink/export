<?php

namespace Craft;

/**
 * Export History Record.
 *
 * Represents the export_history table
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */
class Export_HistoryRecord extends BaseRecord
{
    /**
     * Return table name.
     *
     * @return string
     */
    public function getTableName()
    {
        return 'export_history';
    }

    /**
     * Return table attributes.
     *
     * @return array
     */
    protected function defineAttributes()
    {
        return array(
            'type'     => AttributeType::String,
            'file'     => AttributeType::Name,
            'rows'     => AttributeType::Number,
            'status'   => array(AttributeType::Enum, 'values' => array(ExportModel::StatusStarted, ExportModel::StatusFinished)),
        );
    }

    /**
     * Return table relations.
     *
     * @return array
     */
    public function defineRelations()
    {
        return array(
            'user' => array(static::BELONGS_TO, 'UserRecord', 'onDelete' => static::CASCADE, 'required' => false),
        );
    }
}
