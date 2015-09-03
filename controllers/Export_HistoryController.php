<?php

namespace Craft;

/**
 * Export History Controller.
 *
 * Request actions for Export History
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */
class Export_HistoryController extends BaseController
{
    /**
     * Downloads an export file.
     *
     * @throws HttpException If not found
     */
    public function actionDownload()
    {
        // Get history id
        $history = craft()->request->getParam('id');

        // Get history
        $model = Export_HistoryRecord::model()->findById($history);

        // Get filepath
        $path = craft()->path->getStoragePath().'export/'.$history.'/'.$model->file;

        // Check if file exists
        if (file_exists($path)) {

            // Send the file to the browser
            craft()->request->sendFile($model->file, IOHelper::getFileContents($path), array('forceDownload' => true));
        }

        // Not found, = 404
        throw new HttpException(404);
    }

    /**
     * Deletes an export from the history.
     */
    public function actionDelete()
    {
        // Get history id
        $history = craft()->request->getParam('id');

        // Get history
        $model = Export_HistoryRecord::model()->findById($history);

        // Notify user
        craft()->userSession->setNotice(Craft::t('The export history of {file} has been deleted.', array(
            'file' => $model->file,
        )));

        // Set criteria
        $criteria = new \CDbCriteria();
        $criteria->condition = 'historyId = :history_id';
        $criteria->params = array(
            ':history_id' => $history,
        );

        // Delete history
        $model->delete();

        // Redirect to history
        $this->redirect('export/history');
    }
}
