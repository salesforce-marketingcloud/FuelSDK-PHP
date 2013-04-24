<?php

require('../ET_Client.php');
try {	

	$myclient = new ET_Client();	
	
	## Example using CreateDataExtensions() method
	
	// Declare an associative array which contain all of the details for a DataExtension
	$deOne = array("Name" => "HelperDEOne","CustomerKey" => "HelperDEOne");
	$deOne['columns'] = array(array("Name" => "Name", "FieldType" => "Text", "IsPrimaryKey" => "true", "MaxLength" => "100", "IsRequired" => "true"),array("Name" => "OtherField", "FieldType" => "Text"));
	
	// Declare a 2nd array which contain all of the details for a DataExtension
	$deTwo = array("Name" => "HelperDETwo","CustomerKey" => "HelperDETwo");
	$deTwo['columns'] = array( array("Name" => "Name", "FieldType" => "Text", "IsPrimaryKey" => "true", "MaxLength" => "100", "IsRequired" => "true"),array("Name" => "OtherField", "FieldType" => "Text"));
	
	// Call CreateDataExtensions passing in both DataExtension arrays
	$response = $myclient->CreateDataExtensions(array($deOne, $deTwo));
	print_r('Response Status: '.($response->status ? 'true' : 'false')."\n");
	print 'Code: '.$response->code."\n";
	print 'Message: '.$response->message."\n";	
	print 'Results Length: '. count($response->results)."\n";
	print "Results: \n";
	print_r($response->results);
	print "\n---------------\n";
	
	// Delete deOne
	print_r("Delete deOne  \n");
	$deleteDE = new ET_DataExtension();
	$deleteDE->authStub = $myclient;
	$deleteDE->props = array("CustomerKey" => "HelperDEOne");
	$deleteResult = $deleteDE->delete();
	print_r('Delete Status: '.($deleteResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$deleteResult->code."\n";
	print 'Message: '.$deleteResult->message."\n";	
	print 'Result Count: '.count($deleteResult->results)."\n";
	print 'Results: '."\n";
	print_r($deleteResult->results);
	print "\n---------------\n";
	
	// Delete deTwo
	print_r("Delete deTwo  \n");
	$deleteDE = new ET_DataExtension();
	$deleteDE->authStub = $myclient;
	$deleteDE->props = array("CustomerKey" => "HelperDETwo");
	$deleteResult = $deleteDE->delete();
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



