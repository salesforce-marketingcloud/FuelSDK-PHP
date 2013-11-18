<?php

require('../ET_Client.php');
try {
	
	$NewListName = "PHPSDKList";
	$myclient = new ET_Client();

	// Retrieve All List with GetMoreResults
	print "Retrieve All List with GetMoreResults \n";
	$getList = new ET_List();
	$getList->authStub = $myclient;
	$getList->props = array("ID","PartnerKey","CreatedDate","ModifiedDate","Client.ID","Client.PartnerClientKey","ListName","Description","Category","Type","CustomerKey","ListClassification","AutomatedEmail.ID");
	$getResponse = $getList->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print "\n---------------\n";
	
	while ($getResponse->moreResults) {
		print "Continue Retrieve All List with GetMoreResults \n";
		$getResponse = $getList->GetMoreResults();
		print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResponse->code."\n";
		print 'Message: '.$getResponse->message."\n";
		print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
		print 'Results Length: '. count($getResponse->results)."\n";
		print "\n---------------\n";
	}	
	
	$NameOfTestList = "PHPSDKList";
	
	// Create List
	print "Create List \n";
	$postContent = new ET_List();
	$postContent->authStub = $myclient;
	$postContent->props = array("ListName" => $NewListName, "Description" => "This list was created with the PHPSDK", "Type" => "Private");
	$postResponse = $postContent->post();
	print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResponse->code."\n";
	print 'Message: '.$postResponse->message."\n";	
	print 'Results Length: '. count($postResponse->results)."\n";
	print 'Results: '."\n";
	print_r($postResponse->results);
	print "\n---------------\n";
	
	if ($postResponse->status){

		$newListID = $postResponse->results[0]->NewID;
	
		// Retrieve newly created List
		print "Retrieve newly created List \n";
		$getList = new ET_List();
		$getList->authStub = $myclient;
		$getList->filter = array('Property' => 'ID','SimpleOperator' => 'equals','Value' => $newListID);
		$getList->props = array("ID","PartnerKey","CreatedDate","ModifiedDate","Client.ID","Client.PartnerClientKey","ListName","Description","Category","Type","CustomerKey","ListClassification","AutomatedEmail.ID");
		$getResponse = $getList->get();
		print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResponse->code."\n";
		print 'Message: '.$getResponse->message."\n";
		print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
		print 'Results Length: '. count($getResponse->results)."\n";
		print 'Results: '."\n";
		print_r($getResponse->results);
		print "\n---------------\n";

		// Update the List
		print "Update the List \n";
		$patchList = new ET_List();
		$patchList->authStub = $myclient;
		$patchList->props = array("ID" => $newListID,  "Description"=>"This list was created with the PHPSDK!!!");
		$patchResult = $patchList->patch();
		print_r('Patch Status: '.($patchResult->status ? 'true' : 'false')."\n");
		print 'Code: '.$patchResult->code."\n";
		print 'Message: '.$patchResult->message."\n";	
		print 'Results Length: '. count($patchResult->results)."\n";
		print 'Results: '."\n";
		print_r($patchResult->results);
		print "\n---------------\n";

		// Retrieve Updated List
		print "Retrieve Updated List \n";
		$getList = new ET_List();
		$getList->authStub = $myclient;
		$getList->filter = array('Property' => 'ID','SimpleOperator' => 'equals','Value' => $newListID);
		$getList->props = array("ID","PartnerKey","CreatedDate","ModifiedDate","Client.ID","Client.PartnerClientKey","ListName","Description","Category","Type","CustomerKey","ListClassification","AutomatedEmail.ID");
		$getResponse = $getList->get();
		print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResponse->code."\n";
		print 'Message: '.$getResponse->message."\n";
		print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
		print 'Results Length: '. count($getResponse->results)."\n";
		print 'Results: '."\n";
		print_r($getResponse->results);
		print "\n---------------\n";
		
		// Delete List
		print "Delete List \n";
		$deleteList = new ET_List();
		$deleteList->authStub = $myclient;
		$deleteList->props = array("ID" => $newListID);
		$deleteResponse = $deleteList->delete();
		print_r('Delete Status: '.($deleteResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$deleteResponse->code."\n";
		print 'Message: '.$deleteResponse->message."\n";	
		print 'Results Length: '. count($deleteResponse->results)."\n";
		print 'Results: '."\n";
		print_r($deleteResponse->results);
		print "\n---------------\n";
		
		// Retrieve List to confirm deletion
		print "Retrieve List to confirm deletion \n";
		$getList = new ET_List();
		$getList->authStub = $myclient;
		$getList->filter = array('Property' => 'ID','SimpleOperator' => 'equals','Value' => $newListID);
		$getList->props = array("ID","PartnerKey","CreatedDate","ModifiedDate","Client.ID","Client.PartnerClientKey","ListName","Description","Category","Type","CustomerKey","ListClassification","AutomatedEmail.ID");
		$getResponse = $getList->get();
		print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResponse->code."\n";
		print 'Message: '.$getResponse->message."\n";
		print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
		print 'Results Length: '. count($getResponse->results)."\n";
		print 'Results: '."\n";
		print_r($getResponse->results);
		print "\n---------------\n";
	}
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



