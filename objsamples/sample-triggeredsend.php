<?php

require('../ETClient.php');
try {	
	$params = array();		
	$myclient = new ETClient(true, $params);
	
	//Triggered Send Testing
	print_r("Get all TriggeredSendDefinitions \n");
	$trigger = new ET_TriggeredSend();
	$trigger->authStub = $myclient;
	$trigger->props = array("CustomerKey", "Name", "TriggeredSendStatus");	
	$getResult = $trigger->get();
	print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResult->code."\n";
	print 'Message: '.$getResult->message."\n";
	print 'Result Count: '.count($getResult->results)."\n";
	//print 'Results: '."\n";
	//print_r($getResult->results);
	print "\n---------------\n";

	// Pause a TriggeredSend
	print_r("Pause a TriggeredSend \n");
	$patchTrig = new ET_TriggeredSend();
	$patchTrig->authStub = $myclient;
	$patchTrig->props = array('CustomerKey' => 'TEXTEXT', 'TriggeredSendStatus' => 'Inactive' );
	$patchResult = $patchTrig->patch();
	print_r('Patch Status: '.($patchResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$patchResult->code."\n";
	print 'Message: '.$patchResult->message."\n";
	print 'Result Count: '.count($patchResult->results)."\n";
	print 'Results: '."\n";
	print_r($patchResult->results);
	print "\n---------------\n";

	// Retrieve Single TriggeredSend
	print_r("Retrieve Single TriggeredSend \n");
	$trigger = new ET_TriggeredSend();
	$trigger->authStub = $myclient;
	$trigger->props = array("CustomerKey", "Name", "TriggeredSendStatus");
	$trigger->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => 'TEXTEXT');
	$getResult = $trigger->get();
	print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResult->code."\n";
	print 'Message: '.$getResult->message."\n";
	print 'Result Count: '.count($getResult->results)."\n";
	print 'Results: '."\n";
	print_r($getResult->results);
	print "\n---------------\n";

	// Start a TriggeredSend by setting to Active
	print_r("Start a TriggeredSend by setting to Active \n");
	$patchTrig = new ET_TriggeredSend();
	$patchTrig->authStub = $myclient;
	$patchTrig->props = array('CustomerKey' => 'TEXTEXT', 'TriggeredSendStatus' => 'Active', 'RefreshContent'=>'true' );
	$patchResult = $patchTrig->patch();
	print_r('Patch Status: '.($patchResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$patchResult->code."\n";
	print 'Message: '.$patchResult->message."\n";
	print 'Result Count: '.count($patchResult->results)."\n";
	print 'Results: '."\n";
	print_r($patchResult->results);
	print "\n---------------\n";
		
	// Retrieve Single TriggeredSend
	print_r("Retrieve Single TriggeredSend After setting back to active\n");
	$trigger = new ET_TriggeredSend();
	$trigger->authStub = $myclient;
	$trigger->props = array("CustomerKey", "Name", "TriggeredSendStatus");
	$trigger->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => 'TEXTEXT');
	$getResult = $trigger->get();
	print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResult->code."\n";
	print 'Message: '.$getResult->message."\n";
	print 'Result Count: '.count($getResult->results)."\n";
	print 'Results: '."\n";
	print_r($getResult->results);
	print "\n---------------\n";

	//Send an email with TriggeredSend 
	print_r("Send an email using a triggered send \n");
	$sendTrigger = new ET_TriggeredSend();
	$sendTrigger->props = array('CustomerKey' => 'TEXTEXT');
	$sendTrigger->authStub = $myclient;
	$sendTrigger->subscribers = array(array("EmailAddress" => "michaelallenclark@gmail.com", "SubscriberKey" => "michaelallenclark@gmail.com"));
	$sendResult = $sendTrigger->send();
	print_r('Send Status: '.($sendResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$sendResult->code."\n";
	print 'Message: '.$sendResult->message."\n";
	print 'Results: '."\n";
	print_r($sendResult->results);
	print "\n---------------\n";

	$TriggeredSendNameForTesting = "PHPTest-DeleteME";

	// Create a triggered send definition 
	print_r("Create a triggered send definition  \n");
	$postTrig = new ET_TriggeredSend();
	$postTrig->authStub = $myclient;
	$postTrig->props = array('CustomerKey' => $TriggeredSendNameForTesting,'Name' => $TriggeredSendNameForTesting, 'Email' => array("ID"=>"3113962"), "SendClassification"=> array("CustomerKey"=> "2240") );
	$postResult = $postTrig->post();
	print_r('Post Status: '.($postResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResult->code."\n";
	print 'Message: '.$postResult->message."\n";
	print 'Result Count: '.count($postResult->results)."\n";
	print 'Results: '."\n";
	print_r($postResult->results);
	print "\n---------------\n";

	// Delete a triggered send definition 
	print_r("Delete a triggered send definition  \n");
	$deleteTrig = new ET_TriggeredSend();
	$deleteTrig->authStub = $myclient;
	$deleteTrig->props = array('CustomerKey' => $TriggeredSendNameForTesting);
	$deleteResult = $deleteTrig->delete();
	print_r('Delete Status: '.($deleteResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$deleteResult->code."\n";
	print 'Message: '.$deleteResult->message."\n";
	print 'Result Count: '.count($deleteResult->results)."\n";
	print 'Results: '."\n";
	print_r($deleteResult->results);
	print "\n---------------\n";
	
	}
	catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



