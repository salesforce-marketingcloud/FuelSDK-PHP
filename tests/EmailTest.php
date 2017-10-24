<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_Email;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_Email
*/
final class EmailTest extends TestCase
{
    private $client;
    

    function __construct()
    {
        $this->client = new ET_Client(true);
    }

    public function testCanCreateEmail()
    {
        $result = $this->createEmail();
        print_r( $result );
        $this->assertEquals($result->status, TRUE);
        return $result->results[0];
        
    }

    /**
    * @depends testCanCreateEmail
    */
    public function testCanGetEmail($email)
    {
        $getemail = $this->getMCEmail($email->NewID);
        //make sure the get was successful
        $this->assertEquals($getemail->status, TRUE);
        //compare the Email of the email
        $this->assertEquals($getemail->results[0]->Name == $email->Object->Name, TRUE);
        return $getemail->results[0];
    }

     /**
    * @depends testCanGetEmail
    */
    public function testCanUpdateEmail($email)
    {
        $newName = "Updated Email Address";
        $updatedEmail = $this->updateEmail($email,$newName);

        $getemail = $this->getMCEmail($email->ID);

        $this->assertEquals($getemail->results[0]->Name, $newName);
        return $email;
    }

    /**
    * @depends testCanUpdateEmail
    */
    public function testCanDeleteEmail($email)
    {
        $result = $this->deleteEmail($email);
        $this->assertEquals($result->status, TRUE);

    }

    public function createEmail()
    {
        $myclient = new ET_Client(true);
        $email = new ET_Email();
        $email->authStub = $myclient;
        $email->props = array("CustomerKey" => "SDK Example".uniqid(), 
            "Name"=> "SDK".uniqid(), 
            "Subject"=>"Created with the PHP SDK Testcase",  
            "HTMLBody"=> "<b>This is a test message from PHP SDK Testcase</b>".uniqid(),  
            "EmailType" => "HTML", 
            "IsHTMLPaste" => "true");
        return  $email->post();
    }

    public function getMCEmail($emailId)
    {
        $myclient = new ET_Client(true);
        $email = new ET_Email();
        $email->authStub = $myclient;
        $email->filter= array("Property"=>"ID", "SimpleOperator"=>"equals","Value"=>$emailId);
        return $email->get();
    }

    public function updateEmail($getemail, $newName)
    {
        $myclient = new ET_Client(true);
        $email = new ET_Email();
        $email->authStub = $myclient;
        $email->props["ID"] = $getemail->ID;
        $email->props["Name"] = $newName;

        return $email->patch();
    }

    public function deleteEmail($getemail)
    {
        $myclient = new ET_Client(true);
        $email = new ET_Email();
        $email->authStub = $myclient;
        $email->props["ID"] = $getemail->ID;

        return $email->delete();
    }

    
}

?>