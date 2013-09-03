<?php

require('../ET_Client.php');
try {	

	# In order for this sample to run, it needs to have an asset that it can associate the campaign to
	$ExampleAssetType = "LIST";
	$ExampleAssetItemID = "1953114";
	
	$myclient = new ET_Client();
	
	// Retrieve All Campaigns with GetMoreResults
	print "Retrieve All Campaigns with GetMoreResults \n";
	$getCamp = new ET_Campaign();
	$getCamp->authStub = $myclient;
	$getResult = $getCamp->get();
	print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResult->code."\n";
	print 'Message: '.$getResult->message."\n";
	print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
	print 'Results Length(Items): '. count($getResult->results->items)."\n";
	//print 'Results: "\n"';
	//print_r($getResult->results);
	print "\n---------------\n";
	
	
	while ($getResult->moreResults) {
		print "Continue Retrieve All Campaigns with GetMoreResults \n";
		$getResult = $getCamp->GetMoreResults();
		print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResult->code."\n";
		print 'Message: '.$getResult->message."\n";
		print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
		print 'Results Length(Items): '. count($getResult->results->items)."\n";
		print "\n---------------\n";
	}	 

	// Create a new Campaign
	print "Create a new Campaign \n";
	$postCamp = new ET_Campaign();
	$postCamp->authStub = $myclient;
	$postCamp->props = array("name" => "CampaignForPHPSDK", "description"=> "CampaignForPHPSDK", "color"=>"FF9933", "favorite"=>"false");	
	$postResponse = $postCamp->Post();
	print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResponse->code."\n";
	print 'Message: '.$postResponse->message."\n";
	print 'Results Length: '. count($postResponse->results)."\n";
	print 'Results: '."\n";
	print_r($postResponse->results);
	print "\n---------------\n";

	

	if ($postResponse->status) {
		
		$IDOfpostCampaign = $postResponse->results->id;
		
		// Retrieve the new Campaign
		print "Retrieve the new Campaign \n";
		$getCamp = new ET_Campaign();
		$getCamp->authStub = $myclient;
		$getCamp->props = array("id" => $IDOfpostCampaign);	
		$getResult = $getCamp->Get();
		print_r('Retrieve Status: '.($getResult->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResult->code."\n";
		print 'Message: '.$getResult->message."\n";
		print 'Results Length: '. count($getResult->results)."\n";
		print 'Results: '."\n";
		print_r($getResult->results);
		print "\n---------------\n";	
		
		
		// Update a Campaign
		print "Update a Campaign \n";
		$patchCamp = new ET_Campaign();
		$patchCamp->authStub = $myclient;
		$patchCamp->props = array("id" => $IDOfpostCampaign,  "name" => "CampaignForPHPSDK Updated!");	
		$patchResponse = $patchCamp->Patch();
		print_r('Patch Status: '.($patchResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$patchResponse->code."\n";
		print 'Message: '.$patchResponse->message."\n";
		print 'Results Length: '. count($patchResponse->results)."\n";
		print 'Results: '."\n";
		print_r($patchResponse->results);
		print "\n---------------\n";
		
		
		// Retrieve the updated Campaign
		print "Retrieve the updated Campaign \n";
		$getCamp = new ET_Campaign();
		$getCamp->authStub = $myclient;
		$getCamp->props = array("id" => $IDOfpostCampaign);	
		$getResult = $getCamp->Get();
		print_r('Retrieve Status: '.($getResult->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResult->code."\n";
		print 'Message: '.$getResult->message."\n";
		print 'Results Length: '. count($getResult->results)."\n";
		print 'Results: '."\n";
		print_r($getResult->results);
		print "\n---------------\n";
		
		// Create a new Campaign Asset
		print "Create a new Campaign Asset \n";
		$postCampAsset = new ET_Campaign_Asset();
		$postCampAsset->authStub = $myclient;
		$postCampAsset->props = array("id" => $IDOfpostCampaign, "ids"=> array($ExampleAssetItemID), "type"=> $ExampleAssetType);	
		$postResponse = $postCampAsset->Post();
		print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$postResponse->code."\n";
		print 'Message: '.$postResponse->message."\n";
		print 'Results Length: '. count($postResponse->results)."\n";
		print 'Results: '."\n";
		print_r($postResponse->results);
		print "\n---------------\n";
		
		
		if  ($postResponse->status){
			$IDOfpostCampaignAsset = $postResponse->results[0]->id;
			
			// Retrieve all Campaign Asset for a campaign
			print "Retrieve all Campaign Asset for a campaign \n";
			$getCampAsset = new ET_Campaign_Asset();
			$getCampAsset->authStub = $myclient;
			$getCampAsset->props = array("id" => $IDOfpostCampaign);	
			$getResult = $getCampAsset->Get();
			print_r('Retrieve Status: '.($getResult->status ? 'true' : 'false')."\n");
			print 'Code: '.$getResult->code."\n";
			print 'Message: '.$getResult->message."\n";
			print 'Results Length: '. count($getResult->results)."\n";
			print 'Results: '."\n";
			print_r($getResult->results);
			print "\n---------------\n";	
			
			// Retrieve a single new Campaign Asset
			print "Retrieve a single new Campaign Asset \n";
			$getCampAsset = new ET_Campaign_Asset();
			$getCampAsset->authStub = $myclient;
			$getCampAsset->props = array("id" => $IDOfpostCampaign, "assetId" => $IDOfpostCampaignAsset);	
			$getResult = $getCampAsset->Get();
			print_r('Retrieve Status: '.($getResult->status ? 'true' : 'false')."\n");
			print 'Code: '.$getResult->code."\n";
			print 'Message: '.$getResult->message."\n";
			print 'Results Length: '. count($getResult->results)."\n";
			print 'Results: '."\n";
			print_r($getResult->results);
			print "\n---------------\n";
			
			//Delete the new Campaign Asset
			print "Delete the new Campaign Asset\n";
			$deleteCampAsset = new ET_Campaign_Asset();
			$deleteCampAsset->authStub = $myclient;
			$deleteCampAsset->props = array("id" => $IDOfpostCampaign, "assetId" => $IDOfpostCampaignAsset);	
			$deleteResult = $deleteCampAsset->Delete();
			print_r('Delete Status: '.($deleteResult->status ? 'true' : 'false')."\n");
			print 'Code: '.$deleteResult->code."\n";
			print 'Message: '.$deleteResult->message."\n";
			print 'Results Length: '. count($deleteResult->results)."\n";
			print 'Results: '."\n";
			print_r($deleteResult->results);
			print "\n---------------\n";	
			}
		
		// Delete a Campaign
		print "Delete a Campaign \n";
		$deleteCamp = new ET_Campaign();
		$deleteCamp->authStub = $myclient;
		$deleteCamp->props = array("id" => $IDOfpostCampaign);	
		$deleteResponse = $deleteCamp->Delete();
		print_r('Delete Status: '.($deleteResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$deleteResponse->code."\n";
		print 'Message: '.$deleteResponse->message."\n";
		print 'Results Length: '. count($deleteResponse->results)."\n";
		print 'Results: '."\n";
		print_r($deleteResponse->results);
		print "\n---------------\n";			
	}
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



