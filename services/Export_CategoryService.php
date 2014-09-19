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
       
        // Set the static fields for this type
        $static = array(
            ExportModel::HandleId    => Craft::t("ID"),
            ExportModel::HandleTitle => Craft::t("Title")
        );
        
        // Set the dynamic fields for this type
        $layout = craft()->fields->getLayoutByType(ElementType::Category)->getFields();
        
        // Set the static fields also
        $fields = array(
            'static' => $static,
            'layout' => $layout
        );
        
        // Return fields
        return array($fields);
    
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
    
    public function parseColumn($handle, $settings, $delimiter)
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