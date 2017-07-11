<?php

require('../ET_Client.php');
try {	
	$myclient = new ET_Client();
	
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
	
	// Specify the name of a TriggeredSend that was setup for testing 
	// Do not use a production Triggered Send Definition
	
	$NameOfTestTS = "TEXTEXT";

	// Pause a TriggeredSend
	print_r("Pause a TriggeredSend \n");
	$patchTrig = new ET_TriggeredSend();
	$patchTrig->authStub = $myclient;
	$patchTrig->props = array('CustomerKey' => $NameOfTestTS, 'TriggeredSendStatus' => 'Inactive' );
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
	$trigger->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $NameOfTestTS);
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
		
	// Retrieve Single TriggeredSend After setting back to active
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

	// Send an email with TriggeredSend 
	print_r("Send an email using a triggered send \n");
	$sendTrigger = new ET_TriggeredSend();
	$sendTrigger->props = array('CustomerKey' => 'TEXTEXT');
	$sendTrigger->authStub = $myclient;
	$sendTrigger->subscribers = array(array("EmailAddress" => "testing@bh.exacttarget.com", "SubscriberKey" => "testing@bh.exacttarget.com"));
	$sendResult = $sendTrigger->send();
	print_r('Send Status: '.($sendResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$sendResult->code."\n";
	print 'Message: '.$sendResult->message."\n";
	print 'Results: '."\n";
	print_r($sendResult->results);
	print "\n---------------\n";

	$clientMID = '0000001';

	// Send an email with TriggeredSend with Client context
	print_r("Send an email using a triggered send with Client context\n");
	$sendTrigger = new ET_TriggeredSend();
	$sendTrigger->props = array('CustomerKey' => 'TEXTEXT');
	$sendTrigger->authStub = $myclient;
	$sendTrigger->subscribers = array(array("EmailAddress" => "testing@bh.exacttarget.com", "SubscriberKey" => "testing@bh.exacttarget.com"));
	$sendResult = $sendTrigger->send( $clientMID );
	print_r('Send Status: '.($sendResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$sendResult->code."\n";
	print 'Message: '.$sendResult->message."\n";
	print 'Results: '."\n";
	print_r($sendResult->results);
	print "\n---------------\n";

	// Generate a unique identifier for the TriggeredSend customer key since they cannot be re-used even after deleted
	$TSNameForCreateThenDelete = uniqid();

	// Create a TriggeredSend Definition
	print_r("Create a TriggeredSend Definition  \n");
	$postTrig = new ET_TriggeredSend();
	$postTrig->authStub = $myclient;
	$postTrig->props = array('CustomerKey' => $TSNameForCreateThenDelete,'Name' => $TSNameForCreateThenDelete, 'Email' => array("ID"=>"3113962"), "SendClassification"=> array("CustomerKey"=> "2240") );
	$postResult = $postTrig->post();
	print_r('Post Status: '.($postResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResult->code."\n";
	print 'Message: '.$postResult->message."\n";
	print 'Result Count: '.count($postResult->results)."\n";
	print 'Results: '."\n";
	print_r($postResult->results);
	print "\n---------------\n";

	// Delete a TriggeredSend Definition 
	print_r("Delete a TriggeredSend Definition \n");
	$deleteTrig = new ET_TriggeredSend();
	$deleteTrig->authStub = $myclient;
	$deleteTrig->props = array('CustomerKey' => $TSNameForCreateThenDelete);
	$deleteResult = $deleteTrig->delete();
	print_r('Delete Status: '.($deleteResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$deleteResult->code."\n";
	print 'Message: '.$deleteResult->message."\n";
	print 'Result Count: '.count($deleteResult->results)."\n";
	print 'Results: '."\n";
	print_r($deleteResult->results);
	print "\n---------------\n";
	
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



