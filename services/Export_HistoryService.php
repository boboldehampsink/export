<?php

namespace Craft;

/**
 * Export History Service.
 *
 * Contains logic for showing export history.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */
class Export_HistoryService extends BaseApplicationComponent
{
    /**
     * Show all history.
     *
     * @return array
     */
    public function show()
    {
        // Set criteria
        $criteria = new \CDbCriteria();
        $criteria->order = 'id desc';

        return Export_HistoryRecord::model()->findAll($criteria);
    }

    /**
     * Start history.
     *
     * @param array|object $settings
     *
     * @return int
     */
    public function start($settings)
    {
        $history              = new Export_HistoryRecord();
        $history->userId      = craft()->userSession->getUser()->id;
        $history->type        = $settings['type'];
        $history->file        = basename($settings['file']);
        $history->rows        = $settings['rows'];
        $history->status      = ExportModel::StatusStarted;

        $history->save(false);

        return $history->id;
    }

    /**
     * Stop history.
     *
     * @param int    $history
     * @param string $status
     */
    public function end($history, $status)
    {
        $history = Export_HistoryRecord::model()->findById($history);
        $history->status = $status;

        $history->save(false);
    }
}
