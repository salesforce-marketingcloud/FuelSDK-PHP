<?php

require('../ET_Client.php');
try {	
	$myclient = new ET_Client();	

	$NameOfAttributeOne = "ExampleAttributeOne";
	$NameOfAttributeTwo = "ExampleAttributeTwo";
	
	# Declare an array which contain all of the details for a DataExtension
	$profileAttrOne = array("Name" => $NameOfAttributeOne, "PropertyType"=>"string", "Description"=>"New Attribute from the SDK", "IsRequired"=>"false", "IsViewable"=>"false", "IsEditable"=>"true", "IsSendTime"=>"false");
	$profileAttrTwo = array("Name" => $NameOfAttributeTwo, "PropertyType"=>"string", "Description"=>"New Attribute from the SDK", "IsRequired"=>"false", "IsViewable"=>"false", "IsEditable"=>"true", "IsSendTime"=>"false");
	
	# Call CreateProfileAttributes passing in both Profile Attribute arrays
	print '>>> CreateProfileAttributes';
	$response = $myclient->CreateProfileAttributes(array($profileAttrOne,$profileAttrTwo));
	print_r('CreateProfileAttributes Status: '.($response->status ? 'true' : 'false')."\n");
	print 'Code: '.$response->code."\n";
	print 'Message: '.$response->message."\n";	
	print 'Results Length: '. count($response->results)."\n";
	print 'Results: '."\n";
	print_r($response->results);
	print "\n---------------\n";
	
	print '>>> Delete profileAttrOne';
	$profileattr = new ET_ProfileAttribute();
	$profileattr->authStub = $myclient;
	$profileattr->props = array("Name" => $NameOfAttributeOne);
	$delResponse = $profileattr->delete();
	print_r('Delete Status: '.($delResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$delResponse->code."\n";
	print 'Message: '.$delResponse->message."\n";	
	print 'Results Length: '. count($delResponse->results)."\n";
	print 'Results: '."\n";
	print_r($delResponse->results);
	print "\n---------------\n";
	
	print '>>> Delete profileAttrTwo';
	$profileattr = new ET_ProfileAttribute();
	$profileattr->authStub = $myclient;
	$profileattr->props = array("Name" => $NameOfAttributeTwo);
	$delResponse = $profileattr->delete();
	print_r('Delete Status: '.($delResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$delResponse->code."\n";
	print 'Message: '.$delResponse->message."\n";	
	print 'Results Length: '. count($delResponse->results)."\n";
	print 'Results: '."\n";
	print_r($delResponse->results);
	print "\n---------------\n";
	
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



