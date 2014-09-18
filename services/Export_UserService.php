<?php
namespace Craft;

class Export_UserService extends BaseApplicationComponent 
{


    public function getGroups()
    {
    
        // Check if usergroups are allowed in this installation
        if(isset(craft()->userGroups)) {
    
            // Get usergroups
            $groups = craft()->userGroups->getAllGroups();
            
            // Return when groups found
            if(count($groups)) {
            
                return $groups;
                
            }
        
            // Still return true when no groups found
            return true;
        
        }
        
        // Else, dont proceed with the user element
        return false;
    
    }
    
    public function setCriteria($settings)
    {
    
        // Get users by criteria
        $criteria = craft()->elements->getCriteria(ElementType::User);
        $criteria->limit = null;
        $criteria->status = isset($settings['map']['status']) ? $settings['map']['status'] : null;
        
        // Get by group
        $criteria->groupId = $settings['elementvars']['groups'];
        
        return $criteria;
    
    }
    
    public function parseColumn($handle, $settings, $delimiter)
    {
    
        // If not found, use handle
        $column = $handle;
    
        switch($handle) {
    
            case ExportModel::HandleUsername:
                $column = '"'.Craft::t("Username").'"'.$delimiter;
                break;
                
            case ExportModel::HandleFirstName:
                $column = '"'.Craft::t("First Name").'"'.$delimiter;
                break;
                
            case ExportModel::HandleLastName:
                $column = '"'.Craft::t("Last Name").'"'.$delimiter;
                break;
                
            case ExportModel::HandleEmail:
                $column = '"'.Craft::t("Email").'"'.$delimiter;
                break;
            
        }
        
        return $column;
    
    }
    
}