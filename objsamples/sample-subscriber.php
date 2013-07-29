<?php

require('../ET_Client.php');
try {	
	$myclient = new ET_Client();

	// NOTE: These examples only work in accounts where the SubscriberKey functionality is not enabled
	//       SubscriberKey will need to be included in the props if that feature is enabled
	
	$SubscriberTestEmail = "PHPSDKExample@bh.exacttarget.com";
	
	// Create Subscriber
	print "Create Subscriber \n";
	$subCreate = new ET_Subscriber();
	$subCreate->authStub = $myclient;
	$subCreate->props = array("EmailAddress" => $SubscriberTestEmail);
	$postResult = $subCreate->post();
	print_r('Post Status: '.($postResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResult->code."\n";
	print 'Message: '.$postResult->message."\n";	
	print 'Results Length: '. count($postResult->results)."\n";
	print 'Results: '."\n";
	print_r($postResult->results);
	print "\n---------------\n";

	// Retrieve newly created Subscriber
	print "Retrieve newly created Subscriber \n";
	$retSub = new ET_Subscriber();
	$retSub->authStub = $myclient;
	$retSub->filter = array('Property' => 'SubscriberKey','SimpleOperator' => 'equals','Value' => $SubscriberTestEmail);
	$retSub->props = array("SubscriberKey", "EmailAddress", "Status");
	$getResult = $retSub->get();
	print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResult->code."\n";
	print 'Message: '.$getResult->message."\n";
	print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResult->results)."\n";
	print 'Results: '."\n";
	print_r($getResult->results);
	print "\n---------------\n";

	// Update Subscriber to unsubscribed
	print "Updates Subscriber to unsubscribed\n";
	$subPatch = new ET_Subscriber();
	$subPatch->authStub = $myclient;
	$subPatch->props = array("EmailAddress" => $SubscriberTestEmail, "Status" => "Unsubscribed");
	$patchResult = $subPatch->patch();
	print_r('Patch Status: '.($patchResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$patchResult->code."\n";
	print 'Message: '.$patchResult->message."\n";	
	print 'Results Length: '. count($patchResult->results)."\n";
	print 'Results: '."\n";
	print_r($patchResult->results);
	print "\n---------------\n";

	// Retrieve Subscriber that should have status unsubscribed now
	print "Retrieve Subscriber that should have status unsubscribed now \n";
	$retSub = new ET_Subscriber();
	$retSub->authStub = $myclient;
	$retSub->filter = array('Property' => 'SubscriberKey','SimpleOperator' => 'equals','Value' => $SubscriberTestEmail);
	$retSub->props = array("SubscriberKey", "EmailAddress", "Status");
	$getResult = $retSub->get();
	print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResult->code."\n";
	print 'Message: '.$getResult->message."\n";
	print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResult->results)."\n";
	print 'Results: '."\n";
	print_r($getResult->results);
	print "\n---------------\n";
	
	// Delete Subscriber
	print "Delete Subscriber \n";
	$subDelete = new ET_Subscriber();
	$subDelete->authStub = $myclient;
	$subDelete->props = array("EmailAddress" => $SubscriberTestEmail);
	$deleteResult = $subDelete->delete();
	print_r('Delete Status: '.($deleteResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$deleteResult->code."\n";
	print 'Message: '.$deleteResult->message."\n";	
	print 'Results Length: '. count($deleteResult->results)."\n";
	print 'Results: '."\n";
	print_r($deleteResult->results);
	print "\n---------------\n";
	
	// Retrieve Subscriber to confirm deletion
	print "Retrieve Subscriber to confirm deletion \n";
	$retSub = new ET_Subscriber();
	$retSub->authStub = $myclient;
	$retSub->filter = array('Property' => 'SubscriberKey','SimpleOperator' => 'equals','Value' => $SubscriberTestEmail);
	$retSub->props = array("SubscriberKey", "EmailAddress", "Status");
	$getResult = $retSub->get();
	print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResult->code."\n";
	print 'Message: '.$getResult->message."\n";
	print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResult->results)."\n";
	print 'Results: '."\n";
	print_r($getResult->results);
	print "\n---------------\n";

	// Do not run the "Retrieve All Subscribers" request for testing if you have more than 100,000 records in your account as it will take a long time to complete.
	/*
	// Retrieve All Subcribers with GetMoreResults
	print "Retrieve All Subcribers with GetMoreResults \n";
	$getSub = new ET_Subscriber();
	$getSub->authStub = $myclient;
	$getSub->props = array("SubscriberKey", "EmailAddress", "Status");
	$getResult = $getSub->get();
	print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResult->code."\n";
	print 'Message: '.$getResult->message."\n";
	print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResult->results)."\n";
	print "\n---------------\n";
	
	while ($getResult->moreResults) {
		print "Continue Retrieve All Subcribers with GetMoreResults \n";
		$getResult = $getSub->GetMoreResults();
		print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResult->code."\n";
		print 'Message: '.$getResult->message."\n";
		print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
		print 'Results Length: '. count($getResult->results)."\n";
		print "\n---------------\n";
	}	
	*/
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



