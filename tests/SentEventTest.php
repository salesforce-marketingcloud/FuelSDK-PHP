<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_SentEvent;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_Asset
*/
final class SentEventTest extends TestCase
{
    private $myclient;

    function __construct()
    {
        $this->myclient = new ET_Client(true);
    }

    public function testCanGetClickEvent()
    {
        $retrieveDate = "2013-01-15T13:00:00.000";
        
        // Retrieve Filtered SentEvent with GetMoreResults
        print "Retrieve Filtered SentEvent with GetMoreResults \n";
        $getSentEvent = new ET_SentEvent();
        $getSentEvent->authStub = $this->myclient;
        $getSentEvent->props = array("SendID","SubscriberKey","EventDate","Client.ID","EventType","BatchID","TriggeredSendDefinitionObjectID","ListID","PartnerKey","SubscriberID");
        $getSentEvent->filter = array('Property' => 'EventDate','SimpleOperator' => 'greaterThan','DateValue' => $retrieveDate);
        $getSentEvent->getSinceLastBatch = false;
        $getResponse = $getSentEvent->get();
        print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$getResponse->code."\n";
        print 'Message: '.$getResponse->message."\n";
        print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
        print 'Results Length: '. count($getResponse->results)."\n";
        print "\n---------------\n";
        
        $this->assertTrue($getResponse->status);

    }

}