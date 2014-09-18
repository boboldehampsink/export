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
    
    public function setCriteria($settings)
    {
    
        // Get entries by criteria
        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->limit = null;
        $criteria->status = isset($settings['map']['status']) ? $settings['map']['status'] : null;
    
        // Get by section and entrytype
        $criteria->sectionId = $settings['elementvars']['section'];
        $criteria->type    = $settings['elementvars']['entrytype'];
    
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