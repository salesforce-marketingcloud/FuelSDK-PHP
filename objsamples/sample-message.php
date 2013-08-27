<?php

require('../ET_Client.php');
try {	

	$myclient = new ET_Client();
	
	// Retrieve All Messages with GetMoreResults
	print "Retrieve All Messages with GetMoreResults \n";
	$getMG = new ET_Message_Guide();
	$getMG->authStub = $myclient;
	$getResult = $getMG->get();
	print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResult->code."\n";
	print 'Message: '.$getResult->message."\n";
	print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
	print 'Results Length(Items): '. count($getResult->results->items)."\n";
	//print 'Results: "\n"';
	//print_r($getResult->results);
	print "\n---------------\n";
	
	
	while ($getResult->moreResults) {
		print "Continue Retrieve All Message with GetMoreResults \n";
		$getResult = $getMG->GetMoreResults();
		print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResult->code."\n";
		print 'Message: '.$getResult->message."\n";
		print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
		print 'Results Length(Items): '. count($getResult->results->items)."\n";
		print "\n---------------\n";
	}	 
	
	
	$convertHTML = "<html><head><meta name=\"messageType\" content=\"application/vnd.et.message.web.html\"><meta name=\"viewTypes\" content=\"web \" data-type=\"guide\"></head><body><h1>Hello World!</h1></body></html>";
	
	// Convert a Message
	print "Convert a Message \n";
	$convertMG = new ET_Message_Guide();
	$convertMG->authStub = $myclient;
	$convertMG->props = array("content" => $convertHTML);
	$convertResponse = $convertMG->convert();
	
	print_r('Post Status: '.($convertResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$convertResponse->code."\n";
	print 'Message: '.$convertResponse->message."\n";
	print 'Results Length: '. count($convertResponse->results)."\n";
	print 'Results: '."\n";
	print_r($convertResponse->results);
	print "\n---------------\n";
	
	
	$message = $convertResponse->results;
	
	
	// Create a new Message
	print "Create a new Message \n";
	$postMG = new ET_Message_Guide();
	$postMG->authStub = $myclient;
	$postMG->props = $message;	
	$postResponse = $postMG->Post();
	print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResponse->code."\n";
	print 'Message: '.$postResponse->message."\n";
	print 'Results Length: '. count($postResponse->results)."\n";
	print 'Results: '."\n";
	print_r($postResponse->results);
	print "\n---------------\n";
	

	if ($postResponse->status) {
	
		$IDofPostMessage = $postResponse->results->id;
	
				
		// Retrieve the new Message
		print "Retrieve the new Message \n";
		$getMG = new ET_Message_Guide();
		$getMG->authStub = $myclient;
		$getMG->props = array("id" => $IDofPostMessage);	
		$getResult = $getMG->Get();
		print_r('Retrieve Status: '.($getResult->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResult->code."\n";
		print 'Message: '.$getResult->message."\n";
		print 'Results Length: '. count($getResult->results)."\n";
		print 'Results: '."\n";
		print_r($getResult->results);
		print "\n---------------\n";
		
		
		
		// Render the Message by Message Model
		print "Render the new Message by Message Model \n";
		$renderMG = new ET_Message_Guide();
		$renderMG->authStub = $myclient;
		$renderMG->props = $message;	
		$renderRessult = $renderMG->render();
		print_r('Retrieve Status: '.($renderRessult->status ? 'true' : 'false')."\n");
		print 'Code: '.$renderRessult->code."\n";
		print 'Message: '.$renderRessult->message."\n";
		print 'Results Length: '. count($renderRessult->results)."\n";
		print 'Results: '."\n";
		print_r($renderRessult->results);
		print "\n---------------\n";
		
		
		// Render the Message by Message Id
		print "Render the new Message by Message Id \n";
		$renderMG = new ET_Message_Guide();
		$renderMG->authStub = $myclient;
		$renderMG->props = array("id" => $IDofPostMessage);	
		$renderRessult = $renderMG->render();
		print_r('Retrieve Status: '.($renderRessult->status ? 'true' : 'false')."\n");
		print 'Code: '.$renderRessult->code."\n";
		print 'Message: '.$renderRessult->message."\n";
		print 'Results Length: '. count($renderRessult->results)."\n";
		print 'Results: '."\n";
		print_r($renderRessult->results);
		print "\n---------------\n";
		
		
		// Create List
		print "Create List \n";
		$postContent = new ET_List();
		$postContent->authStub = $myclient;
		$postContent->props = array("ListName" => "Test List for Message Guide Send", "Description" => "This list was created with the PHPSDK", "Type" => "Private");
		$postResponse = $postContent->post();
		print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$postResponse->code."\n";
		print 'Message: '.$postResponse->message."\n";	
		print 'Results Length: '. count($postResponse->results)."\n";
		print 'Results: '."\n";
		print_r($postResponse->results);
		print "\n---------------\n";
		
		if ($postResponse->status){
		
			$listID = $postResponse->results[0]->NewID;
			$SubscriberTestEmail = "PHPSDKListSubscriber@bh.exacttarget.com";
			
			// Create Subscriber on List
			print "Create Subscriber on List \n";
			$subCreate = new ET_Subscriber();
			$subCreate->authStub = $myclient;
			$subCreate->props = array("EmailAddress" => $SubscriberTestEmail, "Lists" => array("ID" => $listID));
			$postResult = $subCreate->post();
			print_r('Post Status: '.($postResult->status ? 'true' : 'false')."\n");
			print 'Code: '.$postResult->code."\n";
			print 'Message: '.$postResult->message."\n";	
			print 'Results Length: '. count($postResult->results)."\n";
			print 'Results: '."\n";
			print_r($postResult->results);
			print "\n---------------\n";
			
			if ($postResponse->status){
			
				// Preview the Message
				print "Preview the new Message \n";
				$sendMG = new ET_Message_Guide();
				$sendMG->authStub = $myclient;
				$sendMG->props = array("listID" => $listID, "messageID" => $IDofPostMessage);	
				$sendResult = $sendMG->send();
				print_r('Retrieve Status: '.($sendResult->status ? 'true' : 'false')."\n");
				print 'Code: '.$sendResult->code."\n";
				print 'Message: '.$sendResult->message."\n";
				print 'Results Length: '. count($sendResult->results)."\n";
				print 'Results: '."\n";
				print_r($sendResult->results);
				print "\n---------------\n";
			
			
				// Send the Message
				print "Send the new Message \n";
				$sendMG = new ET_Message_Guide();
				$sendMG->authStub = $myclient;
				$sendMG->props = array("listID" => $listID, "messageID" => $IDofPostMessage);	
				$sendResult = $sendMG->send();
				print_r('Retrieve Status: '.($sendResult->status ? 'true' : 'false')."\n");
				print 'Code: '.$sendResult->code."\n";
				print 'Message: '.$sendResult->message."\n";
				print 'Results Length: '. count($sendResult->results)."\n";
				print 'Results: '."\n";
				print_r($sendResult->results);
				print "\n---------------\n";
				
			}
		
		}
		
		// Update a Message
		print "Update a Message \n";
		$patchMG = new ET_Message_Guide();
		$patchMG->authStub = $myclient;
		$patchMG->props = $message;
		$patchResponse = $patchMG->Patch();
		print_r('Patch Status: '.($patchResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$patchResponse->code."\n";
		print 'Message: '.$patchResponse->message."\n";
		print 'Results Length: '. count($patchResponse->results)."\n";
		print 'Results: '."\n";
		print_r($patchResponse->results);
		print "\n---------------\n";
		
		// Delete a Message
		print "Delete a Message \n";
		$deleteMG = new ET_Message_Guide();
		$deleteMG->authStub = $myclient;
		$deleteMG->props = array("id" => $IDofPostMessage);	
		$deleteResponse = $deleteMG->Delete();
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



