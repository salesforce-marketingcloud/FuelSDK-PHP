<?php

require('../ET_Client.php');
try {	
	$myclient = new ET_Client();

	$NewImportName = "PHPSDKImport";
	$SendableDataExtensionCustomerKey = "62476204-bfd3-de11-95ca-001e0bbae8cc";
	$ListIDForImport = "1956035";

	print "Create Import to DataExtension\n";
	$postImport = new ET_Import();
	$postImport->authStub = $myclient;
	$postImport->props = array("Name"=>$NewImportName);
	$postImport->props["CustomerKey"] = $NewImportName;
	$postImport->props["Description"] = "Created with RubySDK";
	$postImport->props["AllowErrors"] = "true";
	$postImport->props["DestinationObject"] = array("ObjectID"=>$SendableDataExtensionCustomerKey);
	$postImport->props["FieldMappingType"] = "InferFromColumnHeadings";
	$postImport->props["FileSpec"] = "PHPExample.csv";
	$postImport->props["FileType"] = "CSV";
	$postImport->props["Notification"] = array("ResponseType"=>"email","ResponseAddress"=>"example@example.com");
	$postImport->props["RetrieveFileTransferLocation"] = array("CustomerKey"=>"ExactTarget Enhanced FTP");
	$postImport->props["UpdateType"] = "Overwrite";
	$postResponse = $postImport->post();
	print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResponse->code."\n";
	print 'Message: '.$postResponse->message."\n";	
	print 'Results Length: '. count($postResponse->results)."\n";
	print 'Results: '."\n";
	print_r($postResponse->results);
	print "\n---------------\n";

	print "Delete Import\n";
	$deleteImport = new ET_Import();
	$deleteImport->authStub = $myclient;
	$deleteImport->props = array("CustomerKey" => $NewImportName);
	$deleteResponse = $deleteImport->delete();
	print_r('Delete Status: '.($deleteResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$deleteResponse->code."\n";
	print 'Message: '.$deleteResponse->message."\n";	
	print 'Results Length: '. count($deleteResponse->results)."\n";
	print 'Results: '."\n";
	print_r($deleteResponse->results);
	print "\n---------------\n";

	print "Create Import to List \n";
	$postImport = new ET_Import();
	$postImport->authStub = $myclient;
	$postImport->props = array("Name"=>$NewImportName);
	$postImport->props["CustomerKey"] = $NewImportName;
	$postImport->props["Description"] = "Created with RubySDK";
	$postImport->props["AllowErrors"] = "true";
	$postImport->props["DestinationObject"] = array("ID"=> $ListIDForImport);
	$postImport->props["FieldMappingType"] = "InferFromColumnHeadings";
	$postImport->props["FileSpec"] = "PHPExample.csv";
	$postImport->props["FileType"] = "CSV";
	$postImport->props["Notification"] = array("ResponseType"=>"email","ResponseAddress"=>"example@example.com");
	$postImport->props["RetrieveFileTransferLocation"] = array("CustomerKey"=>"ExactTarget Enhanced FTP");
	$postImport->props["UpdateType"] = "AddAndUpdate";
	$postResponse = $postImport->post();
	print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResponse->code."\n";
	print 'Message: '.$postResponse->message."\n";	
	print 'Results Length: '. count($postResponse->results)."\n";
	print 'Results: '."\n";
	print_r($postResponse->results);
	print "\n---------------\n";
	
	print "Start Import to List\n";
	$startImport = new ET_Import();
	$startImport->authStub = $myclient;
	$startImport->props = array("CustomerKey"=>$NewImportName);
	$startResponse = $startImport->start();
	print_r('Start Status: '.($startResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$startResponse->code."\n";
	print 'Message: '.$startResponse->message."\n";	
	print 'Results Length: '. count($startResponse->results)."\n";
	print 'Results: '."\n";
	print_r($startResponse->results);
	print "\n---------------\n";

	if ($startResponse->status){
		print "Check Status using the same instance of ET_Import as used for start\n";
		$importStatus = "";
		while ($importStatus != "Error" && $importStatus != "Completed") {
			print "Checking status in loop \n";
			# Wait a bit before checking the status to give it time to process
			sleep(15);
			$statusResponse = $startImport->status();
			print_r('Status Status: '.($statusResponse->status ? 'true' : 'false')."\n");
			print 'Code: '.$statusResponse->code."\n";
			print 'Message: '.$statusResponse->message."\n";
			print 'Results Length: '. count($statusResponse->results)."\n";
			print 'Results: '."\n";
			print_r($statusResponse->results);
			print "\n---------------\n";
			$importStatus = $statusResponse->results[0]->ImportStatus;
		}
	}

	print "Delete Import\n";
	$deleteImport = new ET_Import();
	$deleteImport->authStub = $myclient;
	$deleteImport->props = array("CustomerKey" => $NewImportName);
	$deleteResponse = $deleteImport->delete();
	print_r('Delete Status: '.($deleteResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$deleteResponse->code."\n";
	print 'Message: '.$deleteResponse->message."\n";	
	print 'Results Length: '. count($deleteResponse->results)."\n";
	print 'Results: '."\n";
	print_r($deleteResponse->results);
	print "\n---------------\n";

}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



