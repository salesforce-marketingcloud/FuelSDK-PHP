<?php

require('../ET_Client.php');
try {	

	$myclient = new ET_Client();	
	
	## Example using CreateContentAreas() method
	
	$NameOfContentAreaOne = "ExampleContentAreaOne";
	$NameOfContentAreaTwo = "ExampleContentAreaTwo";
	
	# Declare a Ruby Hash which contain all of the details for a DataExtension
	$contAreaOne = array("CustomerKey" => $NameOfContentAreaOne, "Name"=>$NameOfContentAreaOne, "Content"=> "<b>Some HTML Content Goes here</b>");
	$contAreaTwo = array("CustomerKey" => $NameOfContentAreaTwo, "Name"=>$NameOfContentAreaTwo, "Content"=> "<b>Some Different HTML Content Goes here</b>");
	
	# Call CreateDataExtensions passing in both DataExtension Hashes as an Array
	print_r(">>> Calling CreateContentAreas\n");
	$response = $myclient->CreateContentAreas(array($contAreaOne,$contAreaTwo));
	print_r('Response Status: '.($response->status ? 'true' : 'false')."\n");
	print 'Code: '.$response->code."\n";
	print 'Message: '.$response->message."\n";	
	print 'Result Count: '.count($response->results)."\n";
	print 'Results: '."\n";
	print_r($response->results);
	print "\n---------------\n";
	
	print_r(">>> Delete contAreaOne\n");
	$contArea = new ET_ContentArea();
	$contArea->authStub = $myclient;
	$contArea->props = array("CustomerKey" => $NameOfContentAreaOne);
	$delResponse = $contArea->delete();
	print_r('Delete Status: '.($delResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$delResponse->code."\n";
	print 'Message: '.$delResponse->message."\n";	
	print 'Result Count: '.count($delResponse->results)."\n";
	print 'Results: '."\n";
	print_r($delResponse->results);
	print "\n---------------\n";
	
	print_r(">>> Delete contAreaTwo\n");
	$contArea = new ET_ContentArea();
	$contArea->authStub = $myclient;
	$contArea->props = array("CustomerKey" => $NameOfContentAreaTwo);
	$delResponse = $contArea->delete();
	print_r('Delete Status: '.($delResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$delResponse->code."\n";
	print 'Message: '.$delResponse->message."\n";	
	print 'Result Count: '.count($delResponse->results)."\n";
	print 'Results: '."\n";
	print_r($delResponse->results);
	print "\n---------------\n";
	
	
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



