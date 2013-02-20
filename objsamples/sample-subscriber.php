<?php

require('../ETClient.php');
try {	
	$params = array();		
	$myclient = new ETClient(true, $params);

	// Retrieve all Subscribers
	print "Retrieve All Subcribers with GetMoreResults \n";
	$sub = new ET_Subscriber();
	$sub->authStub = $myclient;
	$sub->props = array("SubscriberKey", "EmailAddress", "Status");
	$getResult = $sub->get();
	print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResult->code."\n";
	print 'Message: '.$getResult->message."\n";
	print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResult->results)."\n";
	print "\n---------------\n";

	while ($getResult->moreResults) {
		$getResult = $sub->GetMoreResults();
		print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResult->code."\n";
		print 'Message: '.$getResult->message."\n";
		print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
		print 'Results Length: '. count($getResult->results)."\n";
		print "\n---------------\n";
	}

	$testEmail = "PHPSDKExample@bh.exacttarget.com";
	
	// Create Subscriber
	print "Create Subscriber \n";
	$subCreate = new ET_Subscriber();
	$subCreate->authStub = $myclient;
	$subCreate->props = array("EmailAddress" => $testEmail);
	$createResult = $subCreate->post();
	print_r('Post Status: '.($createResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$createResult->code."\n";
	print 'Message: '.$createResult->message."\n";	
	print 'Results Length: '. count($createResult->results)."\n";
	print 'Results: '."\n";
	print_r($createResult->results);
	print "\n---------------\n";

	// Retrieve Subscribers
	print "Retrieve newly created Subscriber \n";
	$retSub = new ET_Subscriber();
	$retSub->authStub = $myclient;
	$retSub->filter = array('Property' => 'SubscriberKey','SimpleOperator' => 'equals','Value' => $testEmail);
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

	// Update Subscriber
	print "Updates Subscriber to held\n";
	$subPatch = new ET_Subscriber();
	$subPatch->authStub = $myclient;
	$subPatch->props = array("EmailAddress" => $testEmail, "Status" => "Held");
	$patchResult = $subPatch->patch();
	print_r('Patch Status: '.($patchResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$patchResult->code."\n";
	print 'Message: '.$patchResult->message."\n";	
	print 'Results Length: '. count($patchResult->results)."\n";
	print 'Results: '."\n";
	print_r($patchResult->results);
	print "\n---------------\n";

	// Retrieve Subscribers
	print "Retrieve Subscriber that should have status held now \n";
	$retSub = new ET_Subscriber();
	$retSub->authStub = $myclient;
	$retSub->filter = array('Property' => 'SubscriberKey','SimpleOperator' => 'equals','Value' => $testEmail);
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
	$subDelete->props = array("EmailAddress" => $testEmail);
	$deleteResult = $subDelete->delete();
	print_r('Patch Status: '.($deleteResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$deleteResult->code."\n";
	print 'Message: '.$deleteResult->message."\n";	
	print 'Results Length: '. count($deleteResult->results)."\n";
	print 'Results: '."\n";
	print_r($deleteResult->results);
	print "\n---------------\n";
	
	// Retrieve Subscribers
	print "Retrieve Subscriber to confirm deletion \n";
	$retSub = new ET_Subscriber();
	$retSub->authStub = $myclient;
	$retSub->filter = array('Property' => 'SubscriberKey','SimpleOperator' => 'equals','Value' => $testEmail);
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
	
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



