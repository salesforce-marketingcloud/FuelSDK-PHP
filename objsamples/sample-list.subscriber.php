<?php

require('../ET_Client.php');
try {
	
	// NOTE: These examples only work in accounts where the SubscriberKey functionality is not enabled
	//       SubscriberKey will need to be included in the props if that feature is enabled
	
	$NewListName = "PHPSDKListSubscriber";
	$SubscriberTestEmail = "PHPSDKListSubscriber@bh.exacttarget.com";
	$myclient = new ET_Client();

	
	// Create List
	print "Create List \n";
	$postContent = new ET_List();
	$postContent->authStub = $myclient;
	$postContent->props = array("ListName" => $NewListName, "Description" => "This list was created with the RubySDK", "Type" => "Private");
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
		// Create Subscriber on List
		print "Create Subscriber on List \n";
		$subCreate = new ET_Subscriber();
		$subCreate->authStub = $myclient;
		$subCreate->props = array("EmailAddress" => $SubscriberTestEmail, "Lists" => array("ID" => $newListID));
		$postResult = $subCreate->post();
		print_r('Post Status: '.($postResult->status ? 'true' : 'false')."\n");
		print 'Code: '.$postResult->code."\n";
		print 'Message: '.$postResult->message."\n";	
		print 'Results Length: '. count($postResult->results)."\n";
		print 'Results: '."\n";
		print_r($postResult->results);
		print "\n---------------\n";
		
		if (!$postResult->status) {
			// If the subscriber already exists in the account then we need to do an update.
			// Update Subscriber On List 
			if ($postResult->results[0]->ErrorCode == "12014"){
				// Update Subscriber to add to List
				print "Update Subscriber to add to List \n";
				$subPatch = new ET_Subscriber();
				$subPatch->authStub = $myclient;
				$subPatch->props = array("EmailAddress" => $SubscriberTestEmail, "Lists" => array("ID" => $newListID));
				$patchResult = $subPatch->patch();
				print_r('Patch Status: '.($patchResult->status ? 'true' : 'false')."\n");
				print 'Code: '.$patchResult->code."\n";
				print 'Message: '.$patchResult->message."\n";	
				print 'Results Length: '. count($patchResult->results)."\n";
				print 'Results: '."\n";
				print_r($patchResult->results);
				print "\n---------------\n";
			}			
		}

		// Retrieve all Subscribers on the List
		print "Retrieve all Subscribers on the List \n";
		$getList = new ET_List_Subscriber();
		$getList->authStub = $myclient;
		$getList->filter = array('Property' => 'ListID','SimpleOperator' => 'equals','Value' => $newListID);
		$getList->props = array("ObjectID","SubscriberKey","CreatedDate","Client.ID","Client.PartnerClientKey","ListID","Status");
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
	}
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



