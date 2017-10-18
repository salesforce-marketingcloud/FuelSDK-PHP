<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_List;
use FuelSdk\ET_Subscriber;
use FuelSdk\ET_List_Subscriber;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_Asset
*/
final class ListSubscriberTest extends TestCase
{
    private $myclient;
	private $NewListName = "PHPSDKListSubscriber";
	private $SubscriberTestEmail = "PHPSDKListSubscriber@bh.exacttarget.com";

    function __construct()
    {
        $this->myclient = new ET_Client(true);
    }

    public function testCanGetListSubscriber()
    {
        $newListID = $this->createList();
        $this->createSubscriber($newListID);
        $status = $this->createListSubscriber($newListID);

        $this->assertTrue($status);

    }

    public function createList(){
        print "Create List \n";
        $postContent = new ET_List();
        $postContent->authStub = $this->myclient;
        $postContent->props = array("ListName" => $this->NewListName, "Description" => "This list was created with the RubySDK", "Type" => "Private");
        $postResponse = $postContent->post();
        print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$postResponse->code."\n";
        print 'Message: '.$postResponse->message."\n";	
        print 'Results Length: '. count($postResponse->results)."\n";
        print 'Results: '."\n";
        print_r($postResponse->results);
        print "\n---------------\n";
        
        if ($postResponse->status)
            return $postResponse->results[0]->NewID;

    }

    public function createSubscriber($newListID){
		// Create Subscriber on List
		print "Create Subscriber on List \n";
		$subCreate = new ET_Subscriber();
		$subCreate->authStub = $this->myclient;
		$subCreate->props = array("EmailAddress" => $this->SubscriberTestEmail, "SubscriberKey" => "PHPSDKListSubscriber".uniqid(), "Lists" => array("ID" => $newListID));
		$postResult = $subCreate->post();
		print_r('Post Status: '.($postResult->status ? 'true' : 'false')."\n");
		print 'Code: '.$postResult->code."\n";
		print 'Message: '.$postResult->message."\n";	
		print 'Results Length: '. count($postResult->results)."\n";
		print 'Results: '."\n";
		print_r($postResult->results);
		print "\n---------------\n";
    }

    public function createListSubscriber($newListID){
		// Retrieve all Subscribers on the List
		print "Retrieve all Subscribers on the List \n";
		$getList = new ET_List_Subscriber();
		$getList->authStub = $this->myclient;
		$getList->filter = array('Property' => 'ListID','SimpleOperator' => 'equals','Value' => $newListID);
		$getList->props = array("ObjectID","SubscriberKey","CreatedDate","Client.ID","Client.PartnerClientKey","ListID","Status");
		$getResponse = $getList->get();
		print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResponse->code."\n";
		print 'Message: '.$getResponse->message."\n";
		print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
		print 'Results Length: '. count($getResponse->results)."\n";
		print 'Results: '."\n";
		print_r($getResponse->results);
		print "\n---------------\n";

        return $getResponse->status;
    }

}