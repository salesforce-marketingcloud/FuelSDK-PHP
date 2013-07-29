<?php

require('../ET_Client.php');
try {	
	$myclient = new ET_Client();

	// Retrieve All ContentArea with GetMoreResults
	print "Retrieve All ContentArea with GetMoreResults \n";
	$getContent = new ET_ContentArea();
	$getContent->authStub = $myclient;
	$getContent->props = array("RowObjectID","ObjectID","ID","CustomerKey","Client.ID","ModifiedDate","CreatedDate","CategoryID","Name","Layout","IsDynamicContent","Content","IsSurvey","IsBlank","Key");
	$getResponse = $getContent->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print "\n---------------\n";
	
	while ($getResponse->moreResults) {
		print "Continue Retrieve All ContentArea with GetMoreResults \n";
		$getResponse = $getContent->GetMoreResults();
		print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResponse->code."\n";
		print 'Message: '.$getResponse->message."\n";
		print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
		print 'Results Length: '. count($getResponse->results)."\n";
		print "\n---------------\n";
	}	
	
	$NameOfTestContentArea = "PHPSDKContentArea";
	
	// Create ContentArea
	print "Create ContentArea \n";
	$postContent = new ET_ContentArea();
	$postContent->authStub = $myclient;
	$postContent->props = array("CustomerKey" => $NameOfTestContentArea, "Name"=>$NameOfTestContentArea, "Content"=> "<b>Some HTML Content Goes here</b>");
	$postResult = $postContent->post();
	print_r('Post Status: '.($postResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResult->code."\n";
	print 'Message: '.$postResult->message."\n";	
	print 'Results Length: '. count($postResult->results)."\n";
	print 'Results: '."\n";
	print_r($postResult->results);
	print "\n---------------\n";

	// Retrieve newly created ContentArea
	print "Retrieve newly created ContentArea \n";
	$getContent = new ET_ContentArea();
	$getContent->authStub = $myclient;
	$getContent->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $NameOfTestContentArea);
	$getContent->props = array("RowObjectID","ObjectID","ID","CustomerKey","Client.ID","ModifiedDate","CreatedDate","CategoryID","Name","Layout","IsDynamicContent","Content","IsSurvey","IsBlank","Key");
	$getResponse = $getContent->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print 'Results: '."\n";
	print_r($getResponse->results);
	print "\n---------------\n";

	// Update ContentArea
	print "Updates ContentArea \n";
	$subPatch = new ET_ContentArea();
	$subPatch->authStub = $myclient;
	$subPatch->props = array("CustomerKey" => $NameOfTestContentArea, "Name"=>$NameOfTestContentArea, "Content"=> "<b>Some HTML Content Goes here. NOW WITH NEW CONTENT</b>");
	$patchResult = $subPatch->patch();
	print_r('Patch Status: '.($patchResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$patchResult->code."\n";
	print 'Message: '.$patchResult->message."\n";	
	print 'Results Length: '. count($patchResult->results)."\n";
	print 'Results: '."\n";
	print_r($patchResult->results);
	print "\n---------------\n";

	// Retrieve Updated ContentArea
	print "Retrieve Updated ContentArea \n";
	$getContent = new ET_ContentArea();
	$getContent->authStub = $myclient;
	$getContent->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $NameOfTestContentArea);
	$getContent->props = array("RowObjectID","ObjectID","ID","CustomerKey","Client.ID","ModifiedDate","CreatedDate","CategoryID","Name","Layout","IsDynamicContent","Content","IsSurvey","IsBlank","Key");
	$getResponse = $getContent->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print 'Results: '."\n";
	print_r($getResponse->results);
	print "\n---------------\n";
	
	// Delete ContentArea
	print "Delete ContentArea \n";
	$deleteContent = new ET_ContentArea();
	$deleteContent->authStub = $myclient;
	$deleteContent->props = array("CustomerKey" => $NameOfTestContentArea);
	$deleteResponse = $deleteContent->delete();
	print_r('Delete Status: '.($deleteResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$deleteResponse->code."\n";
	print 'Message: '.$deleteResponse->message."\n";	
	print 'Results Length: '. count($deleteResponse->results)."\n";
	print 'Results: '."\n";
	print_r($deleteResponse->results);
	print "\n---------------\n";
	
	// Retrieve ContentArea to confirm deletion
	print "Retrieve ContentArea to confirm deletion \n";
	$getContent = new ET_ContentArea();
	$getContent->authStub = $myclient;
	$getContent->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $NameOfTestContentArea);
	$getContent->props = array("RowObjectID","ObjectID","ID","CustomerKey","Client.ID","ModifiedDate","CreatedDate","CategoryID","Name","Layout","IsDynamicContent","Content","IsSurvey","IsBlank","Key");
	$getResponse = $getContent->get();
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



