<?php

require('../ET_Client.php');
try {	

	$myclient = new ET_Client();
	
	// Retrieve All Assets with GetMoreResults
	print "Retrieve All Assets with GetMoreResults \n";
	$getAsset = new ET_Asset();
	$getAsset->authStub = $myclient;
	$getResult = $getAsset->get();
	print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResult->code."\n";
	print 'Message: '.$getResult->message."\n";
	print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
	print 'Results Length(Items): '. count($getResult->results->items)."\n";
	//print 'Results: "\n"';
	//print_r($getResult->results);
	print "\n---------------\n";
	
	
	while ($getResult->moreResults) {
		print "Continue Retrieve All Assets with GetMoreResults \n";
		$getResult = $getAsset->GetMoreResults();
		print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResult->code."\n";
		print 'Message: '.$getResult->message."\n";
		print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
		print 'Results Length(Items): '. count($getResult->results->items)."\n";
		print "\n---------------\n";
	}	 
	
	
	

	$base64EncodedString = base64_encode("This is my file contents of a text file");
	
	// Create a new Asset Base 64
print "Create a new Asset Base 64 \n";
	$postAsset = new ET_Asset();
	$postAsset->authStub = $myclient;
	$postAsset->props = array("fileName" => "TestFile.txt", "mimeType" => "text/plain", "fileData" => $base64EncodedString, "displayName" => "TestFile.txt", "customerKey" => md5(uniqid()), "description" => "");
	$postResponse = $postAsset->Post();
	print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResponse->code."\n";
	print 'Message: '.$postResponse->message."\n";
	print 'Results Length: '. count($postResponse->results)."\n";
	print 'Results: '."\n";
	print_r($postResponse->results);
	print "\n---------------\n";
	
	
	
	// Create a new Asset File Path
	print "Create a new Asset File Path \n";
	$postAsset2 = new ET_Asset();
	$postAsset2->authStub = $myclient;
	$postAsset2->props = array("filePath" => $_SERVER['PWD'] . '/sample-asset-TestFilePath.txt');
	$postResponse = $postAsset2->Post();
	print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResponse->code."\n";
	print 'Message: '.$postResponse->message."\n";
	print 'Results Length: '. count($postResponse->results)."\n";
	print 'Results: '."\n";
	print_r($postResponse->results);
	print "\n---------------\n";	
	
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



