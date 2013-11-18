<?php

require('../ET_Client.php');
try {	
	$myclient = new ET_Client();

	// Modify the date below to reduce the number of results returned from the request
	// Setting this too far in the past could result in a very large response size
	
	$retrieveDate = "2012-01-15T13:00:00.000";
	
	// Retrieve Filtered UnsubEvent with GetMoreResults
	print "Retrieve Filtered UnsubEvent with GetMoreResults \n";
	$getUnsubEvent = new ET_UnsubEvent();
	$getUnsubEvent->authStub = $myclient;
	$getUnsubEvent->props = array("SendID","SubscriberKey","EventDate","Client.ID","EventType","BatchID","TriggeredSendDefinitionObjectID","PartnerKey");
	$getUnsubEvent->filter = array('Property' => 'EventDate','SimpleOperator' => 'greaterThan','DateValue' => $retrieveDate);
	$getUnsubEvent->getSinceLastBatch = false;
	$getResponse = $getUnsubEvent->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print "\n---------------\n";
	
	while ($getResponse->moreResults) {
		print "Continue Retrieve All UnsubEvent with GetMoreResults \n";
		$getResponse = $getUnsubEvent->GetMoreResults();
		print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResponse->code."\n";
		print 'Message: '.$getResponse->message."\n";
		print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
		print 'Results Length: '. count($getResponse->results)."\n";
		print "\n---------------\n";
	}
	
	
	// The following request could potentially bring back large amounts of data if run against a production account		
	
	/*
	// Retrieve All UnsubEvent with GetMoreResults
	print "Retrieve All UnsubEvent with GetMoreResults \n";
	$getUnsubEvent = new ET_UnsubEvent();
	$getUnsubEvent->authStub = $myclient;
	$getUnsubEvent->props = array("SendID","SubscriberKey","EventDate","Client.ID","EventType","BatchID","TriggeredSendDefinitionObjectID","PartnerKey");	
	$getResponse = $getUnsubEvent->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print "\n---------------\n";
	
	while ($getResponse->moreResults) {
	print "Continue Retrieve All UnsubEvent with GetMoreResults \n";
	$getResponse = $getUnsubEvent->GetMoreResults();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print "\n---------------\n";
	}	
	*/
	
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



