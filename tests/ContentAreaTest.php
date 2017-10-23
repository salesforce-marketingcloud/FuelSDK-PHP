<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_ContentArea;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_ContentArea
*/
final class ContentAreaTest extends TestCase
{
    private $client;
    

    function __construct()
    {
        $this->client = new ET_Client(true);
    }

    public function testCanCreateContentArea()
    {
        $result = $this->createContentArea();
        $this->assertEquals($result->status, TRUE);
        
        return $result->results[0];
    }

    /**
    * @depends testCanCreateContentArea
    */
    public function testCanGetContentArea($contentarea)
    {
        $getCA = $this->getContentArea($contentarea->NewID);
        //make sure the get was successful
        $this->assertEquals($getCA->status, TRUE);
        //compare the content area name
        $this->assertEquals($getCA->results[0]->Name == $contentarea->Object->Name, TRUE);
        return $getCA->results[0];
    }

     /**
    * @depends testCanGetContentArea
    */
    public function testCanUpdateContentArea($contentarea)
    {
        $newContent = "Updated Content";
        $updatedCA = $this->updateContentArea($contentarea,$newContent);

        $getCA = $this->getContentArea($contentarea->ID);
        var_dump($getCA);
        $this->assertEquals($getCA->results[0]->Content == $newContent, TRUE);
        return $contentarea;
    }

    /**
    * @depends testCanUpdateContentArea
    */
    public function testCanDeleteContentArea($contentarea)
    {
        $result = $this->deleteContentArea($contentarea);
        $this->assertEquals($result->status, TRUE);

    }

    public function createContentArea()
    {
        $contentarea = new ET_ContentArea();
        $contentarea->authStub = $this->client;
        $contentarea->props = array("CustomerKey" => "ExampleContentArea".uniqid(), "Name"=>"ExampleContentArea".uniqid(), "Content"=> "Original Content");
        return $contentarea->post();

    }

    public function getContentArea($contentAreaId)
    {
        $contentarea = new ET_ContentArea();
        $contentarea->authStub = $this->client;
        $contentarea->filter= array("Property"=>"ID", "SimpleOperator"=>"equals","Value"=>$contentAreaId);

        return $contentarea->get();
    }

   
    public function updateContentArea($contarea,$updatedContent)
    {
        $contentarea = new ET_ContentArea();
        $contentarea->authStub = $this->client;
        $contentarea->props = array("ID" => $contarea->ID, "Content"=> $updatedContent);

        return $contentarea->patch();
    }

    public function deleteContentArea($contarea)
    {
        $contentarea = new ET_ContentArea();
        $contentarea->authStub = $this->client;
        $contentarea->props = array("ID" => $contarea->ID);

        return $contentarea->delete();
    }



}

?>