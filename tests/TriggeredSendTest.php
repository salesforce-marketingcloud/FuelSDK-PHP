<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_TriggeredSend;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_Asset
*/
final class TriggeredSendTest extends TestCase
{
    private $myclient;
	private $NameOfTestTS;
    private $SendClassificationID;
    private $EmailID;
    private $TSNameForCreateThenDelete;
    private $TSNameForDelete;

    function __construct()
    {
        $this->myclient = new ET_Client(true);
        $this->NameOfTestTS = "TEXTEXT";
        $this->SendClassificationID = "33e2e7ee-194a-e711-80d2-1402ec6b9528";
        $this->EmailID = "189392";
//        $this->TSNameForCreateThenDelete = uniqid();
    }

    public function testCanGetTriggeredSendDefinition()
    {
        //Triggered Send Testing
        print_r("\nGet all TriggeredSendDefinitions \n");
        $trigger = new ET_TriggeredSend();
        $trigger->authStub = $this->myclient;
        $trigger->props = array("CustomerKey", "Name", "TriggeredSendStatus");	
        $getResult = $trigger->get();
        print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
        print 'Code: '.$getResult->code."\n";
        print 'Message: '.$getResult->message."\n";
        print 'Result Count: '.count($getResult->results)."\n";
        print 'Results: '."\n";
        print_r($getResult->results);
        print "\n---------------\n";
        $this->assertTrue($getResult->status);
    }

    public function testCanPauseTriggeredSendDefinition()
    {
        // Pause a TriggeredSend
        print_r("\nPause a TriggeredSend \n");
        $patchTrig = new ET_TriggeredSend();
        $patchTrig->authStub = $this->myclient;
        $patchTrig->props = array('CustomerKey' => $this->NameOfTestTS, 'TriggeredSendStatus' => 'Inactive' );
        $patchResult = $patchTrig->patch();
        print_r('Patch Status: '.($patchResult->status ? 'true' : 'false')."\n");
        print 'Code: '.$patchResult->code."\n";
        print 'Message: '.$patchResult->message."\n";
        print 'Result Count: '.count($patchResult->results)."\n";
        print 'Results: '."\n";
        print_r($patchResult->results);
        print "\n---------------\n";

        $this->assertTrue($patchResult->status);
        $this->assertEquals($patchResult->results[0]->StatusMessage, "TriggeredSendDefinition updated");
        $this->assertEquals($patchResult->results[0]->Object->TriggeredSendStatus, "Inactive");
    }

    public function testCanStartTriggeredSendDefinition()
    {
        // Start a TriggeredSend by setting to Active
        print_r("\nStart a TriggeredSend by setting to Active \n");
        $patchTrig = new ET_TriggeredSend();
        $patchTrig->authStub = $this->myclient;
        $patchTrig->props = array('CustomerKey' => $this->NameOfTestTS, 'TriggeredSendStatus' => 'Active', 'RefreshContent'=>'true' );
        $patchResult = $patchTrig->patch();
        print_r('Patch Status: '.($patchResult->status ? 'true' : 'false')."\n");
        print 'Code: '.$patchResult->code."\n";
        print 'Message: '.$patchResult->message."\n";
        print 'Result Count: '.count($patchResult->results)."\n";
        print 'Results: '."\n";
        print_r($patchResult->results);
        print "\n---------------\n";   

        $this->assertTrue($patchResult->status);
        $this->assertEquals($patchResult->results[0]->StatusMessage, "TriggeredSendDefinition updated");
        $this->assertEquals($patchResult->results[0]->Object->TriggeredSendStatus, "Active");   

    }

    public function testCanSendTriggeredSendDefinition()
    {
        // Send an email with TriggeredSend 
        print_r("\nSend an email using a triggered send \n");
        $sendTrigger = new ET_TriggeredSend();
        $sendTrigger->props = array('CustomerKey' => $this->NameOfTestTS);
        $sendTrigger->authStub = $this->myclient;
        $sendTrigger->subscribers = array(array("EmailAddress" => "shiarif@gmail.com", "SubscriberKey" => "sharif.ahmed@salesforce.com"));
        $sendResult = $sendTrigger->send();
        print_r('Send Status: '.($sendResult->status ? 'true' : 'false')."\n");
        print 'Code: '.$sendResult->code."\n";
        print 'Message: '.$sendResult->message."\n";
        print 'Results: '."\n";
        print_r($sendResult->results);
        print "\n---------------\n";   

        $this->assertTrue($sendResult->status);
        $this->assertEquals($sendResult->results[0]->StatusMessage, "Created TriggeredSend");
    }

    public function testCanCreateTriggeredSendDefinition()
    {
        // Create a TriggeredSend Definition
        print_r("\nCreate a TriggeredSend Definition  \n");
        $this->TSNameForCreateThenDelete = uniqid();        
        echo 'CustomerKey = ' . $this->TSNameForCreateThenDelete . "\n";

        $postTrig = new ET_TriggeredSend();
        $postTrig->authStub = $this->myclient;
        $postTrig->props = array('CustomerKey' => $this->TSNameForCreateThenDelete,'Name' => $this->TSNameForCreateThenDelete, 'Email' => array("ID"=>$this->EmailID), "SendClassification"=> array("ObjectID"=> $this->SendClassificationID) );
        $postResult = $postTrig->post();
        print_r('Post Status: '.($postResult->status ? 'true' : 'false')."\n");
        print 'Code: '.$postResult->code."\n";
        print 'Message: '.$postResult->message."\n";
        print 'Result Count: '.count($postResult->results)."\n";
        print 'Results: '."\n";
        print_r($postResult->results);
        print "\n---------------\n";

        $this->TSNameForDelete = $postResult->results[0]->Object->Name;

        $this->assertTrue($postResult->status);
        $this->assertEquals($postResult->results[0]->StatusMessage, "TriggeredSendDefinition created");
        $this->assertEquals($postResult->results[0]->Object->Name, $this->TSNameForCreateThenDelete);
        //$this->assertEquals($postResult->results[0]->Object->EmailSubject, $this->NameOfTestTS);

        return $this->TSNameForCreateThenDelete;          
    }

    /**
     * @depends testCanCreateTriggeredSendDefinition
     */
    public function testCanDeleteTriggeredSendDefinition($TSNameForDelete)
    {
        // Delete a TriggeredSend Definition 
        print_r("\nDelete a TriggeredSend Definition \n");
        echo 'CustomerKey = ' . $TSNameForDelete . "\n";

        $deleteTrig = new ET_TriggeredSend();
        $deleteTrig->authStub = $this->myclient;
        $deleteTrig->props = array('CustomerKey' => $TSNameForDelete);
        $deleteResult = $deleteTrig->delete();
        print_r('Delete Status: '.($deleteResult->status ? 'true' : 'false')."\n");
        print 'Code: '.$deleteResult->code."\n";
        print 'Message: '.$deleteResult->message."\n";
        print 'Result Count: '.count($deleteResult->results)."\n";
        print 'Results: '."\n";
        print_r($deleteResult->results);
        print "\n---------------\n";

        $this->assertTrue($deleteResult->status);
        $this->assertEquals($deleteResult->results[0]->StatusMessage, "TriggeredSendDefinition deleted");
        $this->assertEquals($deleteResult->results[0]->Object->CustomerKey, $TSNameForDelete);  
    }    


}