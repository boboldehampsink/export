<?php

namespace Craft;

/**
 * Export Element Interface.
 *
 * Determines Element Type export rules.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */
interface IExportElementType
{
    /**
     * Return export template.
     *
     * @return string
     */
    public function getTemplate();

    /**
     * Get element groups.
     */
    public function getGroups();

    /**
     * Return element fields.
     *
     * @param array $settings
     * @param bool  $reset
     */
    public function getFields(array $settings, $reset);

    /**
     * Set element criteria.
     *
     * @param array $settings
     */
    public function setCriteria(array $settings);

    /**
     * Get element attributes.
     *
     * @param array            $map
     * @param BaseElementModel $element
     */
    public function getAttributes(array $map, BaseElementModel $element);
}
