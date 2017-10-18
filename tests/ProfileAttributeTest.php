<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_ProfileAttribute;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_Asset
*/
final class ProfileAttributeTest extends TestCase
{
    private $myclient;
	private $NameOfAttribute;

    function __construct()
    {
        $this->myclient = new ET_Client(true);
        $this->NameOfAttribute = "PHPSDKTestAttribute";//.uniqid();        
    }

    public function testCanCreateProfileAttribute()
    {
        print "Create ProfileAttribute \n";
        $postProfileAttribute = new ET_ProfileAttribute();
        $postProfileAttribute->authStub = $this->myclient;
        $postProfileAttribute->props = array("Name" => $this->NameOfAttribute, "PropertyType"=>"string", "Description"=>"New Attribute from the SDK", "IsRequired"=>"false", "IsViewable"=>"false", "IsEditable"=>"true", "IsSendTime"=>"false");
        $postResponse = $postProfileAttribute->post();
        print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$postResponse->code."\n";
        print 'Message: '.$postResponse->message."\n";	
        print 'Results Length: '. count($postResponse->results)."\n";
        print 'Results: '."\n";
        print_r($postResponse->results);
        print "\n---------------\n";

        $this->assertTrue($postResponse->status);
        $this->assertEquals($postResponse->results[0]->StatusMessage, "Success");
    }

    public function testCanGetProfileAttribute()
    {
        print "Retrieve All ProfileAttributes\n";
        $getProfileAttribute = new ET_ProfileAttribute();
        $getProfileAttribute->authStub = $this->myclient;
        $getResponse = $getProfileAttribute->get();
        print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$getResponse->code."\n";
        print 'Message: '.$getResponse->message."\n";	
        print 'Results Length: '. count($getResponse->results)."\n";
        print 'Results: '."\n";
        print_r($getResponse->results);
        print "\n---------------\n";

        $this->assertTrue($getResponse->status);
        $this->assertEquals($getResponse->results[1]->Name, $this->NameOfAttribute);
    }

    public function testCanUpdateProfileAttribute()
    {
        print "Update ProfileAttribute \n";
        $patchProfileAttribute = new ET_ProfileAttribute();
        $patchProfileAttribute->authStub = $this->myclient;
        $patchProfileAttribute->props = array("Name" => $this->NameOfAttribute, "PropertyType"=>"string", "IsViewable"=>"true");

        $patchResponse = $patchProfileAttribute->patch();
        print_r('Patch Status: '.($patchResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$patchResponse->code."\n";
        print 'Message: '.$patchResponse->message."\n";	
        print 'Results Length: '. count($patchResponse->results)."\n";
        print 'Results: '."\n";
        print_r($patchResponse->results);
        print "\n---------------\n";

        $this->assertTrue($patchResponse->status);
        $this->assertTrue($patchResponse->results[0]->Object->IsViewable);

    }    

    public function testCanDeleteProfileAttribute()
    {
        print "Delete ProfileAttribute \n";
        $deleteProfileAttribute = new ET_ProfileAttribute();
        $deleteProfileAttribute->authStub = $this->myclient;
        $deleteProfileAttribute->props = array("Name" => $this->NameOfAttribute, "PropertyType"=>"string");
        $deleteResponse = $deleteProfileAttribute->delete();
        print_r('Delete Status: '.($deleteResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$deleteResponse->code."\n";
        print 'Message: '.$deleteResponse->message."\n";	
        print 'Results Length: '. count($deleteResponse->results)."\n";
        print 'Results: '."\n";
        print_r($deleteResponse->results);
        print "\n---------------\n";

        $this->assertTrue($deleteResponse->status);
        $this->assertEquals($deleteResponse->results[0]->StatusMessage, "Success");
    }    

}