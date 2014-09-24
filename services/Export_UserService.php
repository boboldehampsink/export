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
    
    public function getFields($settings)
    {
    
        // Set criteria
        $criteria = new \CDbCriteria;
        $criteria->condition = 'settings = :settings';
        $criteria->params = array(
            ':settings' => JsonHelper::encode($settings)
        );
        
        // Check if we have a map already
        $stored = Export_MapRecord::model()->find($criteria);
                
        if(!count($stored)) {
       
            // Set the static fields for this type
            $fields = array(
                ExportModel::HandleId        => array('name' => Craft::t("ID"), 'checked' => 0),
                ExportModel::HandleUsername  => array('name' => Craft::t("Username"), 'checked' => 1),
                ExportModel::HandleFirstName => array('name' => Craft::t("First Name"), 'checked' => 1),
                ExportModel::HandleLastName  => array('name' => Craft::t("Last Name"), 'checked' => 1),
                ExportModel::HandleEmail     => array('name' => Craft::t("Email"), 'checked' => 1),
                ExportModel::HandleStatus    => array('name' => Craft::t("Status"), 'checked' => 0)
            );
            
            // Set the dynamic fields for this type
            foreach(craft()->fields->getLayoutByType(ElementType::User)->getFields() as $field) {
                $data = $field->getField();
                $fields[$data->handle] = array('name' => $data->name, 'checked' => 1);
            }
            
        } else {
        
            // Get the stored map        
            $fields = $stored->map;
        
        }
        
        // Return fields
        return $fields;
    
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
    
    public function parseColumn($handle, $element, $settings, $delimiter)
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
    
    public function getAttributes($map, $element)
    {
    
        // Get element as array
        $attributes = array_merge($element->getAttributes(), $element->getContent()->getAttributes());
        
        return $attributes;
    
    }    
    
}