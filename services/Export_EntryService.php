<?php
namespace Craft;

class Export_EntryService extends BaseApplicationComponent 
{

    public function getGroups()
    {
    
        // Get editable sections for user
        $editable = craft()->sections->getEditableSections();
        
        // Get sections but not singles
        $sections = array();
        foreach($editable as $section) {
            if($section->type != SectionType::Single) {
                $sections[] = $section;
            }
        }
        
        return $sections;
    
    }
    
    public function getFields($settings)
    {
        
        // Get section id
        $section = $settings['elementvars']['section'];
        
        // Get entrytype id(s)
        $entrytype = $settings['elementvars']['entrytype'];
        
        if(empty($entrytype)) {
        
            // Get entrytype models
            $entrytypes = craft()->sections->getEntryTypesBySectionId($section);
        
        } else {
        
            // Get entrytype model
            $entrytypes = array(craft()->sections->getEntryTypeById($entrytype));
            
        }
        
        // Create a nice field map
        $fields = array();
        
        // With multiple or one entry type
        foreach($entrytypes as $entrytype) {
            
            // Set the static fields for this type
            $static = array(
                ExportModel::HandleId         => Craft::t("ID"),
                ExportModel::HandleTitle      => $entrytype->hasTitleField ? $entrytype->titleLabel : false,
                ExportModel::HandleSlug       => Craft::t("Slug"),
                ExportModel::HandleParent     => Craft::t("Parent"),
                ExportModel::HandleAuthor     => Craft::t("Author"),
                ExportModel::HandlePostDate   => Craft::t("Post Date"),
                ExportModel::HandleExpiryDate => Craft::t("Expiry Date"),
                ExportModel::HandleEnabled    => Craft::t("Enabled"),
                ExportModel::HandleStatus     => Craft::t("Status")
            );
            
            // Set the dynamic fields for this type
            $layout = array();
            $tabs = craft()->fields->getLayoutById($entrytype->fieldLayoutId)->getTabs();
            foreach($tabs as $tab) {
                $layout += $tab->getFields();
            }
        
            // Set the static fields also
            $fields[] = array(
                'static' => $static,
                'layout' => $layout
            );
        
        }
        
        // Return fields
        return $fields;
    
    }
    
    public function setCriteria($settings)
    {
    
        // Get entries by criteria
        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->limit = null;
        $criteria->status = isset($settings['map']['status']) ? $settings['map']['status'] : null;
    
        // Get by section and entrytype
        $criteria->sectionId = $settings['elementvars']['section'];
        $criteria->type      = $settings['elementvars']['entrytype'];
    
        return $criteria;
    
    }
    
    public function parseColumn($handle, $settings, $delimiter)
    {
    
        // If not found, use handle
        $column = $handle;
        
        switch($handle) {
    
            case ExportModel::HandleTitle:
                $entrytype = craft()->sections->getEntryTypeById($settings['elementvars']['entrytype']);
                $column = '"'.($entrytype ? addslashes($entrytype->titleLabel) : Craft::t("Title")).'"'.$delimiter;
                break;
                
            case ExportModel::HandleParent:
                $column = '"'.Craft::t("Parent").'"'.$delimiter;
                break;
                
            case ExportModel::HandleAuthor:
                $column = '"'.Craft::t("Author").'"'.$delimiter;
                break;
                
            case ExportModel::HandlePostDate:
                $column = '"'.Craft::t("Post Date").'"'.$delimiter;
                break;
                
            case ExportModel::HandleExpiryDate:
                $column = '"'.Craft::t("Expiry Date").'"'.$delimiter;
                break;
                
            case ExportModel::HandleEnabled:
                $column = '"'.Craft::t("Enabled").'"'.$delimiter;
                break;
                
            case ExportModel::HandleSlug:
                $column = '"'.Craft::t("Slug").'"'.$delimiter;
                break;
                
        }
        
        return $column;
    
    }
    
}