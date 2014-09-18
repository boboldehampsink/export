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

}