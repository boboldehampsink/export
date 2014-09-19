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
    
        // Set criteria
        $criteria = new \CDbCriteria;
        $criteria->condition = 'settings = :settings';
        $criteria->params = array(
            ':settings' => JsonHelper::encode($settings)
        );
        
        // Check if we have a map already
        $stored = Export_MapRecord::model()->find($criteria);
                
        if(!count($stored)) {
                   
            // Get section id
            $section = $settings['elementvars']['section'];
            
            // Get entrytype id(s)
            $entrytype = $settings['elementvars']['entrytype'];
            
            // If "All"
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
                $layout = array(
                    ExportModel::HandleId         => array('name' => Craft::t("ID"), 'checked' => 0),
                    ExportModel::HandleTitle . "_" . $entrytype->id => array('name' => $entrytype->hasTitleField ? $entrytype->titleLabel : Craft::t("Title"), 'checked' => 1, 'entrytype' => $entrytype->id),
                    ExportModel::HandleSlug       => array('name' => Craft::t("Slug"), 'checked' => 0),
                    ExportModel::HandleAuthor     => array('name' => Craft::t("Author"), 'checked' => 0),
                    ExportModel::HandlePostDate   => array('name' => Craft::t("Post Date"), 'checked' => 0),
                    ExportModel::HandleExpiryDate => array('name' => Craft::t("Expiry Date"), 'checked' => 0),
                    ExportModel::HandleEnabled    => array('name' => Craft::t("Enabled"), 'checked' => 0),
                    ExportModel::HandleStatus     => array('name' => Craft::t("Status"), 'checked' => 0)
                );
                
                // Set the dynamic fields for this type
                $tabs = craft()->fields->getLayoutById($entrytype->fieldLayoutId)->getTabs();
                foreach($tabs as $tab) {
                    $fieldData = array();
                    foreach($tab->getFields() as $field) {
                        $data = $field->getField();
                        $fieldData[$data->handle] = array('name' => $data->name, 'checked' => 1);
                    }
                    $layout += $fieldData;
                }
                        
                // Set the static fields also
                $fields += $layout;
                        
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
    
        // Get entries by criteria
        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->limit = null;
        $criteria->status = isset($settings['map']['status']) ? $settings['map']['status'] : null;
    
        // Get by section and entrytype
        $criteria->sectionId = $settings['elementvars']['section'];
        $criteria->type      = $settings['elementvars']['entrytype'];
    
        return $criteria;
    
    }
    
    public function parseColumn($handle, $element, $settings, $delimiter)
    {
    
        // If not found, use handle
        $column = $handle;
        
        // Parse title
        if(substr($handle, 0, 5) == ExportModel::HandleTitle) {
            $id     = substr($handle, 6);
            $handle = ExportModel::HandleTitle;
        }
        
        switch($handle) {
    
            case ExportModel::HandleTitle:
                $entrytype = craft()->sections->getEntryTypeById($id);
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