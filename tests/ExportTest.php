<?php
namespace Craft;

class ExportTest extends BaseTest 
{

    protected $exportService;
    
    public function setUp()
    {
    
        // Get dependencies
        $dir = __DIR__;
        $map = array(
            '\\Craft\\ExportModel'         => '/../models/ExportModel.php',
            '\\Craft\\ExportService'       => '/../services/ExportService.php',
            '\\Craft\\Export_EntryService' => '/../services/Export_EntryService.php',
            '\\Craft\\Export_UserService'  => '/../services/Export_UserService.php'
        );

        // Inject them
        foreach($map as $classPath => $filePath) {
            if(!class_exists($classPath, false)) {
                require_once($dir . $filePath);
            }
        }
    
        // Construct
        $this->exportService      = new ExportService;
        $this->exportEntryService = new Export_EntryService;
        $this->exportUserService  = new Export_UserService;
    
    } 
    
    function testActionDownloadEntries() 
    {
    
        $settings = array (
          'service' => $this->exportEntryService,
          'type' => 'Entry',
          'elementvars' => 
          array (
            'section' => '14',
            'entrytype' => '',
          ),
          'map' => 
          array (
            'elementId' => 
            array (
              'name' => 'ID',
              'label' => 'ID',
              'checked' => '',
            ),
            'slug' => 
            array (
              'name' => 'Slug',
              'label' => 'Slug',
              'checked' => '',
            ),
            'authorId' => 
            array (
              'name' => 'Author',
              'label' => 'Author',
              'checked' => '',
            ),
            'postDate' => 
            array (
              'name' => 'Post Date',
              'label' => 'Post Date',
              'checked' => '',
            ),
            'expiryDate' => 
            array (
              'name' => 'Expiry Date',
              'label' => 'Expiry Date',
              'checked' => '',
            ),
            'enabled' => 
            array (
              'name' => 'Enabled',
              'label' => 'Enabled',
              'checked' => '',
            ),
            'status' => 
            array (
              'name' => 'Status',
              'label' => 'Status',
              'checked' => '',
            )
          ),
        );
            
        // Download
        $data = $this->exportService->download($settings);
        
        // check if we got a csv
        $this->assertInternalType('string', $data);
        
    }
    
    function testActionDownloadUsers() 
    {
    
        $settings = array (
          'service' => $this->exportUserService,
          'type' => 'User',
          'elementvars' => 
          array (
            'groups' => 
            array (1),
          ),
          'map' => 
          array (
            'elementId' => 
            array (
              'name' => 'ID',
              'label' => 'ID',
              'checked' => '',
            ),
            'username' => 
            array (
              'name' => 'Username',
              'label' => 'Username',
              'checked' => '1',
            ),
            'firstName' => 
            array (
              'name' => 'First Name',
              'label' => 'First Name',
              'checked' => '1',
            ),
            'lastName' => 
            array (
              'name' => 'Last Name',
              'label' => 'Last Name',
              'checked' => '1',
            ),
            'email' => 
            array (
              'name' => 'Email',
              'label' => 'Email',
              'checked' => '1',
            ),
            'status' => 
            array (
              'name' => 'Status',
              'label' => 'Status',
              'checked' => '',
            )
          ),
        );
        
        // Download
        $data = $this->exportService->download($settings);
        
        // check if we got a csv
        $this->assertInternalType('string', $data);
    
    }
    
}