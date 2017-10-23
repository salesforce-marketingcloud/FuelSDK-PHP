<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_DataExtension;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_DataExtension
*/
final class DataExtensionTest extends TestCase
{
    private $client;
    

    function __construct()
    {
        $this->client = new ET_Client(true);
    }

    public function testCanCreateDataExtension()
    {
        $result = $this->createDataExtension();
        $this->assertEquals($result->status, TRUE);
        $this->assertEquals($result->results[0]->StatusMessage == "Data Extension created.", TRUE);
        return $result->results[0];
    }

    /**
    * @depends testCanCreateDataExtension
    */
    public function testCanGetDataExtension($dataextension)
    {
        $getDE = $this->getDataExtension($dataextension->Object->CustomerKey);
        //make sure the get was successful
        $this->assertEquals($getDE->status, TRUE);
        //compare the content area name
        $this->assertEquals($getDE->results[0]->Name == $dataextension->Object->Name, TRUE);
        return $getDE->results[0];
    }

     /**
    * @depends testCanGetDataExtension
    */
    public function testCanUpdateDataExtension($dataextension)
    {
        $newDEName = "Updated DE Name";
        $updatedDE = $this->updateDataExtension($dataextension,$newDEName);
        $getDE = $this->getDataExtension($dataextension->CustomerKey);
        $this->assertEquals($getDE->results[0]->Name == $newDEName, TRUE);
        return $dataextension;
    }

    /**
    * @depends testCanUpdateDataExtension
    */
    public function testCanDeleteDataExtension($dataextension)
    {
        $result = $this->deleteDataExtension($dataextension);
        $this->assertEquals($result->status, TRUE);

    }

    public function createDataExtension()
    {
        $dataextension = new ET_DataExtension();
        $dataextension->authStub = $this->client;
        $dataextension->props = array("Name" => "SDKDataExtension".uniqid(), "Description" => "SDK Created Data Extension", "CustomerKey" => "CustKey".uniqid());
        $dataextension->columns = array();
        $dataextension->columns[] = array("Name" => "Key", "FieldType" => "Text", "IsPrimaryKey" => "true","MaxLength" => "100", "IsRequired" => "true");
        $dataextension->columns[] = array("Name" => "Value", "FieldType" => "Text");
        return $dataextension->post();

    }

    public function getDataExtension($customerkey)
    {
        $dataextension = new ET_DataExtension();
        $dataextension->authStub = $this->client;
        $dataextension->props = array("Name","Description","CustomerKey","ObjectID");
        $dataextension->filter= array("Property"=>"CustomerKey", "SimpleOperator"=>"equals","Value"=>$customerkey);

        return $dataextension->get();
    }

   
    public function updateDataExtension($de,$updatedName)
    {
        $dataextension = new ET_DataExtension();
        $dataextension->authStub = $this->client;
        $dataextension->props = array("CustomerKey" => $de->CustomerKey, "Name"=> $updatedName);
        $dataextension->columns = array();
        return $dataextension->patch();
    }

    public function deleteDataExtension($de)
    {
        $dataextension = new ET_DataExtension();
        $dataextension->authStub = $this->client;
        $dataextension->props = array("ObjectID" => $de->ObjectID);
       
        return $dataextension->delete();
    }



}

?>