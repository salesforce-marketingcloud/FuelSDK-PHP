<?php

require('../ET_Client.php');
try {	
	$myclient = new ET_Client();

	// Retrieve All Email with GetMoreResults
	print "Retrieve All Email with GetMoreResults \n";
	$getEmail = new ET_Email();
	$getEmail->authStub = $myclient;
	$getEmail->props = array("ID","PartnerKey","CreatedDate","ModifiedDate","Client.ID","Name","Folder","CategoryID","HTMLBody","TextBody","Subject","IsActive","IsHTMLPaste","ClonedFromID","Status","EmailType","CharacterSet","HasDynamicSubjectLine","ContentCheckStatus","Client.PartnerClientKey","ContentAreas","CustomerKey");
	$getResponse = $getEmail->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print "\n---------------\n";
	
	while ($getResponse->moreResults) {
		print "Continue Retrieve All Email with GetMoreResults \n";
		$getResponse = $getEmail->GetMoreResults();
		print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResponse->code."\n";
		print 'Message: '.$getResponse->message."\n";
		print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
		print 'Results Length: '. count($getResponse->results)."\n";
		print "\n---------------\n";
	}	
	
	$NameOfTestEmail = "PHPSDKEmail";
	
	// Create Email
	print "Create Email \n";
	$postEmail = new ET_Email();
	$postEmail->authStub = $myclient;
	$postEmail->props = array("CustomerKey" => $NameOfTestEmail, "Name"=>$NameOfTestEmail, "Subject"=>"Created with the SDK",  "HTMLBody"=> "<b>Some HTML Goes here</b>",  "EmailType" => "HTML", "IsHTMLPaste" => "true");
	$postResult = $postEmail->post();
	print_r('Post Status: '.($postResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResult->code."\n";
	print 'Message: '.$postResult->message."\n";	
	print 'Results Length: '. count($postResult->results)."\n";
	print 'Results: '."\n";
	print_r($postResult->results);
	print "\n---------------\n";

	// Retrieve newly created Email
	print "Retrieve newly created Email \n";
	$getEmail = new ET_Email();
	$getEmail->authStub = $myclient;
	$getEmail->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $NameOfTestEmail);
	$getEmail->props = array("ID","PartnerKey","CreatedDate","ModifiedDate","Client.ID","Name","Folder","CategoryID","HTMLBody","TextBody","Subject","IsActive","IsHTMLPaste","ClonedFromID","Status","EmailType","CharacterSet","HasDynamicSubjectLine","ContentCheckStatus","Client.PartnerClientKey","ContentAreas","CustomerKey");
	$getResponse = $getEmail->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print 'Results: '."\n";
	print_r($getResponse->results);
	print "\n---------------\n";

	// Update Email
	print "Updates Email \n";
	$patchEmail = new ET_Email();
	$patchEmail->authStub = $myclient;
	$patchEmail->props = array("CustomerKey" => $NameOfTestEmail, "Name"=>$NameOfTestEmail, "Subject"=>"Created with the SDK!!! Now with more !!!!",  "HTMLBody"=> "<b>Some HTML Content Goes here. NOW WITH NEW CONTENT</b>");
	$patchResult = $patchEmail->patch();
	print_r('Patch Status: '.($patchResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$patchResult->code."\n";
	print 'Message: '.$patchResult->message."\n";	
	print 'Results Length: '. count($patchResult->results)."\n";
	print 'Results: '."\n";
	print_r($patchResult->results);
	print "\n---------------\n";

	// Retrieve Updated Email
	print "Retrieve Updated Email \n";
	$getEmail = new ET_Email();
	$getEmail->authStub = $myclient;
	$getEmail->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $NameOfTestEmail);
	$getEmail->props = array("ID","PartnerKey","CreatedDate","ModifiedDate","Client.ID","Name","Folder","CategoryID","HTMLBody","TextBody","Subject","IsActive","IsHTMLPaste","ClonedFromID","Status","EmailType","CharacterSet","HasDynamicSubjectLine","ContentCheckStatus","Client.PartnerClientKey","ContentAreas","CustomerKey");
	$getResponse = $getEmail->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print 'Results: '."\n";
	print_r($getResponse->results);
	print "\n---------------\n";
	
	// Delete Email
	print "Delete Email \n";
	$deleteEmail = new ET_Email();
	$deleteEmail->authStub = $myclient;
	$deleteEmail->props = array("CustomerKey" => $NameOfTestEmail);
	$deleteResponse = $deleteEmail->delete();
	print_r('Delete Status: '.($deleteResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$deleteResponse->code."\n";
	print 'Message: '.$deleteResponse->message."\n";	
	print 'Results Length: '. count($deleteResponse->results)."\n";
	print 'Results: '."\n";
	print_r($deleteResponse->results);
	print "\n---------------\n";
	
	// Retrieve Email to confirm deletion
	print "Retrieve Email to confirm deletion \n";
	$getEmail = new ET_Email();
	$getEmail->authStub = $myclient;
	$getEmail->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $NameOfTestEmail);
	$getEmail->props = array("ID","PartnerKey","CreatedDate","ModifiedDate","Client.ID","Name","Folder","CategoryID","HTMLBody","TextBody","Subject","IsActive","IsHTMLPaste","ClonedFromID","Status","EmailType","CharacterSet","HasDynamicSubjectLine","ContentCheckStatus","Client.PartnerClientKey","ContentAreas","CustomerKey");
	$getResponse = $getEmail->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print 'Results: '."\n";
	print_r($getResponse->results);
	print "\n---------------\n";


}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



