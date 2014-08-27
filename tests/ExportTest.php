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
            '\\Craft\\ExportModel'   => '/../models/ExportModel.php',
            '\\Craft\\ExportService' => '/../services/ExportService.php'
        );

        // Inject them
        foreach($map as $classPath => $filePath) {
            if(!class_exists($classPath, false)) {
                require_once($dir . $filePath);
            }
        }
    
        // Construct
        $this->exportService = new ExportService;
    
    } 
    
    function testActionDownloadEntries() 
    {
    
        // Get element type
        $type = ElementType::Entry;
        
        // Entries / get section
        $section = 14;
        $entrytype = 16;
        
        // The fields
        $map = array(
            'title' => '1',
            'slug' => '',
            'authorId' => '',
            'postDate' => '',
            'expiryDate' => '',
            'enabled' => '',
            'status' => '',
            'dealerId' => '1',
            'remarks' => '1',
            'serviceProviderEmail' => '1',
            'ServiceProviderwebsite' => '1',
            'telIc' => '1',
            'telAc' => '1',
            'serviceProviderTelephone' => '1',
            'openingHours' => '1',
            'telAh' => '1',
            'notice' => '1',
            'serviceProviderFax' => '1',
            'unicom' => '1',
            'price' => '1',
            'airportFees' => '1',
            'duty' => '1',
            'vat' => '1',
            'comments' => '1',
            'flightCardAccepted' => '1',
            'sterlingCardAccepted' => '1',
            'avgas' => '1',
            'oneHundredLL' => '1',
            'jetA' => '1',
            'jetA1' => '1',
            'jp8' => '1',
            'ts1' => '1',
            'jetPlus' => '1',
            'selfServiceAvgas' => '1',
            'selfServiceJet' => '1'
        );
        
        // Download
        $data = $this->exportService->download(array(
            'type' => $type,
            'section' => $section,
            'entrytype' => $entrytype,
            'groups' => null,
            'map' => $map
        ));
        
        // check if we got a csv
        $this->assertInternalType('string', $data);
        
    }
    
    function testActionDownloadUsers() 
    {
    
        // Get element type
        $type = ElementType::User;
        
        // Users / get group
        $groups = array(1);
        
        // The fields
        $map = array(
            'username' => '1',
            'firstName' => '',
            'lastName' => '',
            'email' => '',
            'status' => '',
            'addressLine1' => '1',
            'addressLine2' => '1',
            'addressLine3' => '1',
            'zipcode' => '1',
            'city' => '1',
            'state' => '1',
            'country' => '1',
            'nameOfBusiness' => '1',
            'natureOfBusiness' => '1',
            'cardNumber' => '1',
            'accountNumber' => '1',
            'telephone' => '1',
            'fax' => '1',
            'oldUserId' => '1',
            'hasUpdatedProfile' => '1',
            'hasBeenApproved' => '1',
            'preferenceCurrency' => '1',
            'preferenceUnits' => '1',
            'consentmarketing' => '1'
        );
        
        // Download
        $data = $this->exportService->download(array(
            'type' => $type,
            'section' => null,
            'entrytype' => null,
            'groups' => $groups,
            'map' => $map
        ));
        
        // check if we got a csv
        $this->assertInternalType('string', $data);
    
    }
    
}