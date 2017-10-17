<?php

use PHPUnit\Framework\TestCase;

/**
* @covers ET_SMSKeyword
*/
final class SMSKeywordTest extends TestCase
{
    private $myclient;

    function __construct()
    {
        $this->myclient = new ET_Client(true);
    }

    public function testCanGetUnsubEvent()
    {
        try {
            $postKey = new ET_SMSKeyword();
            $postKey->authStub = $this->myclient;
            $postKey->props = array("ShortCode" => "29860", "Keyword"=> "AWESOMEYOU", "CountryCode"=> "US" );	
            $postResult = $postKey->post();
            print_r($postResult);

            $this->assertNotNull($postResult->results->keywordId);
            $this->assertEquals($postResult->code, "202");
        }
        catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
}

