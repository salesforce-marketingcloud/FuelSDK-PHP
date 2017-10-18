<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_Import;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_Asset
*/
final class ImportTest extends TestCase
{
    private $myclient;
	private $NewImportName;
	private $SendableDataExtensionCustomerKey;
	private $TaskResultID;

    function __construct()
    {
        $this->myclient = new ET_Client(true);
        $this->NewImportName = "PHPSDKImport";
        $this->SendableDataExtensionCustomerKey = "92b0d93d-ee5f-e711-80d2-1402ec6b9528";
    }

    public function testCanCreateImportDefinition()
    {
        print "Create Import to DataExtension\n";
        $postImport = new ET_Import();
        $postImport->authStub = $this->myclient;
        $postImport->props = array("Name"=>$this->NewImportName);
        $postImport->props["CustomerKey"] = $this->NewImportName;
        $postImport->props["Description"] = "Created with RubySDK";
        $postImport->props["AllowErrors"] = "true";
        $postImport->props["DestinationObject"] = array("ObjectID"=>$this->SendableDataExtensionCustomerKey);
        $postImport->props["FieldMappingType"] = "InferFromColumnHeadings";
        $postImport->props["FileSpec"] = "PHPExample.csv";
        $postImport->props["FileType"] = "CSV";
        $postImport->props["Notification"] = array("ResponseType"=>"email","ResponseAddress"=>"example@example.com");
        $postImport->props["RetrieveFileTransferLocation"] = array("CustomerKey"=>"ExactTarget Enhanced FTP");
        $postImport->props["UpdateType"] = "Overwrite";
        $postResponse = $postImport->post();
        print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$postResponse->code."\n";
        print 'Message: '.$postResponse->message."\n";	
        print 'Results Length: '. count($postResponse->results)."\n";
        print 'Results: '."\n";
        print_r($postResponse->results);
        print "\n---------------\n";

        $this->assertTrue($postResponse->status);
        $this->assertEquals($postResponse->results[0]->StatusMessage, "ImportDefinition created.");
    }

    public function testCanGetImportDefinition()
    {
        print "Get Import\n";
        $getImport = new ET_Import();
        $getImport->authStub = $this->myclient;
        $getImport->props = array("CustomerKey" => $this->NewImportName);
        $getResponse = $getImport->get();
        print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$getResponse->code."\n";
        print 'Message: '.$getResponse->message."\n";	
        print 'Results Length: '. count($getResponse->results)."\n";
        print 'Results: '."\n";
        print_r($getResponse);
        print "\n---------------\n";

        $this->assertTrue($getResponse->status);
    }

    public function testCanStartImportDefinition()
    {
        print "Start Import to DataExtension\n";
        $startImport = new ET_Import();
        $startImport->authStub = $this->myclient;
        $startImport->props = array("CustomerKey"=>$this->NewImportName);
        $startResponse = $startImport->start();
        print_r('Start Status: '.($startResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$startResponse->code."\n";
        print 'Message: '.$startResponse->message."\n";	
        print 'Results Length: '. count($startResponse->results)."\n";
        print 'Results: '."\n";
        print_r($startResponse->results);
        print "\n---------------\n";

        
        $this->assertTrue($startResponse->status);
        $this->assertEquals($startResponse->results[0]->Task->StatusMessage, "OK");
        $this->assertContains("ImportDefinition performed", $startResponse->results[0]->StatusMessage);        

        return $startResponse->results[0]->Task->ID;    
    }

    /**
     * @depends testCanStartImportDefinition
     */
    public function testCanGetStatusImportDefinition($TaskResultID)
    {
        print "Get Status Import\n";
        $getImport = new ET_Import();
        $getImport->authStub = $this->myclient;
        $getImport->props = array("CustomerKey" => $this->NewImportName);
        $getImport->lastTaskID = $TaskResultID;
        echo "Task ID: " . $TaskResultID . "\n\n\n";

        $getResponse = $getImport->status();
        print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$getResponse->code."\n";
        print 'Message: '.$getResponse->message."\n";	
        print 'Results Length: '. count($getResponse->results)."\n";
        print 'Results: '."\n";
        print_r($getResponse);
        print "\n---------------\n";

        $this->assertTrue($getResponse->status);
    }    

    public function testCanDeleteImportDefinition()
    {
        print "Delete Import\n";
        $deleteImport = new ET_Import();
        $deleteImport->authStub = $this->myclient;
        $deleteImport->props = array("CustomerKey" => $this->NewImportName);
        $deleteResponse = $deleteImport->delete();
        print_r('Delete Status: '.($deleteResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$deleteResponse->code."\n";
        print 'Message: '.$deleteResponse->message."\n";	
        print 'Results Length: '. count($deleteResponse->results)."\n";
        print 'Results: '."\n";
        print_r($deleteResponse);
        print "\n---------------\n";

        $this->assertTrue($deleteResponse->status);
        $this->assertEquals($deleteResponse->results[0]->StatusMessage, "ImportDefinition deleted");
    }    

}