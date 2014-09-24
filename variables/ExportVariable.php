<?php 
namespace Craft;

class ExportVariable 
{

    public function getGroups($elementType) 
    {
    
        // Get from right elementType
        $service = 'export_' . strtolower($elementType);
        
        // Check if elementtype can be imported
        if(isset(craft()->$service)) {
    
            // Return "groups" (section, groups, etc.)
            return craft()->$service->getGroups();
            
        } 
        
        return false;
    
    }
    
    public function getFields($elementType, $reset)
    {
    
        // Get from right elementType
        $service = 'export_' . strtolower($elementType);
        
        // Get export vars
        $export = craft()->request->getParam('export');
        
        // Return fields of elementType
        return craft()->$service->getFields($export, $reset);
    
    }

}