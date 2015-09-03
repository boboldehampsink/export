<?php

namespace Craft;

/**
 * Export Task.
 *
 * Contains logic for exporting
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */
class ExportTask extends BaseTask
{
    /**
     * Define settings.
     *
     * @return array
     */
    protected function defineSettings()
    {
        return array(
            'file'        => AttributeType::Name,
            'rows'        => AttributeType::Number,
            'map'         => AttributeType::Mixed,
            'type'        => AttributeType::String,
            'elementvars' => array(AttributeType::Mixed, 'default' => array()),
            'history'     => AttributeType::Number,
        );
    }

    /**
     * Return description.
     *
     * @return string
     */
    public function getDescription()
    {
        return Craft::t('Export');
    }

    /**
     * Return total steps.
     *
     * @return int
     */
    public function getTotalSteps()
    {
        // Get settings
        $settings = $this->getSettings();

        // Delete element template caches before importing
        craft()->templateCache->deleteCachesByElementType($settings->type);

        // Take a step for every row
        return $settings->rows;
    }

    /**
     * Run step.
     *
     * @param int $step
     *
     * @return bool
     */
    public function runStep($step)
    {
        // Get settings
        $settings = $this->getSettings();

        // Get data
        $data = craft()->export->data($settings->file);

        // On start
        if (!$step) {

            // Fire an "onExportStart" event
            $event = new Event($this, array('settings' => $settings));
            craft()->export->onExportStart($event);
        }

        // Check if row exists
        if (isset($data[$step])) {

            // Export row
            craft()->export->row($step, $data[$step], $settings);
        }

        // When finished
        if ($step == ($settings->rows - 1)) {

            // Finish
            craft()->export->finish($settings);

            // Fire an "onExportFinish" event
            $event = new Event($this, array('settings' => $settings));
            craft()->export->onExportFinish($event);
        }

        return true;
    }
}
