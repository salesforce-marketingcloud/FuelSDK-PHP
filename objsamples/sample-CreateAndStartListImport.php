<?php

require('../ET_Client.php');
try {	
	$myclient = new ET_Client();	

	$ListID = "1956035";
	$CSVFileName = "SDKExample.csv";
	
	/*
	Parameters:
		List ID  
		File Name - File must be a CSV located on your ExactTarget FTP Site
	*/

	# Call SendEmailToList
	$response = $myclient->CreateAndStartListImport($ListID, $CSVFileName);
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



