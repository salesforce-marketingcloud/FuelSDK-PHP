<?php
namespace FuelSdk\Test;
use FuelSdk\ET_Client;
use FuelSdk\ET_TriggeredSendSummary;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_Asset
*/
final class TriggeredSendSummaryTest extends TestCase
{
    private $myclient;

    function __construct()
    {
        $this->myclient = new ET_Client(true);
    }

    public function testCanGetSummary()
    {
        // Modify the date below to reduce the number of results returned from the request
        // Setting this too far in the past could result in a very large response size
        
        // Retrieve Filtered Send with GetMoreResults
        print "Retrieve Triggered Send Summary with GetMoreResults \n";
        $getSummary = new ET_TriggeredSendSummary();
        $getSummary->authStub = $this->myclient;
        $getResponse = $getSummary->get();
        print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$getResponse->code."\n";
        print 'Message: '.$getResponse->message."\n";
        print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
        print 'Results Length: '. count($getResponse->results)."\n";
        print "\n---------------\n";

        $this->assertTrue($getResponse->status);
    }

}