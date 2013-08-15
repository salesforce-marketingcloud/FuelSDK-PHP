<?php

require('../ET_Client.php');
try {	
	$myclient = new ET_Client();	
	
	$EmailIDForSendDefinition = "3113962";
	$SendClassificationCustomerKey = "2239";
	$SendableDataExtensionCustomerKey = "F6F3871A-D124-499B-BBF5-3EFC0E827A51";
	
	# Call SendEmailToList
	$response = $myclient->SendEmailToDataExtension($EmailIDForSendDefinition, $SendableDataExtensionCustomerKey,$SendClassificationCustomerKey);
	print_r('Send Status: '.($response->status ? 'true' : 'false')."\n");
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



