<?php
include_once('tests/UnitBootstrap.php');
use FuelSdk\ET_Client;
use FuelSdk\ET_TriggeredSendSummary;


try {

	$myclient = new ET_Client();
	
	//Triggered Send Testing
	print_r("Get all TriggeredSendSummary \n");
	$trigger = new ET_TriggeredSendSummary();
	$trigger->authStub = $myclient;
	//$trigger->props = array("Bounces", "Clicks", "Queued");	
	$getResult = $trigger->get();
	print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResult->code."\n";
	print 'Message: '.$getResult->message."\n";
	print 'Result Count: '.count($getResult->results)."\n";
	print 'Results: '."\n";
	print_r($getResult->results);
	print "\n---------------\n";

} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}


?>