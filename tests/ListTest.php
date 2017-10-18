<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_List;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_List
*/
final class ListTest extends TestCase
{
    private $client;
    

    function __construct()
    {
        $this->client = new ET_Client(true);
    }

    public function testCanCreateList()
    {
        $result = $this->createList();
        $this->assertEquals($result->status, TRUE);
        $this->assertEquals($result->results[0]->StatusMessage == "Created List.", TRUE);
        return $result->results[0];
    }

    /**
    * @depends testCanCreateList
    */
    public function testCanGetList($list)
    {
        $getlist = $this->getList($list->NewID);
        //make sure the get was successful
        $this->assertEquals($getlist->status, TRUE);
        //compare the Name of the list
        $this->assertEquals($getlist->results[0]->ListName == $list->Object->ListName, TRUE);
        return $getlist->results[0];
    }

     /**
    * @depends testCanGetList
    */
    public function testCanUpdateList($list)
    {
        $newName = "Updated List Name";
        $updatedList = $this->updateList($list,$newName);
        $this->assertEquals($updatedList->status, TRUE);
        $this->assertEquals($updatedList->results[0]->StatusMessage == "Updated List.", TRUE);
        $getlist = $this->getList($list->ID);

        $this->assertEquals($getlist->results[0]->ListName == $newName, TRUE);
        return $list;
    }

    /**
    * @depends testCanUpdateList
    */
    public function testCanDeleteList($list)
    {
        $result = $this->deleteList($list);
        $this->assertEquals($result->status, TRUE);
        $this->assertEquals($result->results[0]->StatusMessage == "List deleted", TRUE);

    }

    public function testCanUpsertList()
    {
        $list = new ET_List();
        $list->props = array("ListName" => "UpsertedPHPSDKList".uniqid(), "Description" => "SDK Created List".uniqid());
        //call upsert to create a new list.
        $result = $this->upsertList($list);
        $this->assertEquals($result->status, TRUE);
        $this->assertEquals($result->results[0]->StatusMessage == "Updated List.", TRUE);

        //try to get the list we created above
        $getlist = $this->getList($result->results[0]->Object->ID);
        //make sure the get was successful
        $this->assertEquals($getlist->status, TRUE);
        //call the upsert again ... but this time we are going to update the existing one by passing ID field populated
        $list->props = array("ID" => $getlist->results[0]->ID, "ListName" => "UpsertedPHPSDKList".uniqid(), "Description" => "SDK Created List".uniqid());
        $result = $this->upsertList($list);
        $this->assertEquals($result->status, TRUE);
        $this->assertEquals($result->results[0]->StatusMessage == "Updated List.", TRUE);
        
        //delete the lsit
        $result = $this->deleteList($getlist->results[0]);

        $this->assertEquals($result->status, TRUE);
        $this->assertEquals($result->results[0]->StatusMessage == "List deleted", TRUE);


    }

    public function createList()
    {
        $list = new ET_List();
        $list->authStub = $this->client;

        $list->props = array("ListName" => "PHPSDKList".uniqid(), "Description" => "SDK Created List".uniqid());

        return $list->post();

    }

    public function upsertList($list)
    {
        $list->authStub = $this->client;

        return $list->put();

    }

    public function getList($listId)
    {
        $list = new ET_List();
        $list->authStub = $this->client;
        $list->filter= array("Property"=>"ID", "SimpleOperator"=>"equals","Value"=>$listId);
        return $list->get();
    }

    public function updateList($getlist, $newName)
    {
        
        $list = new ET_List();
        $list->authStub = $this->client;
        $list->props["ID"] = $getlist->ID;
        $list->props["ListName"] = $newName;

        return $list->patch();
    }

    public function deleteList($getlist)
    {
        $list = new ET_List();
        $list->authStub = $this->client;
        $list->props["ID"] = $getlist->ID;

        return $list->delete();
    }

}

?>