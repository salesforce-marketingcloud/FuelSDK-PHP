<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_Subscriber;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_Subscriber
*/
final class SubscriberTest extends TestCase
{
    private $client;
    

    function __construct()
    {
        $this->client = new ET_Client(true);
    }

    public function testCanCreateSubscriber()
    {
        $result = $this->createSubscriber();
        $this->assertEquals($result->status, TRUE);
        $this->assertEquals($result->results[0]->StatusMessage == "Created Subscriber.", TRUE);
        return $result->results[0];
    }

    /**
    * @depends testCanCreateSubscriber
    */
    public function testCanGetSubscriber($subscriber)
    {
        $getsubscriber = $this->getSubscriber($subscriber->NewID);
        //make sure the get was successful
        $this->assertEquals($getsubscriber->status, TRUE);
        //compare the key of the subscriber
        $this->assertEquals($getsubscriber->results[0]->SubscriberKey == $subscriber->Object->SubscriberKey, TRUE);
        return $getsubscriber->results[0];
    }

     /**
    * @depends testCanGetSubscriber
    */
    public function testCanUpdateSubscriber($subscriber)
    {
        $newEmail = "updatedemail@salesforce.com";
        $updatedSubscriber = $this->updateSubscriber($subscriber,$newEmail);
        $this->assertEquals($updatedSubscriber->status, TRUE);
        $this->assertEquals($updatedSubscriber->results[0]->StatusMessage == "Updated Subscriber.", TRUE);
        $getsubscriber = $this->getSubscriber($subscriber->ID);

        $this->assertEquals($getsubscriber->results[0]->EmailAddress == $newEmail, TRUE);
        return $subscriber;
    }

    /**
    * @depends testCanUpdateSubscriber
    */
    public function testCanDeleteSubscriber($subscriber)
    {
        $result = $this->deleteSubscriber($subscriber);
        $this->assertEquals($result->status, TRUE);
        $this->assertEquals($result->results[0]->StatusMessage == "Subscriber deleted", TRUE);

    }

    public function testCanUpsertSubscriber()
    {
        $listtest = new ListTest();
        $list = $listtest->createList();
        $listID = $list->results[0]->NewID;
        $subscriber = new ET_Subscriber();
        $subscriber->props = array("SubscriberKey" => "PHPSDKSubscriber".uniqid(), 
                                    "EmailAddress" => uniqid()."@salesforce.com",
                                    "Lists" => array("ID" => $listID),
                                    "Attributes" => array("Name" => "First Name", "Value" => "FirstName".uniqid()),
                                    "Attributes" => array("Name" => "Last Name", "Value" => "LastName".uniqid())
                                    );

        //call upsert to create a new subscriber.
        $result = $this->upsertSubscriber($subscriber);
        $this->assertEquals($result->status, TRUE);
        $this->assertEquals($result->results[0]->StatusMessage == "Updated Subscriber.", TRUE);

        //try to get the subscriber we created above
        $getsubscriber = $this->getSubscriber($result->results[0]->Object->ID);
        //make sure the get was successful
        $this->assertEquals($getsubscriber->status, TRUE);
        //call the upsert again ... but this time we are going to update the existing one by passing ID field populated
        $subscriber->props = array("ID" => $getsubscriber->results[0]->ID, "EmailAddress" => "updatedemail@salesforce.com");
        $result = $this->upsertSubscriber($subscriber);
        $this->assertEquals($result->status, TRUE);
        $this->assertEquals($result->results[0]->StatusMessage == "Updated Subscriber.", TRUE);
        
        //delete the subscriber
        $result = $this->deleteSubscriber($getsubscriber->results[0]);

        $this->assertEquals($result->status, TRUE);
        $this->assertEquals($result->results[0]->StatusMessage == "Subscriber deleted", TRUE);

        $result = $listtest->deleteList($list->results[0]->Object);
        $this->assertEquals($result->status, TRUE);

    }

    public function createSubscriber()
    {
        $list = new ListTest();
        $listID = $list->createList()->results[0]->NewID;

        $subscriber = new ET_Subscriber();
        $subscriber->authStub = $this->client;

        $subscriber->props = array("SubscriberKey" => "PHPSDKSubscriber".uniqid(), 
                                    "EmailAddress" => uniqid()."@salesforce.com",
                                    "Lists" => array("ID" => $listID),
                                    "Attributes" => array("Name" => "First Name", "Value" => "FirstName".uniqid()),
                                    "Attributes" => array("Name" => "Last Name", "Value" => "LastName".uniqid())
                                    );

        return $subscriber->post();

    }

    public function upsertSubscriber($subscriber)
    {
        $subscriber->authStub = $this->client;

        return $subscriber->put();

    }

    public function getSubscriber($subscriberId)
    {
        $subscriber = new ET_Subscriber();
        $subscriber->authStub = $this->client;
        $subscriber->filter= array("Property"=>"ID", "SimpleOperator"=>"equals","Value"=>$subscriberId);
        return $subscriber->get();
    }

    public function updateSubscriber($getsubscriber, $newEmail)
    {
        
        $subscriber = new ET_Subscriber();
        $subscriber->authStub = $this->client;
        $subscriber->props["ID"] = $getsubscriber->ID;
        $subscriber->props["EmailAddress"] = $newEmail;

        return $subscriber->patch();
    }

    public function deleteSubscriber($getsubscriber)
    {
        $subscriber = new ET_Subscriber();
        $subscriber->authStub = $this->client;
        $subscriber->props["ID"] = $getsubscriber->ID;

        return $subscriber->delete();
    }

}

?>