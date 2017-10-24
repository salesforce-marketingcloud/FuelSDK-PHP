<?php
namespace FuelSdk\Test;
use FuelSdk\ET_Client;
use FuelSdk\ET_ResultMessage;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_ResultMessage
*/
final class ResultMessageTest extends TestCase
{
    private $myclient;

    function __construct()
    {
        $this->myclient = new ET_Client(true);
    }

    public function testCanGetResultMessage()
    {

        print "Retrieve ResultMessage with GetMoreResults \n";
        $getResultMessage = new ET_ResultMessage();
        $getResultMessage->authStub = $this->myclient;
        $getResponse = $getResultMessage->get();
        print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$getResponse->code."\n";
        print 'Message: '.$getResponse->message."\n";
        print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
        print 'Results Length: '. count($getResponse->results)."\n";
        print "\n---------------\n";

        $this->assertTrue($getResponse->status);
        $this->assertEquals($getResponse->code, "200");

    }

}