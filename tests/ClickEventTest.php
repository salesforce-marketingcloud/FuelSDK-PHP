<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_ClickEvent;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_Asset
*/
final class ClickEventTest extends TestCase
{
    private $myclient;

    function __construct()
    {
        $this->myclient = new ET_Client(true);
    }

    public function testCanGetClickEvent()
    {
        $retrieveDate = "2012-01-15T13:00:00.000";
        // Retrieve Filtered ClickEvent with GetMoreResults
        print "Retrieve Filtered ClickEvent with GetMoreResults \n";
        $getClickEvent = new ET_ClickEvent();
        $getClickEvent->authStub = $this->myclient;
        $getClickEvent->props = array("SendID","SubscriberKey","EventDate","Client.ID","EventType","BatchID","TriggeredSendDefinitionObjectID","PartnerKey");
        $getClickEvent->filter = array('Property' => 'EventDate','SimpleOperator' => 'greaterThan','DateValue' => $retrieveDate);
        $getClickEvent->getSinceLastBatch = true;
        $getResponse = $getClickEvent->get();

        print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$getResponse->code."\n";
        print 'Message: '.$getResponse->message."\n";
        print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
        print 'Results Length: '. count($getResponse->results)."\n";
        print_r( $getResponse->results );   
        print "\n---------------\n";
        $this->assertTrue($getResponse->status);

    }

    public function getAsset($id)
    {
        $asset = new ET_Asset();
        $auth = $this->myclient;
        $asset->authStub = $auth;
        $asset->props["mediaItemID"] = $id;

        $result = $asset->get();


        print 'Results: "\n"';
        print_r($result);
        
        return $result;
    }

}