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
        return '0.2.2';
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
    
    function registerUnitTest() 
    {
    
        // Import the test
        Craft::import('plugins.export.tests.ExportTest');
        
        // Return the test
        return new ExportTest();
    
    }
    
}