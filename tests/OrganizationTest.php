<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_Organization;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_Asset
*/
final class OrganizationTest extends TestCase
{
    private $myclient;
    private $CustomerKeyofExistingOrganization;
    private $NameOfTestOrganization;
    private $CustomerKeyOfTestOrganization;

    function __construct()
    {
        $this->myclient = new ET_Client(true);
        $this->CustomerKeyofExistingOrganization = '65941725-F407-49C4-A64A-F6C8E38A01B1';
        $this->NameOfTestOrganization = "TestOrganizationName";
        $this->CustomerKeyOfTestOrganization = "TestOrganizationCustomerKey::" . substr(md5(rand()),0,7);
    }

    public function testCanGetAllOrganization()
    {
        // Retrieve All Organizations with GetMoreResults
        print "Retrieve All Organizations with GetMoreREsults \n";
        $getOrganization = new ET_Organization();
        $getOrganization->authStub = $this->myclient;
        $getOrganization->props = array("ID", "Name", "AccountType", "Address", "BrandID", "BusinessName", "City", "Country", "DeletedDate", "EditionID", "Email", "Fax", "FromName", "InheritAddress", "IsActive", "IsTestAccount", "IsTrialAccount", "ParentAccount.ID", "ParentID", "ParentName", "Phone", "PrivateLabelID", "Roles", "State", "Zip", "CreatedDate", "ModifiedDate", "CustomerKey", "Client.EnterpriseID");
        $getResponse = $getOrganization->get();
        print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$getResponse->code."\n";
        print 'Message: '.$getResponse->message."\n";
        print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
        print 'Results Length: '. count($getResponse->results)."\n";
        print "\n---------------\n";

        $this->assertTrue($getResponse->status);
        $this->assertTrue($getResponse->moreResults);   
    }

    public function testCanGetOneOrganization()
    {
        // Retreive Specific Organization
        print "Retrieve Specific Organization \n";
        $getOrganization = new ET_Organization();
        $getOrganization->authStub = $this->myclient;
        $getOrganization->props = array("ID", "Name", "IsActive", "CustomerKey");
        $getOrganization->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $this->CustomerKeyofExistingOrganization);
        $getResponse = $getOrganization->get();
        print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$getResponse->code."\n";
        print 'Message: '.$getResponse->message."\n";
        print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
        print 'Results Length: '. count($getResponse->results)."\n";
        print "\n---------------\n";
        print_r($getResponse);
        print "\n---------------\n";

        $this->assertTrue($getResponse->status);
        $this->assertEquals($getResponse->results[0]->CustomerKey, $this->CustomerKeyofExistingOrganization);
    }

    public function testCanCreateOrganization()
    {
        // Create Organization
        print "Create Organization \n";
        $postOrganization = new ET_Organization();
        $postOrganization->authStub = $this->myclient;
        $postOrganization->props = array("CustomerKey" =>  $this->CustomerKeyOfTestOrganization, "Name" => $this->NameOfTestOrganization, "AccountType" => "ENTERPRISE_2", "DBID" => "101", "Email" => "test@organization.com", "FromName" => "AGENCY CLIENT", "Business Name" => "Test Organization", "Address" => "123 ABC Street", "City" => "Indianapolis", "State" => "IN", "Zip" => "46202", "IsTestAccount" => true, "EditionID" => 3, "IsActive" => true);
        $postResult = $postOrganization->post();
        print_r('Post Status: '.($postResult->status ? 'true' : 'false')."\n");
        print 'Code: '.$postResult->code."\n";
        print 'Message: '.$postResult->message."\n";	
        print 'Results Length: '. count($postResult->results)."\n";
        print 'Results: '."\n";
        print_r($postResult->results);
        print "\n---------------\n";

        $this->assertTrue($postResult->status);
        $this->assertEquals($postResult->results[0]->StatusMessage, "Account Updated / Created");
        $this->assertEquals($postResult->results[0]->Object->CustomerKey, $this->CustomerKeyOfTestOrganization);

        return $this->CustomerKeyOfTestOrganization;
    }    


    /**
     * @depends testCanCreateOrganization
     */

    public function testCanUpdateOrganization($CustomerKeyOfTestOrganization)
    {
        // Update Organization
        print "Update Organization \n";
        $patchOrganization = new ET_Organization();
        $patchOrganization->authStub = $this->myclient;
        $patchOrganization->props = array("CustomerKey" =>  $CustomerKeyOfTestOrganization, "Name" => "New TestOrganizationName", "AccountType" => "ENTERPRISE_2", "Email" => "test@organization.com", "FromName" => "AGENCY CLIENT", "Business Name" => "Test Organization", "Address" => "123 ABC Street", "City" => "Indianapolis", "State" => "IN", "Zip" => "46202", "IsTestAccount" => true, "EditionID" => 3, "IsActive" => true, "AccountStatusID" => "1");
        $patchResult = $patchOrganization->patch();
        print_r('Patch Status: '.($patchResult->status ? 'true' : 'false')."\n");
        print 'Code: '.$patchResult->code."\n";
        print 'Message: '.$patchResult->message."\n";	
        print 'Results Length: '. count($patchResult->results)."\n";
        print 'Results: '."\n";
        print_r($patchResult->results);
        print "\n---------------\n";
        
        $this->assertTrue($patchResult->status);
        $this->assertEquals($patchResult->results[0]->StatusMessage, "Account Updated / Created");
        $this->assertEquals($patchResult->results[0]->Object->CustomerKey, $CustomerKeyOfTestOrganization);
    }

}