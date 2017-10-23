<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_UnsubEvent;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_Asset
*/
final class UnsubEventTest extends TestCase
{
    private $myclient;

    function __construct()
    {
        $this->myclient = new ET_Client(true);
    }

    public function testCanGetUnsubEvent()
    {
        $retrieveDate = "2017-01-15T13:00:00.000";
        
        // Retrieve Filtered UnsubEvent with GetMoreResults
        print "Retrieve Filtered UnsubEvent with GetMoreResults \n";
        $getUnsubEvent = new ET_UnsubEvent();
        $getUnsubEvent->authStub = $this->myclient;
        $getUnsubEvent->props = array("SendID","SubscriberKey","EventDate","Client.ID","EventType","BatchID","TriggeredSendDefinitionObjectID","PartnerKey");
        $getUnsubEvent->filter = array('Property' => 'EventDate','SimpleOperator' => 'greaterThan','DateValue' => $retrieveDate);
        $getUnsubEvent->getSinceLastBatch = false;
        $getResponse = $getUnsubEvent->get();
        print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$getResponse->code."\n";
        print 'Message: '.$getResponse->message."\n";
        print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
        print 'Results Length: '. count($getResponse->results)."\n";
        print "\n---------------\n";
        
        $this->assertTrue($getResponse->status);

    }

}