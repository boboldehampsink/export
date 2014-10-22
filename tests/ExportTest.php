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
            'title_14' => 
            array (
              'name' => 'Country',
              'label' => 'COUNTRY',
              'checked' => '1',
            ),
            'airportState' => 
            array (
              'name' => 'State',
              'label' => 'STATE',
              'checked' => '1',
            ),
            'airportCity' => 
            array (
              'name' => 'City',
              'label' => 'CITY',
              'checked' => '1',
            ),
            'title_15' => 
            array (
              'name' => 'Airport',
              'label' => 'AIRPORT',
              'checked' => '1',
            ),
            'iata' => 
            array (
              'name' => 'IATA Code',
              'label' => 'IATA ',
              'checked' => '1',
            ),
            'icao' => 
            array (
              'name' => 'ICAO code',
              'label' => 'ICAO',
              'checked' => '1',
            ),
            'title_16' => 
            array (
              'name' => 'Service Provider',
              'label' => 'SERVICE_PROVIDER',
              'checked' => '1',
            ),
            'telIc' => 
            array (
              'name' => 'telephone international country code',
              'label' => 'INTERNATIONAL_CODE',
              'checked' => '1',
            ),
            'telAc' => 
            array (
              'name' => 'telephone area code',
              'label' => 'AREA_CODE',
              'checked' => '1',
            ),
            'serviceProviderTelephone' => 
            array (
              'name' => 'telephone number',
              'label' => 'TELEPHONE',
              'checked' => '1',
            ),
            'serviceProviderFax' => 
            array (
              'name' => 'fax number',
              'label' => 'FAX',
              'checked' => '1',
            ),
            'unicom' => 
            array (
              'name' => 'unicom',
              'label' => 'UNICOM',
              'checked' => '1',
            ),
            'telAh' => 
            array (
              'name' => 'after hours phone',
              'label' => 'AFTER_HOURS_PHONE',
              'checked' => '1',
            ),
            'openingHours' => 
            array (
              'name' => 'opening hours',
              'label' => 'OPENING_HOURS',
              'checked' => '1',
            ),
            'notice' => 
            array (
              'name' => 'opening hours notice',
              'label' => 'NOTICE',
              'checked' => '1',
            ),
            'flightCardAccepted' => 
            array (
              'name' => 'flight card accepted',
              'label' => 'FLIGHT_CARD_ACCEPT',
              'checked' => '1',
            ),
            'sterlingCardAccepted' => 
            array (
              'name' => 'Sterling Card accepted',
              'label' => 'STERLING_CARD_ACCEPT',
              'checked' => '1',
            ),
            'dealerId' => 
            array (
              'name' => 'dealer ID',
              'label' => 'DEALER_ID',
              'checked' => '1',
            ),
            'avgas' => 
            array (
              'name' => 'avgas fuel offered?',
              'label' => 'Avgas',
              'checked' => '1',
            ),
            'oneHundredLL' => 
            array (
              'name' => '100LL fuel offered?',
              'label' => '100LL',
              'checked' => '1',
            ),
            'jetA' => 
            array (
              'name' => 'jet A fuel offered?',
              'label' => 'JET A',
              'checked' => '1',
            ),
            'jetA1' => 
            array (
              'name' => 'Jet A1 fuel offered?',
              'label' => 'JET A-1',
              'checked' => '1',
            ),
            'ts1' => 
            array (
              'name' => 'TS-1 fuel offered?',
              'label' => 'TS-1',
              'checked' => '1',
            ),
            'jp8' => 
            array (
              'name' => 'JP-8 fuel offered?',
              'label' => 'JP-8',
              'checked' => '1',
            ),
            'jetPlus' => 
            array (
              'name' => 'jet+ additive offered?',
              'label' => 'Jet + Additive',
              'checked' => '1',
            ),
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
            ),
            'latitude' => 
            array (
              'name' => 'Latitude',
              'label' => 'Latitude',
              'checked' => '',
            ),
            'longitude' => 
            array (
              'name' => 'Longitude',
              'label' => 'Longitude',
              'checked' => '',
            ),
            'remarks' => 
            array (
              'name' => 'remarks',
              'label' => 'remarks',
              'checked' => '',
            ),
            'serviceProviderEmail' => 
            array (
              'name' => 'e-mail address',
              'label' => 'e-mail address',
              'checked' => '',
            ),
            'ServiceProviderwebsite' => 
            array (
              'name' => 'website url',
              'label' => 'website url',
              'checked' => '',
            ),
            'price' => 
            array (
              'name' => 'fuel price',
              'label' => 'fuel price',
              'checked' => '',
            ),
            'airportFees' => 
            array (
              'name' => 'airport fees',
              'label' => 'airport fees',
              'checked' => '',
            ),
            'duty' => 
            array (
              'name' => 'duty',
              'label' => 'duty',
              'checked' => '',
            ),
            'vat' => 
            array (
              'name' => 'VAT %',
              'label' => 'VAT %',
              'checked' => '',
            ),
            'comments' => 
            array (
              'name' => 'comments',
              'label' => 'comments',
              'checked' => '',
            ),
            'selfServiceAvgas' => 
            array (
              'name' => 'self service Avgas',
              'label' => 'self service Avgas',
              'checked' => '',
            ),
            'selfServiceJet' => 
            array (
              'name' => 'self service jet',
              'label' => 'self service jet',
              'checked' => '',
            ),
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
            ),
            'addressLine1' => 
            array (
              'name' => 'Address line 1',
              'label' => 'Address line 1',
              'checked' => '1',
            ),
            'addressLine2' => 
            array (
              'name' => 'Address line 2',
              'label' => 'Address line 2',
              'checked' => '1',
            ),
            'addressLine3' => 
            array (
              'name' => 'Address line 3',
              'label' => 'Address line 3',
              'checked' => '1',
            ),
            'zipcode' => 
            array (
              'name' => 'Zipcode',
              'label' => 'Zipcode',
              'checked' => '1',
            ),
            'city' => 
            array (
              'name' => 'City',
              'label' => 'City',
              'checked' => '1',
            ),
            'state' => 
            array (
              'name' => 'State',
              'label' => 'State',
              'checked' => '1',
            ),
            'country' => 
            array (
              'name' => 'Country',
              'label' => 'Country',
              'checked' => '1',
            ),
            'nameOfBusiness' => 
            array (
              'name' => 'Name of business',
              'label' => 'Name of business',
              'checked' => '1',
            ),
            'natureOfBusiness' => 
            array (
              'name' => 'Nature of Business',
              'label' => 'Nature of Business',
              'checked' => '1',
            ),
            'cardNumber' => 
            array (
              'name' => 'Sterling card number',
              'label' => 'Sterling card number',
              'checked' => '1',
            ),
            'accountNumber' => 
            array (
              'name' => 'Global Ref number',
              'label' => 'Global Ref number',
              'checked' => '1',
            ),
            'telephone' => 
            array (
              'name' => 'Telephone number',
              'label' => 'Telephone number',
              'checked' => '1',
            ),
            'fax' => 
            array (
              'name' => 'Fax number',
              'label' => 'Fax number',
              'checked' => '1',
            ),
            'oldUserId' => 
            array (
              'name' => 'Old User ID',
              'label' => 'Old User ID',
              'checked' => '1',
            ),
            'hasUpdatedProfile' => 
            array (
              'name' => 'Has updated profile',
              'label' => 'Has updated profile',
              'checked' => '1',
            ),
            'hasBeenApproved' => 
            array (
              'name' => 'Has been approved',
              'label' => 'Has been approved',
              'checked' => '1',
            ),
            'preferenceCurrency' => 
            array (
              'name' => 'Preference Currency',
              'label' => 'Preference Currency',
              'checked' => '1',
            ),
            'preferenceUnits' => 
            array (
              'name' => 'Preference Units',
              'label' => 'Preference Units',
              'checked' => '1',
            ),
            'consentmarketing' => 
            array (
              'name' => 'Consent Marketing',
              'label' => 'Consent Marketing',
              'checked' => '1',
            ),
            'nextApproval' => 
            array (
              'name' => 'Next Approval',
              'label' => 'Next Approval',
              'checked' => '1',
            ),
            'delegatedBpUsers' => 
            array (
              'name' => 'Delegated BP Users',
              'label' => 'Delegated BP Users',
              'checked' => '1',
            ),
            'notifications' => 
            array (
              'name' => 'Notifications',
              'label' => 'Notifications',
              'checked' => '1',
            ),
          ),
        );
        
        // Download
        $data = $this->exportService->download($settings);
        
        // check if we got a csv
        $this->assertInternalType('string', $data);
    
    }
    
}