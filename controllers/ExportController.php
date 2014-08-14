<?php
namespace Craft;

class ExportController extends BaseController
{

    public $allowAnonymous = array('actionDownload');

    public function actionGetEntryTypes() 
    {
    
        // Only ajax post requests
        $this->requirePostRequest();
        $this->requireAjaxRequest();
        
        // Get section
        $section = craft()->request->getPost('section');
        $section = craft()->sections->getSectionById($section);
        
        // Get entry types
        $entrytypes = $section->getEntryTypes();
        
        // Return JSON
        $this->returnJson($entrytypes);
    
    }

    // Process input for mapping
    public function actionMap() 
    {
    
        // Only post requests
        $this->requirePostRequest();
        
        // Create token
        $token = craft()->tokens->createToken(array('action' => 'export/download'));
            
        // Send variables to template and display
        $this->renderTemplate('export/map', array(
            'exportToken' => $token
        ));
    
    }
    
    // Start import task
    public function actionDownload() 
    {
    
        // We need a token for this
        //$this->requireToken();
        
        // Only post requests
        $this->requirePostRequest();
        
        // Get element type
        $type = craft()->request->getParam('type');
        
        // Entries / get section
        $section = craft()->request->getParam('section');
        $entrytype = craft()->request->getParam('entrytype');
        
        // Users / get groups
        $groups = craft()->request->getParam('groups');
        
        // Get mapping fields
        $map = craft()->request->getParam('fields');
        
        // Download
        craft()->export->download(array(
            'type' => $type,
            'section' => $section,
            'entrytype' => $entrytype,
            'groups' => $groups,
            'map' => $map
        ));
    
    }
    
}