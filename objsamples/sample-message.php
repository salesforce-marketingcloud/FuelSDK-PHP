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
	
	$RetrieveMessageByKeyTestKey = "527BC1BC-E9B1-402D-8FB0-3125D1088A55";
	
	// Retrieve Message by Key	 
	print "Retrieve Message by Key \n";
	$getSingleMG = new ET_Message_Guide();
	$getSingleMG->authStub = $myclient;
	$getSingleMG->props = array("key" => $RetrieveMessageByKeyTestKey);
	$getSingleResult = $getSingleMG->get();
	print_r('Get Status: '.($getSingleResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getSingleResult->code."\n";
	print 'Message: '.$getSingleResult->message."\n";
	print 'Results: "\n"';
	print_r($getSingleResult->results);
	print "\n---------------\n";
	
	//$RetrieveMessageByIDTestID = "562f7bc3-0df6-4eb5-ae7c-f2710a83c540";
	
	// Retrieve Message by ID	 
	//print "Retrieve Message by ID \n";
	//$getSingleMG = new ET_Message_Guide();
	//$getSingleMG->authStub = $myclient;
	//$getSingleMG->props = array("id" => $RetrieveMessageByIDTestID);
	//$getSingleResult = $getSingleMG->get();
	//print_r('Get Status: '.($getSingleResult->status ? 'true' : 'false')."\n");
	//print 'Code: '.$getSingleResult->code."\n";
	//print 'Message: '.$getSingleResult->message."\n";
	//print_r('More Results: '.($getSingleResult->moreResults ? 'true' : 'false')."\n");
	//print 'Results Length(Items): '. count($getSingleResult->results->items)."\n";
	//print 'Results: "\n"';
	//print_r($getSingleResult->results);
	//print "\n---------------\n";
	
	
	$convertHTML = "<html><head><meta name=\"messageType\" content=\"application/vnd.et.message.email.html\"><meta name=\"viewTypes\" content=\"emailhtmlbody\" data-type=\"guide\"></head><body><div style=\"background: black; border: 1; width: 105px; height: 305px;\"><div data-type=\"slot\" style=\"background: red; border: 1; width: 100px; height: 100px;\" data-alias=\"master\">R</div><div data-type=\"slot\" data-alias=\"A\" style=\"background: white; border: 1; width: 100px; height: 100px;\">W</div><div data-type=\"slot\" data-alias=\"B\" style=\"background: blue; border: 1; width: 100px; height: 100px;\">B <div data-type=\"slot\" data-alias=\"C\" style=\"background: orange; border: 1; width: 100px; height: 100px;\">C <br /></div></div></div><a href=\"%%profile_center_url%%\" alias=\"Update Profile\">Update Profile</a><table cellpadding=\"2\" cellspacing=\"0\" width=\"600\" ID=\"Table5\" Border=0><tr><td><font face=\"verdana\" size=\"1\" color=\"#444444\">This email was sent to:  %%emailaddr%% <br><br><b>Email Sent By:</b> %%Member_Busname%%<br>%%Member_Addr%% %%Member_City%%, %%Member_State%%, %%Member_PostalCode%%, %%Member_Country%%<br><br></font></td></tr></table></body></html>";
	
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
		$message = $postResponse->results;
				
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
			$SubscriberTestEmail = "ExampleTestEmail@bh.exacttarget.com";
			
			// Create Subscriber on List
			print "Upsert Subscriber to List \n";
			$subCreate = new ET_Subscriber();
			$subCreate->authStub = $myclient;
			$subCreate->props = array("EmailAddress" => $SubscriberTestEmail, "Lists" => array("ID" => $listID));
			$putResult = $subCreate->put();
			print_r('Put Status: '.($putResult->status ? 'true' : 'false')."\n");
			print 'Code: '.$putResult->code."\n";
			print 'Message: '.$putResult->message."\n";	
			print 'Results Length: '. count($putResult->results)."\n";
			print 'Results: '."\n";
			print_r($putResult->results);
			print "\n---------------\n";
			
			if ($postResponse->status){
				// Send the Message
				print "Send the new Message \n";
				$sendMG = new ET_Message_Guide();
				$sendMG->authStub = $myclient;
				$sendMG->props = array("listID" => $listID, "messageID" => $IDofPostMessage, "subject"=>"Example Subject");	
				$sendResult = $sendMG->send();
				print_r('Retrieve Status: '.($sendResult->status ? 'true' : 'false')."\n");
				print 'Code: '.$sendResult->code."\n";
				print 'Message: '.$sendResult->message."\n";
				print 'Results Length: '. count($sendResult->results)."\n";
				print 'Results: '."\n";
				print_r($sendResult->results);
				print 'New JobID: '."\n";
				print_r($sendResult->results[0]->NewID);
				print "\n---------------\n";
			}
		
		}
		
		// Update a Message		
		print "Update a Message \n";
		$patchMG = new ET_Message_Guide();
		$patchMG->authStub = $myclient;
		$patchMG->props = get_object_vars($message);
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



