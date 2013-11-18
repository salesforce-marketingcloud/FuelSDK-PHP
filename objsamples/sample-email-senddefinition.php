<?php

require('../ET_Client.php');
try {	
	$myclient = new ET_Client();
	
	$NewSendDefinitionName = "PHPSDKSendDefinition";
	$SendableDataExtensionCustomerKey = "F6F3871A-D124-499B-BBF5-3EFC0E827A51";
	$EmailIDForSendDefinition = "3113962";
	$ListIDForSendDefinition = "1729515";
	$SendClassificationCustomerKey = "2239";
	
	print "Retrieve Send Definition Details \n";
	$getSendDefinition = new ET_Email_SendDefinition();
	$getSendDefinition->authStub = $myclient;
	$getSendDefinition->props = array('Client.ID', 'CreatedDate','ModifiedDate','ObjectID','CustomerKey','Name','CategoryID','Description','SendClassification.CustomerKey','SenderProfile.CustomerKey','SenderProfile.FromName','SenderProfile.FromAddress','DeliveryProfile.CustomerKey','DeliveryProfile.SourceAddressType','DeliveryProfile.PrivateIP','DeliveryProfile.DomainType','DeliveryProfile.PrivateDomain','DeliveryProfile.HeaderSalutationSource','DeliveryProfile.FooterSalutationSource','SuppressTracking','IsSendLogging','Email.ID','BccEmail','AutoBccEmail','TestEmailAddr','EmailSubject','DynamicEmailSubject','IsMultipart','IsWrapped','SendLimit','SendWindowOpen','SendWindowClose','DeduplicateByEmail','ExclusionFilter','Additional');
	$getResponse = $getSendDefinition->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print "\n---------------\n";
	
	print "Create SendDefinition to DataExtension \n";
	$postSendDefinition = new ET_Email_SendDefinition();
	$postSendDefinition->authStub = $myclient;
	$postSendDefinition->props = array("Name"=>$NewSendDefinitionName);
	$postSendDefinition->props["CustomerKey"] = $NewSendDefinitionName;
	$postSendDefinition->props["Description"]  = "Created with PHPSDK";
	$postSendDefinition->props["SendClassification"] = array("CustomerKey"=>$SendClassificationCustomerKey);
	$postSendDefinition->props["SendDefinitionList"] = array("CustomerKey"=> $SendableDataExtensionCustomerKey, "DataSourceTypeID"=>"CustomObject");
	$postSendDefinition->props["Email"] = array("ID"=>$EmailIDForSendDefinition);
	$postResult = $postSendDefinition->post();
	print_r('Post Status: '.($postResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResult->code."\n";
	print 'Message: '.$postResult->message."\n";	
	print 'Results Length: '. count($postResult->results)."\n";
	print 'Results: '."\n";
	print_r($postResult->results);
	print "\n---------------\n";
	
	print "Delete SendDefinition \n";
	$deleteSendDefinition = new ET_Email_SendDefinition();
	$deleteSendDefinition->authStub = $myclient;
	$deleteSendDefinition->props = array("CustomerKey" => $NewSendDefinitionName);
	$deleteResponse = $deleteSendDefinition->delete();
	print_r('Delete Status: '.($deleteResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$deleteResponse->code."\n";
	print 'Message: '.$deleteResponse->message."\n";	
	print 'Results Length: '. count($deleteResponse->results)."\n";
	print 'Results: '."\n";
	print_r($deleteResponse->results);
	print "\n---------------\n";
	
	print "Create SendDefinition to List \n";
	$postSendDefinition = new ET_Email_SendDefinition();
	$postSendDefinition->authStub = $myclient;
	$postSendDefinition->props = array("Name"=>$NewSendDefinitionName);
	$postSendDefinition->props["CustomerKey"] = $NewSendDefinitionName;
	$postSendDefinition->props["Description"]  = "Created with PHPSDK";
	$postSendDefinition->props["SendClassification"] = array("CustomerKey"=>$SendClassificationCustomerKey);
	$postSendDefinition->props["SendDefinitionList"] = array("List"=> array("ID"=>$ListIDForSendDefinition), "DataSourceTypeID"=>"List");
	$postSendDefinition->props["Email"] = array("ID"=>$EmailIDForSendDefinition);
	$postResult = $postSendDefinition->post();
	print_r('Post Status: '.($postResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResult->code."\n";
	print 'Message: '.$postResult->message."\n";	
	print 'Results Length: '. count($postResult->results)."\n";
	print 'Results: '."\n";
	print_r($postResult->results);
	print "\n---------------\n";
	
	print "Send SendDefinition \n";
	$sendSendDefinition = new ET_Email_SendDefinition();
	$sendSendDefinition->authStub = $myclient;
	$sendSendDefinition->props = array("CustomerKey"=>$NewSendDefinitionName);
	$sendResponse = $sendSendDefinition->send();
	print_r('Send Status: '.($sendResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$sendResponse->code."\n";
	print 'Message: '.$sendResponse->message."\n";	
	print 'Results Length: '. count($sendResponse->results)."\n";
	print 'Results: '."\n";
	print_r($sendResponse->results);
	print "\n---------------\n";
	
	print "Check Status using the same instance of ET_Email::SendDefinition as used with start method \n";
	$emailStatus = "";
	while ($sendResponse->status && $emailStatus != "Canceled" && $emailStatus != "Complete") {
		print "Checking status in loop \n";
		# Wait a bit before checking the status to give it time to process
		sleep(15);
		$statusResponse = $sendSendDefinition->status();
		print_r('Send Status: '.($statusResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$statusResponse->code."\n";
		print 'Message: '.$statusResponse->message."\n";	
		print 'Results Length: '. count($statusResponse->results)."\n";
		print 'Results: '."\n";
		print_r($statusResponse->results);
		print "\n---------------\n";
		$emailStatus = $statusResponse->results[0]->Status;
	}
	
	print "Delete SendDefinition \n";
	$deleteSendDefinition = new ET_Email_SendDefinition();
	$deleteSendDefinition->authStub = $myclient;
	$deleteSendDefinition->props = array("CustomerKey" => $NewSendDefinitionName);
	$deleteResponse = $deleteSendDefinition->delete();
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



