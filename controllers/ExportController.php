<?php

namespace Craft;

/**
 * Export controller.
 *
 * Handles mapping and export requests.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */
class ExportController extends BaseController
{
    /**
     * Get available entry types for section.
     *
     * @return string JSON
     */
    public function actionGetEntryTypes()
    {
        // Only ajax post requests
        $this->requirePostRequest();
        $this->requireAjaxRequest();

        // Get section
        $section = craft()->request->getPost('section');
        $section = craft()->sections->getSectionById($section);

        // Get entry types
        $entrytypes = $section->getEntryTypes();

        // Return JSON
        $this->returnJson($entrytypes);
    }

    /**
     * Process input for mapping.
     *
     * @return string HTML
     */
    public function actionMap()
    {

        // Get export posts
        $export = craft()->request->getRequiredPost('export');
        $reset = craft()->request->getPost('reset');

        // Send variables to template and display
        $this->renderTemplate('export/_map', array(
            'export' => $export,
            'reset' => $reset,
        ));
    }

    /**
     * Start export task.
     */
    public function actionExport()
    {
        // Get import post
        $settings = craft()->request->getRequiredPost('export');

        // Get mapping fields
        $map = craft()->request->getParam('fields');

        // Save map
        craft()->export->saveMap($settings, $map);

        // Set filename and rows
        $settings['file'] = 'export_'.strtolower($settings['type']).'_'.date('Ymd').'.csv';
        $settings['rows'] = 0;

        // Create history
        $history = craft()->export_history->start($settings);

        // Add history to settings
        $settings['history'] = $history;

        // UNCOMMENT FOR DEBUGGING
        //craft()->export->debug($settings, $history, 1);

        // Create the import task
        $task = craft()->tasks->createTask('Export', Craft::t('Exporting').' '.strtolower($settings['type']), $settings);

        // Notify user
        craft()->userSession->setNotice(Craft::t('Export process started.'));

        // Redirect to history
        $this->redirect('export/history?task='.$task->id);
    }
}
