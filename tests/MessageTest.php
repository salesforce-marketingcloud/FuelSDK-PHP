<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_Message_Guide;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_Message
*/
final class MessageTest extends TestCase
{
    private $myclient;

    function __construct()
    {
        $this->myclient = new ET_Client(true);
    }

    public function testCanCreateMessage()
    {
        $result = $this->createMessage();

        $this->assertEquals($result->status, TRUE);

    }
    
    public function createMessage($name = "")
    {
        // Create a new Message
        print "Create a new Message \n";
        $postMG = new ET_Message_Guide();
        $postMG->authStub = $this->myclient;

        $getResult = $postMG->get();
        //print_r($getResult);
        $message = $getResult->results;

        $postMG->props = $message;	
        $postResponse = $postMG->Post();
        print 'Post Response: '."\n";
        print_r($postResponse);
        return $postResponse;
    }

    public function testCanGetMessage()
    {
        //first create a Message
        $getMG = new ET_Message_Guide();
        $getMG->authStub = $this->myclient;
        $message = $this->createMessage("Get Message test ".uniqid());
        //get the newly created Message
        $getMessage = $this->getMessage($message->results->id);
        //compare the name of the Message
        $this->assertEquals($getMessage->results->key == $message->results->key, TRUE);
    }

    public function getMessage($id)
    {

        $RetrieveMessageByKeyTestKey = "527BC1BC-E9B1-402D-8FB0-3125D1088A55";
        $RetrieveMessageByKeyTestKey = "66ed9f5b-d685-467f-a77b-62dc44df59f1";
        
        // Retrieve Message by Key	 
        print "Retrieve Message by Key \n";
        $getSingleMG = new ET_Message_Guide();
        $getSingleMG->authStub = $this->myclient;
        $getSingleMG->props = array("id" => $id);
        
        $getSingleResult = $getSingleMG->get();
        print_r('Get Status: '.($getSingleResult->status ? 'true' : 'false')."\n");
        print 'Code: '.$getSingleResult->code."\n";
        print 'Message: '.$getSingleResult->message."\n";
        print 'Results: "\n"';
        print_r($getSingleResult->results);
        print "\n---------------\n";
        return $getSingleResult;
    }    

    public function testCanConvertMessage(){
        $convertHTML = "<html><head><meta name=\"messageType\" content=\"application/vnd.et.message.email.html\"><meta name=\"viewTypes\" content=\"emailhtmlbody\" data-type=\"guide\"></head><body><div style=\"background: black; border: 1; width: 105px; height: 305px;\"><div data-type=\"slot\" style=\"background: red; border: 1; width: 100px; height: 100px;\" data-alias=\"master\">R</div><div data-type=\"slot\" data-alias=\"A\" style=\"background: white; border: 1; width: 100px; height: 100px;\">W</div><div data-type=\"slot\" data-alias=\"B\" style=\"background: blue; border: 1; width: 100px; height: 100px;\">B <div data-type=\"slot\" data-alias=\"C\" style=\"background: orange; border: 1; width: 100px; height: 100px;\">C <br /></div></div></div><a href=\"%%profile_center_url%%\" alias=\"Update Profile\">Update Profile</a><table cellpadding=\"2\" cellspacing=\"0\" width=\"600\" ID=\"Table5\" Border=0><tr><td><font face=\"verdana\" size=\"1\" color=\"#444444\">This email was sent to:  %%emailaddr%% <br><br><b>Email Sent By:</b> %%Member_Busname%%<br>%%Member_Addr%% %%Member_City%%, %%Member_State%%, %%Member_PostalCode%%, %%Member_Country%%<br><br></font></td></tr></table></body></html>";
        // Convert a Message
        print "Convert a Message \n";
        $convertMG = new ET_Message_Guide();
        $convertMG->authStub = $this->myclient;
        $convertMG->props = array("content" => $convertHTML);
        $convertResponse = $convertMG->convert();
        
        print_r('Post Status: '.($convertResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$convertResponse->code."\n";
        print 'Message: '.$convertResponse->message."\n";
        print 'Results Length: '. count($convertResponse->results)."\n";
        print 'Results: '."\n";
        print_r($convertResponse->results);
        print "\n---------------\n";

        $this->assertEquals($convertResponse->results->messageType->value, "application/vnd.et.message.email.html");
    }

    public function updateMessage($id)
    {
        $desc = "chaning the description";
        $message = new ET_Message_Guide();
//        $message = new ET_Message();
        
        $auth = $this->client;
        $message->authStub = $auth;
        $message->props["id"] = $id;
        $message->props["description"] = $desc;

        $result = $message->patch();

        return $result;
    }

    public function deleteMessage($id)
    {
        $message = new ET_Message_Guide();
//        $message = new ET_Message();
        
        $auth = $this->client;
        $message->authStub = $auth;
        $message->props["id"] = $id;

        $result = $message->delete();

        return $result;
    }






}