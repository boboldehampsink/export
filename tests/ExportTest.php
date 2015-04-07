<?php

namespace Craft;

/**
 * Export test.
 *
 * Contains unit tests for the Export plugin.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */
class ExportTest extends BaseTest
{
    /**
     * Set up test.
     */
    public function setUp()
    {
        // Load plugins
        $pluginsService = craft()->getComponent('plugins');
        $pluginsService->loadPlugins();
    }

    /**
     * Test the downloading of entries.
     */
    public function testActionDownloadEntries()
    {
        $settings = array(
            'type' => 'Entry',
            'elementvars' => array(
                'section' => '14',
                'entrytype' => '',
            ),
            'map' => array(
                'elementId' => array(
                    'name' => 'ID',
                    'label' => 'ID',
                    'checked' => '',
                ),
                'slug' => array(
                    'name' => 'Slug',
                    'label' => 'Slug',
                    'checked' => '',
                ),
                'authorId' => array(
                    'name' => 'Author',
                    'label' => 'Author',
                    'checked' => '',
                ),
                'postDate' => array(
                    'name' => 'Post Date',
                    'label' => 'Post Date',
                    'checked' => '',
                ),
                'expiryDate' => array(
                    'name' => 'Expiry Date',
                    'label' => 'Expiry Date',
                    'checked' => '',
                ),
                'enabled' => array(
                    'name' => 'Enabled',
                    'label' => 'Enabled',
                    'checked' => '',
                ),
                'status' => array(
                    'name' => 'Status',
                    'label' => 'Status',
                    'checked' => '',
                ),
            ),
        );

        // Download
        $data = craft()->export->download($settings);

        // check if we got a csv
        $this->assertInternalType('string', $data);
    }

    /**
     * Test the downloading of users.
     */
    public function testActionDownloadUsers()
    {
        $settings = array(
            'type' => 'User',
            'elementvars' => array(
              'groups' => array(1),
            ),
            'map' => array(
                'elementId' => array(
                    'name' => 'ID',
                    'label' => 'ID',
                    'checked' => '',
                ),
                'username' => array(
                    'name' => 'Username',
                    'label' => 'Username',
                    'checked' => '1',
                ),
                'firstName' => array(
                    'name' => 'First Name',
                    'label' => 'First Name',
                    'checked' => '1',
                ),
                'lastName' => array(
                    'name' => 'Last Name',
                    'label' => 'Last Name',
                    'checked' => '1',
                ),
                'email' => array(
                    'name' => 'Email',
                    'label' => 'Email',
                    'checked' => '1',
                ),
                'status' => array(
                    'name' => 'Status',
                    'label' => 'Status',
                    'checked' => '',
                ),
            ),
        );

        // Download
        $data = craft()->export->download($settings);

        // check if we got a csv
        $this->assertInternalType('string', $data);
    }
}
