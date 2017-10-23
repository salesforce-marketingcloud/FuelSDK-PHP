<?php
include_once('tests/UnitBootstrap.php');
use FuelSdk\ET_Client;
use FuelSdk\ET_ResultMessage;


try {	
	$myclient = new ET_Client();

	// Retrieve ResultMessage with GetMoreResults
	print "Retrieve ResultMessage with GetMoreResults \n";
	$getResultMessage = new ET_ResultMessage();
	$getResultMessage->authStub = $myclient;
	$getResponse = $getResultMessage->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print 'Results: '. print_r($getResponse->results)."\n";
	print "\n---------------\n";
	
	while ($getResponse->moreResults) {
		print "Continue Retrieve All ResultMessage with GetMoreResults \n";
		$getResponse = $getResultMessage->GetMoreResults();
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



