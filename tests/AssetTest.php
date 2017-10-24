<?php
namespace FuelSdk\Test;
use FuelSdk\ET_Client;
use FuelSdk\ET_Asset;
use PHPUnit\Framework\TestCase;
/**
* @covers ET_Asset
*/
final class AssetTest extends TestCase
{
    private $myclient;

    function __construct()
    {
        $this->myclient = new ET_Client(true);
    }

    public function testCanCreateAsset()
    {
        $result = $this->createAsset();
        $this->assertEquals($result->status, TRUE);
    }

    public function testCanGetAsset()
    {
        //first create a asset
        $asset = $this->createAsset("Get asset test ".uniqid());
        echo "asset object: \n";
        print_r($asset);
        //get the newly created asset
        $getAsset = $this->getAsset($asset->results->mediaItem->mediaItemID);
        //compare the name of the asset
        $this->assertEquals($getAsset->results->items[0]->fileURL == $asset->results->mediaItem->fileURL, TRUE);
    }

    public function testCanNotDeleteAsset()
    {
        //first create a asset
        $asset = new ET_Asset();
        $this->assertNull($asset->delete());
    }    

    public function testCanNotUpdateAsset()
    {
        //first create a asset
        $asset = new ET_Asset();
        $this->assertNull($asset->patch());
    }    


    public function updateAsset($id)
    {
        $desc = "chaning the description";
        $asset = new ET_Asset();
        $auth = $this->myclient;
        $asset->authStub = $auth;
        $asset->props["mediaItemID"] = $id;
        $asset->props["description"] = $desc;

        $result = $asset->post();

        return $result;
    }

    public function getAsset($id)
    {
        // Retrieve All Assets with GetMoreResults
/*        print "Retrieve All Assets with GetMoreResults \n";
        $getAsset = new ET_Asset();
        $getAsset->authStub = $myclient;
        $getResult = $getAsset->get();
        print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
        print 'Code: '.$getResult->code."\n";
        print 'Message: '.$getResult->message."\n";
        print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
        print 'Results Length(Items): '. count($getResult->results->items)."\n";

        print 'Results: "\n"';
        print_r($getResult);
        print "\n---------------\n";
*/
        $asset = new ET_Asset();
        $auth = $this->myclient;
        $asset->authStub = $auth;
        $asset->props["mediaItemID"] = $id;

        $result = $asset->get();


        print 'Results: "\n"';
        print_r($result);
        
        return $result;
    }

    public function createAsset($name = "")
    {
        $base64EncodedString = base64_encode("This is my file contents of a text file");
        if($name == "")
            $name = "PHP SDK Asset Test ".uniqid();

        // Create a new Asset Base 64
        print "Create a new Asset Base 64 \n";
        $postAsset = new ET_Asset();
        $postAsset->authStub = $this->myclient;
        $postAsset->props = array( "name" => $name, 
            "fileName" => "TestFile.txt", "mimeType" => "text/plain", "fileData" => $base64EncodedString, 
            "displayName" => "TestFile.txt", "customerKey" => md5(uniqid()), "description" => "Test description");
        $postResponse = $postAsset->Post();

        print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
        print 'Code: '.$postResponse->code."\n";
        print 'Message: '.$postResponse->message."\n";
        print 'Results Length: '. count($postResponse->results)."\n";
        
        print 'Results: '."\n";
        print_r($postResponse);
        print "\n---------------\n";

        return $postResponse;

    }


}