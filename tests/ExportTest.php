<?php
namespace Craft;

class ExportTest extends \WebTestCase 
{
    
    function testActionDownloadEntries() 
    {
    
        // Get element type
        $type = ElementType::Entry;
        
        // Entries / get section
        $section = 14;
        $entrytype = 16;
        
        // The fields
        $fields = array(
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
        
        // Get token
        $token = craft()->tokens->createToken(array('action' => 'export/download'));
        
        // Post fields and download csv
        $result = $this->post(
            str_replace('admin/', '', UrlHelper::getActionUrl('export/download', array('token' => $token))),
            array(
                'type' => $type,
                'section' => $section,
                'entrytype' => $entrytype,
                'groups' => null,
                'fields' => $fields
            )
        );
        
        // check if we got a csv
        $this->assertMime(array('text/csv; charset=utf-8'));
        
    }
    
    function testActionDownloadUsers() 
    {
    
        // Get element type
        $type = ElementType::User;
        
        // Users / get group
        $groups = array(1);
        
        // The fields
        $fields = array(
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
        
        // Get token
        $token = craft()->tokens->createToken(array('action' => 'export/download'));
        
        // Post fields and download csv
        $result = $this->post(
            str_replace('admin/', '', UrlHelper::getActionUrl('export/download', array('token' => $token))),
            array(
                'type' => $type,
                'section' => null,
                'entrytype' => null,
                'groups' => $groups,
                'fields' => $fields
            )
        );
        
        // check if we got a csv
        $this->assertMime(array('text/csv; charset=utf-8'));
    
    }
    
}