<?php
namespace FuelSdk\Test;
use FuelSdk\ET_Client;
use FuelSdk\ET_DataExtractActivity;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_DataExtract
*/
final class DataExtractTest extends TestCase
{
    private $myclient;
    private $extractmap;
	private $extracttype = "Data Extension Extract";
	private $filename = "php_extract.csv";
	private $DECustKey = "017dce26-b61f-43c2-bb15-0e46de82d177";    
    
    function __construct()
    {
        $this->myclient = new ET_Client();
//        $this->extractmap = array();
//        $this->populateExtractType();
    }    

/*    private function populateExtractType()
    {
        $extractdesc = new ET_ExtractDescription();
        $extractdesc->authStub = $this->myclient;
        $extractdesc->props = array("ID","CustomerKey","Name", "Description","InteractionObjectID", "ObjectID","PartnerKey","CreatedDate","Client.ID","EventType","BatchID","TriggeredSendDefinitionObjectID","PartnerKey");
        $extractResponse = $extractdesc->get();
        foreach($extractResponse->results as $obj){
            $this->extractmap[$obj->Name] = $obj->ObjectID;
        }
    }

    public function testCanPerformDataExtract()
    {
        try {        
            print "Start Data Extraction\n";
            $startExtract = new ET_DataExtractActivity();
            $startExtract->authStub = $this->myclient;

            $Parameters= array( 				
                "Parameter"=>array(
                    array("Name"=>"StartDate", "Value"=>"2017-06-01 01:00 AM"),
                    array("Name"=>"EndDate", "Value"=>"2017-09-01 01:00 AM"),
                    array("Name"=>"OutputFileName", "Value"=>$this->filename),
                    array("Name"=>"DECustomerKey", "Value"=>$this->DECustKey),
                    array("Name"=>"HasColumnHeaders", "Value"=>"true"),
                    array("Name"=>"_AsyncID", "Value"=>"0")
                )
            );

            $startExtract->props = array("ID"=>$this->extractmap[$this->extracttype], "Options"=>"", "Parameters"=>$Parameters);

            $startResponse = $startExtract->start();
            print_r('Start Status: '.($startResponse->status ? 'true' : 'false')."\n");
            print 'Code: '.$startResponse->code."\n";
            print 'Message: '.$startResponse->message."\n";	
            print 'Results Length: '. count($startResponse->results)."\n";
            print 'Results: '."\n";
            print_r($startResponse->results);
            print "\n---------------\n";	
            print_r($startResponse);

            $this->assertEquals($startResponse->code, "200");
            $this->assertEquals($startResponse->status, "OK");
            $this->assertNotNull($startResponse->request_id);


        }
        catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }        
    }    
*/
    public function testCanPerformDataExtractOnTrackingData()
    {
        try {        
            print "Start Data Extraction On Tracking Data\n";
            $startExtract = new ET_DataExtractActivity();
            $startExtract->authStub = $this->myclient;

            $startResponse = $startExtract->extractTrackingData("2017-06-01 12:00 AM", "2017-09-01 12:00 AM");
            print_r('Start Status: '.($startResponse->status ? 'true' : 'false')."\n");
            print 'Code: '.$startResponse->code."\n";
            print 'Message: '.$startResponse->message."\n";	
            print 'Results Length: '. count($startResponse->results)."\n";
            print 'Results: '."\n";
            print_r($startResponse->results);
            print "\n---------------\n";	
            print_r($startResponse);

            $this->assertEquals($startResponse->code, "200");
            $this->assertEquals($startResponse->status, "OK");
            $this->assertNotNull($startResponse->request_id);


        }
        catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }        
    } 

    public function testCanPerformDataExtractOnDE()
    {
        try {        
            print "Start Data Extraction On Data Extension\n";
            $startExtract = new ET_DataExtractActivity();
            $startExtract->authStub = $this->myclient;

            $startResponse = $startExtract->extractDataExtension($this->DECustKey);
            print_r('Start Status: '.($startResponse->status ? 'true' : 'false')."\n");
            print 'Code: '.$startResponse->code."\n";
            print 'Message: '.$startResponse->message."\n";	
            print 'Results Length: '. count($startResponse->results)."\n";
            print 'Results: '."\n";
            print_r($startResponse->results);
            print "\n---------------\n";	
            print_r($startResponse);

            $this->assertEquals($startResponse->code, "200");
            $this->assertEquals($startResponse->status, "OK");
            $this->assertNotNull($startResponse->request_id);


        }
        catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }        
    } 


}