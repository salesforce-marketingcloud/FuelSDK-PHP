<?php

require('../ET_Client.php');
try {	
	$myclient = new ET_Client();	

	$DataExtensionCustomerKey = "62476204-bfd3-de11-95ca-001e0bbae8cc";
	$CSVFileName = "SDKExample.csv";
	
	/*
	Parameters:
	* Data Extension CustomerKey - CustomerKey values are displayed in the UI as External Key   
	* File Name - File must be a CSV located on your ExactTarget FTP Site
	* Overwrite (Boolean) - Set to True in order to overwrite all existing data in the data extension. Required if Data Extension does not have a primary key.
	*/

	# Call CreateAndStartDataExtensionImport
	$response = $myclient->CreateAndStartDataExtensionImport($DataExtensionCustomerKey, $CSVFileName, true);
	print_r('Import Status: '.($response->status ? 'true' : 'false')."\n");
	print 'Code: '.$response->code."\n";
	print 'Message: '.$response->message."\n";	
	print 'Results Length: '. count($response->results)."\n";
	print 'Results: '."\n";
	print_r($response->results);
	print "\n---------------\n";
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



