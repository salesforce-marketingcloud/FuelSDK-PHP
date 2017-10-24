<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_User;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_User
*/
final class UserTest extends TestCase
{
    private $client;
    
    function __construct()
    {
        $this->client = new ET_Client(true);
    }


    public function testCanCreateUser()
    {
        $result = $this->createUser();
        $this->user = $result->results[0];
        $this->assertEquals($result->status, TRUE);
        return $result->results[0]->Object;
    }
    /**
    * @depends testCanCreateUser
    */
    public function testCanGetUser($user)
    {
        
        $getuser = $this->getUser($user->ID);
        //make sure the get was successful
        $this->assertEquals($getuser->status, TRUE);
        //compare the Email of the user
        $this->assertEquals($getuser->results[0]->UserID == $user->UserID, TRUE);
        return $getuser->results[0];
    }

    /**
    * @depends testCanGetUser
    */
    public function testCanUpdateUser($user)
    {
        //update just the name of the user
        $updatedUser = $this->updateUser($user,"test@example.com");
        //fetch the user again after update
        $getuser = $this->getUser($user->ID, true);
        //make sure the get was successful
        $this->assertEquals($getuser->status, TRUE);
        //compare the updated Email property
        $this->assertEquals($getuser->results[0]->Email == "test@example.com", TRUE);
        $this->disableUser($getuser->results[0]);
        
    }

   

    public function getUser($id, $permission = false)
    {
        $user = new ET_User();
        $auth = $this->client;
        $user->authStub = $auth;
        
        $user->filter= array("Property"=>"ID", "SimpleOperator"=>"equals","Value"=>$id);
        $result = $user->get();

        return $result;
    }

    public function createUser($name = "")
    {
        $user = new ET_User();
        $auth = $this->client;

        $user->authStub = $auth;
        if($name == "")
        {
            $name = "TestUser".uniqid();
        }
        $user->props["Name"] = $name;
        $user->props["UserID"] = $name;
        $user->props["Password"] = "23789hsjshkjs%%*";
        $user->props["Email"]=$name."@outlook.com";
        $user->props["ActiveFlag"] = "true";
        $user->props["IsAPIUser"] = "true";
        $user->props["IsLocked"] = "true";
        $user->props["MustChangePassword"] = "true";
        $user->props["Client"] = array("ID" => "10766790");
        $user->props["Delete"] = 0;
        $user->props["SsoIdentities"] = array("SsoIdentity" => array("Active" => "true","FederatedID" => $name));
        $user->props["DefaultBusinessUnit"] = "10766790";
        return $user->post();
        
    }

    public function updateUser($getuser,$email)
    {
        $user = new ET_User();
        $auth = $this->client;
        $user->authStub = $auth;
        $user->props["Client"] = array("ID"=>10766790);
        $user->props["ID"] = $getuser->ID;
        $user->props["UserID"] = $getuser->UserID;
        $user->props["Name"] = $getuser->Name;
        $user->props["Email"] = $email;
        $user->props["Delete"] = 0;
        return $user->patch();

    }

    public function disableUser($getuser)
    {
        $user = new ET_User();
        $auth = $this->client;
        $user->authStub = $auth;
        $user->props["Client"] = array("ID"=>10766790);
        $user->props["ID"] = $getuser->ID;
        $user->props["UserID"] = $getuser->UserID;
        $user->props["Name"] = $getuser->Name;
        $user->props["Email"] = $getuser->Email;
        $user->props["Delete"] = 1;
        $user->props["ActiveFlag"] = 0;
        return $user->patch();

    }

   

    
}

?>