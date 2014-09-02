<?php
namespace Craft;

class ExportPlugin extends BasePlugin
{

    function getName()
    {
        return Craft::t('Export');
    }

    function getVersion()
    {
        return '0.2.3';
    }

    function getDeveloper()
    {
        return 'Bob Olde Hampsink';
    }

    function getDeveloperUrl()
    {
        return 'http://www.itmundi.nl';
    }
    
    function hasCpSection()
    {
        return true;
    }
    
}