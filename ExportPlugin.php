<?php

namespace Craft;

class ExportPlugin extends BasePlugin
{
    public function getName()
    {
        return Craft::t('Export');
    }

    public function getVersion()
    {
        return '0.4.8';
    }

    public function getDeveloper()
    {
        return 'Bob Olde Hampsink';
    }

    public function getDeveloperUrl()
    {
        return 'http://www.itmundi.nl';
    }

    public function hasCpSection()
    {
        return true;
    }

    public function registerUserPermissions()
    {
        return array(
            'reset' => array('label' => Craft::t('Reset export map')),
        );
    }
}
