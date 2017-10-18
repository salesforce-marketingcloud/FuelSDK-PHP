<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_OpenEvent;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_Asset
*/
final class OpenEventTest extends TestCase
{
    private $myclient;

    function __construct()
    {
        $this->myclient = new ET_Client(true);
    }

    public function testCanGetOpenEvent()
    {
        $retrieveDate = "2012-01-15T13:00:00.000";
        
        // Retrieve Filtered OpenEvent with GetMoreResults
        print "Retrieve Filtered OpenEvent with GetMoreResults \n";
        $getOpenEvent = new ET_OpenEvent();
        $getOpenEvent->authStub = $this->myclient;
        $getOpenEvent->props = array("SendID","SubscriberKey","EventDate","Client.ID","EventType","BatchID","TriggeredSendDefinitionObjectID","PartnerKey");
        $getOpenEvent->filter = array('Property' => 'EventDate','SimpleOperator' => 'greaterThan','DateValue' => $retrieveDate);
        $getOpenEvent->getSinceLastBatch = false;
        $getResponse = $getOpenEvent->get();
        print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$getResponse->code."\n";
        print 'Message: '.$getResponse->message."\n";
        print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
        print 'Results Length: '. count($getResponse->results)."\n";
        print "\n---------------\n";


        $this->assertTrue($getResponse->status);

    }

}