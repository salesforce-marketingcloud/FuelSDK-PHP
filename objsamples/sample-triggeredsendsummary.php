<?php
// include_once('src/ET_Client.php');
// include_once('src/ET_TriggeredSend.php');
spl_autoload_register( function($class_name) {
    include_once 'src/'.$class_name.'.php';
});
date_default_timezone_set('UTC');

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