<?php
namespace Craft;

class Export_CategoryService extends BaseApplicationComponent 
{

    public function getGroups()
    {
    
        // Return editable groups for user
        return craft()->categories->getEditableGroups();
    
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
                ExportModel::HandleTitle     => array('name' => Craft::t("Title"), 'checked' => 1),
                ExportModel::HandleSlug      => array('name' => Craft::t("Slug"), 'checked' => 0),
                ExportModel::HandleParent    => array('name' => Craft::t("Parent"), 'checked' => 0),
                ExportModel::HandleAncestors => array('name' => Craft::t("Ancestors"), 'checked' => 0)
            );
            
            // Set the dynamic fields for this type
            foreach(craft()->fields->getLayoutByType(ElementType::Category)->getFields() as $field) {
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
    
        // Match with current data
        $criteria = craft()->elements->getCriteria(ElementType::Category);
        $criteria->limit = null;
        $criteria->status = isset($settings['map']['status']) ? $settings['map']['status'] : null;
    
        // Look in same group when replacing
        $criteria->groupId = $settings['elementvars']['group'];
    
        return $criteria;
    
    }
    
    public function parseColumn($handle, $element, $settings, $delimiter)
    {
    
        // If not found, use handle
        $column = $handle;
        
        switch($handle) {
    
            case ExportModel::HandleTitle:
                $column = '"'.Craft::t("Title").'"'.$delimiter;
                break;
                
            case ExportModel::HandleSlug:
                $column = '"'.Craft::t("Slug").'"'.$delimiter;
                break;
                
        }
        
        return $column;
    
    }

}