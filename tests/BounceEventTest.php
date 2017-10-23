<?php
namespace FuelSdk\Test;

use PHPUnit\Framework\TestCase;
use FuelSdk\ET_BounceEvent;
use FuelSdk\ET_Client;

/**
* @covers ET_Asset
*/
final class BounceEventTest extends TestCase
{
    private $myclient;

    function __construct()
    {
        $this->myclient = new ET_Client(true);
    }

    public function testCanGetBounceEvent()
    {

        $retrieveDate = "2011-01-15T13:00:00.000";
        
        // Retrieve Filtered BounceEvent with GetMoreResults
        print "Retrieve Filtered BounceEvent with GetMoreResults \n";
        $getBounceEvent = new ET_BounceEvent();
        $getBounceEvent->authStub = $this->myclient;
        $getBounceEvent->props = array("SendID","SubscriberKey","EventDate","Client.ID","EventType","BatchID","TriggeredSendDefinitionObjectID","PartnerKey");
        $getBounceEvent->filter = array('Property' => 'EventDate','SimpleOperator' => 'greaterThan','DateValue' => $retrieveDate);
        $getBounceEvent->getSinceLastBatch = false;
        $getResponse = $getBounceEvent->get();
        print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$getResponse->code."\n";
        print 'Message: '.$getResponse->message."\n";
        print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
        print 'Results Length: '. count($getResponse->results)."\n";
        print "\n---------------\n";

        $this->assertTrue($getResponse->status);

    }

}