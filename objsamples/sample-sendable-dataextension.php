<?php
// include_once('src/ET_Client.php');
// include_once('src/ET_DataExtension.php');
// include_once('src/ET_DataExtension_Column.php');
// include_once('src/ET_DataExtension_Row.php');
spl_autoload_register( function($class_name) {
    include_once 'src/'.$class_name.'.php';
});
date_default_timezone_set('UTC');

try {	
	$myclient = new ET_Client();
		
	//DataExtension Testing
	//Get all Data Extensions
	print_r("Get all Data Extensions \n");
	$getDE = new ET_DataExtension();
	$getDE->authStub = $myclient;
	$getDE->props = array("CustomerKey", "Name");	
	$getResult = $getDE->get();
	print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResult->code."\n";
	print 'Message: '.$getResult->message."\n";
	print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
	print 'Result Count: '.count($getResult->results)."\n";
	//print 'Results: '."\n";
	//print_r($getResult->results);
	print "\n---------------\n";

	// Specify a name for the data extension that will be used for testing 
	// Note: Name and CustomerKey will be the same value
	// WARNING: Data Extension will be deleted so don't use the name of a
	// production data extension 
	
	$DataExtensionNameForTesting = "PHPSDKTestDE".uniqid();

	// Create a Data Extension
	print_r("Create a Data Extension  \n");
	$postDE = new ET_DataExtension();
	$postDE->authStub = $myclient;

	$postDE->props = array(
	    "Name"                       => $DataExtensionNameForTesting, 
	    "CustomerKey"                => $DataExtensionNameForTesting,
	    "IsSendable"                 => "true",
	    "SendableCustomObjectField"  => array(
	      'Name' => 'EmailAddress'),
	    "SendableSubscriberField"    => array(
	      'Name' => 'Subscriber Key',
	      'Value' => NULL
	      )
    	);

	$postDE->columns = array();
	$postDE->columns[] = array("Name" => "EmailAddress", "FieldType" => "EmailAddress", "IsPrimaryKey" => "true","MaxLength" => "100", "IsRequired" => "true");
	$postDE->columns[] = array("Name" => "TEXT", "FieldType" => "Text");
	$postResult = $postDE->post();
	print_r('Post Status: '.($postResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResult->code."\n";
	print 'Message: '.$postResult->message."\n";	
	print 'Result Count: '.count($postResult->results)."\n";
	print 'Results: '."\n";
	print_r($postResult->results);
	print "\n---------------\n";
	
   }
   catch (Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";
   }

?>
