<?php

require('../ET_Client.php');
try {	
	$myclient = new ET_Client();

	// Modify the date below to reduce the number of results returned from the request
	// Setting this too far in the past could result in a very large response size
	
	$retrieveDate = "2015-01-15T13:00:00.000";
	
	// Retrieve Filtered Send with GetMoreResults
	print "Retrieve Filtered Send with GetMoreResults \n";
	$getSend = new ET_Send();
	$getSend->authStub = $myclient;
	$getSend->props = array("ID","PartnerKey","CreatedDate","ModifiedDate","Client.ID","Client.PartnerClientKey","Email.ID","Email.PartnerKey","SendDate","FromAddress","FromName","Duplicates","InvalidAddresses","ExistingUndeliverables","ExistingUnsubscribes","HardBounces","SoftBounces","OtherBounces","ForwardedEmails","UniqueClicks","UniqueOpens","NumberSent","NumberDelivered","NumberTargeted","NumberErrored","NumberExcluded","Unsubscribes","MissingAddresses","Subject","PreviewURL","SentDate","EmailName","Status","IsMultipart","SendLimit","SendWindowOpen","SendWindowClose","IsAlwaysOn","Additional","BCCEmail","EmailSendDefinition.ObjectID","EmailSendDefinition.CustomerKey");
	$getSend->filter = array('Property' => 'SendDate','SimpleOperator' => 'greaterThan','DateValue' => $retrieveDate);
	$getSend->getSinceLastBatch = false;
	$getResponse = $getSend->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print "\n---------------\n";
	
	while ($getResponse->moreResults) {
		print "Continue Retrieve Send with GetMoreResults \n";
		$getResponse = $getSend->GetMoreResults();
		print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResponse->code."\n";
		print 'Message: '.$getResponse->message."\n";
		print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
		print 'Results Length: '. count($getResponse->results)."\n";
		print "\n---------------\n";
	}
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



