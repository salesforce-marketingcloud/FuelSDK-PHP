<?php
namespace FuelSdk\Test;

use FuelSdk\ET_Client;
use FuelSdk\ET_Campaign;
use PHPUnit\Framework\TestCase;

/**
* @covers ET_Campaign
*/
final class CampaignTest extends TestCase
{
    private $client;

    function __construct()
    {
        $this->client = new ET_Client(true);
    }
    public function testCanCreateCampaign()
    {
        $result = $this->createCampaign();

        $this->assertEquals($result->status, TRUE);

    }

    public function testCanGetCampaign()
    {
        //first create a campaign
        $campaign = $this->createCampaign("Get campaign test ".uniqid());
        //get the newly created campaign
        $getCampaign = $this->getCampaign($campaign->results->id);
        //compare the name of the campaign
        $this->assertEquals($getCampaign->results->name == $campaign->results->name, TRUE);
    }

    public function testCanDeleteCampaign()
    {
        //first create a campaign
        $campaign = $this->createCampaign("Delete campaign test ".uniqid());
        echo "\n";
        echo json_encode($campaign);
        //delete the newly created campaign
        $deleteCampaign = $this->deleteCampaign($campaign->results->id);
        echo json_encode($deleteCampaign);
        //get the newly created campaign
        $getCampaign = $this->getCampaign($campaign->results->id);
        echo json_encode($getCampaign);
        //compare the name of the campaign
        $this->assertEquals($getCampaign->results == $campaign->results, FALSE);
    }    

    public function testCanUpdateCampaign()
    {
        //first create a campaign
        $campaign = $this->createCampaign("Update campaign test ".uniqid());
        echo "\n";
        print_r($campaign);
        //update the newly created campaign
        $updateCampaign = $this->updateCampaign($campaign->results->id);
        print_r($updateCampaign);
        //get the newly created campaign
        $getCampaign = $this->getCampaign($campaign->results->id);
        echo "the get campaign:\n";
        print_r($getCampaign);
        //compare the name of the campaign
        $this->assertEquals($getCampaign->results->description == $updateCampaign->results->description, TRUE);
    }    

    public function updateCampaign($id)
    {
        $desc = "chaning the description";
        $campaign = new ET_Campaign();
        $auth = $this->client;
        $campaign->authStub = $auth;
        $campaign->props["id"] = $id;
        $campaign->props["description"] = $desc;

        $result = $campaign->patch();

        return $result;
    }

    public function deleteCampaign($id)
    {
        $campaign = new ET_Campaign();
        $auth = $this->client;
        $campaign->authStub = $auth;
        $campaign->props["id"] = $id;

        $result = $campaign->delete();

        return $result;
    }

    public function getCampaign($id)
    {
        $campaign = new ET_Campaign();
        $auth = $this->client;
        $campaign->authStub = $auth;
        $campaign->props["id"] = $id;

        $result = $campaign->get();

        return $result;
    }

    public function createCampaign($name = "")
    {
        $campaign = new ET_Campaign();
        $auth = $this->client;

        $campaign->authStub = $auth;
        if($name == "")
        {
            $name = "PHP SDK Test ".uniqid();
        }
        $campaign->props["name"] = $name;
        $campaign->props["description"] = $name;
        $campaign->props["isFavorite"] = false;
        $campaign->props["campaignOwner"]="2de648d5-4bdd-444d-9ce8-e2f08bddb567";
        $campaign->props["campaignOwnerName"] = "Campaign Manager";
        $campaign->props["campaignStatus"] = "InProcess";
        $campaign->props["campaignCode"] = "PHP SDK Test";
        $campaign->props["campaignFolderID"] = 0;

        $result = $campaign->post();
        return $result;
    }


}