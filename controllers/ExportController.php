<?php

namespace Craft;

/**
 * Export controller.
 *
 * Handles mapping and export requests.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@nerds.company>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   MIT
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

<<<<<<< HEAD
        // Send variables to template and display
        $this->renderTemplate('export/_map', array(
            'export' => $export,
            'reset' => $reset,
        ));
=======
        $deliveryOption = craft()->request->getPost('deliveryOption');
        $emailRecipients = craft()->request->getPost('emailRecipients');

        // Send variables to template and display
        $this->renderTemplate(
            'export/_map',
            array(
                'export' => $export,
                'reset' => $reset,
                'deliveryOption' => $deliveryOption,
                'emailRecipients' => $emailRecipients,
            )
        );
>>>>>>> 7543ef1... Able to select options on whether or not you want to export and email the result later or download the responsr
    }

    /**
     * Download export.
     *
     * @return string CSV
     */
    public function actionDownload()
    {
<<<<<<< HEAD

=======
>>>>>>> 7543ef1... Able to select options on whether or not you want to export and email the result later or download the responsr
        // Get export post
        $settings = craft()->request->getRequiredPost('export');

        // Get mapping fields
        $map = craft()->request->getParam('fields');

<<<<<<< HEAD
=======
        $deliveryOption = craft()->request->getRequiredPost('deliveryOption');


        $emailRecipients = craft()->request->getPost('emailRecipients');

>>>>>>> 7543ef1... Able to select options on whether or not you want to export and email the result later or download the responsr
        // Save map
        craft()->export->saveMap($settings, $map);

        // Set more settings
        $settings['map'] = $map;

        // Get data
<<<<<<< HEAD
        $data = craft()->export->download($settings);

        // Download the csv
        craft()->request->sendFile('export.csv', $data, array('forceDownload' => true, 'mimeType' => 'text/csv'));
=======


        if ($deliveryOption === 'download') {
            // Download the csv
            $data = craft()->export->download($settings);
            craft()->request->sendFile('export.csv', $data, array('forceDownload' => true, 'mimeType' => 'text/csv'));

            return;
        }
        // start a task and notify upon completion
        craft()->tasks->createTask(
            'Export',
            null,
            array(
                'dataSettings' => $settings,
                'emails' => $emailRecipients,
            )
        );
>>>>>>> 7543ef1... Able to select options on whether or not you want to export and email the result later or download the responsr
    }
}
