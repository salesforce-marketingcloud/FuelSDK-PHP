<?php

require('../ET_Client.php');
try {	
	$myclient = new ET_Client();

	// Retrieve All Folder with GetMoreResults
	print "Retrieve All Folder with GetMoreResults \n";
	$getFolder = new ET_Folder();
	$getFolder->authStub = $myclient;
	$getFolder->props = array("ID", "Client.ID", "ParentFolder.ID", "ParentFolder.CustomerKey", "ParentFolder.ObjectID", "ParentFolder.Name", "ParentFolder.Description", "ParentFolder.ContentType", "ParentFolder.IsActive", "ParentFolder.IsEditable", "ParentFolder.AllowChildren", "Name", "Description", "ContentType", "IsActive", "IsEditable", "AllowChildren", "CreatedDate", "ModifiedDate", "Client.ModifiedBy", "ObjectID", "CustomerKey", "Client.EnterpriseID", "Client.CreatedBy");
	$getResponse = $getFolder->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print "\n---------------\n";
	
	while ($getResponse->moreResults) {
		print "Continue Retrieve All Folder with GetMoreResults \n";
		$getResponse = $getFolder->GetMoreResults();
		print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResponse->code."\n";
		print 'Message: '.$getResponse->message."\n";
		print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
		print 'Results Length: '. count($getResponse->results)."\n";
		print "\n---------------\n";
	}	
	
	$NameOfTestFolder = "PHPSDKFolder";
	
	// Retrieve Specific Folder for Email Folder ParentID
	print "Retrieve Specific Folder for Email Folder ParentID \n";
	$getFolder = new ET_Folder();
	$getFolder->authStub = $myclient;
	$getFolder->props = array("ID");
	$getFolder->filter = array('LeftOperand' => array('Property' => 'ParentFolder.ID','SimpleOperator' => 'equals','Value' => '0'), 'LogicalOperator' => 'AND', 'RightOperand' => array('Property' => 'ContentType','SimpleOperator' => 'equals','Value' => 'EMAIL'));
	$getResponse = $getFolder->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print "\n---------------\n";
	
	$ParentIDForEmail = $getResponse->results[0]->ID;
	print_r('Parent Folder for Email: '.$ParentIDForEmail."\n");
	

	// Create Folder
	print "Create Folder \n";
	$postFolder = new ET_Folder();
	$postFolder->authStub = $myclient;
	$postFolder->props = array("CustomerKey" => $NameOfTestFolder, "Name" => $NameOfTestFolder, "Description" => $NameOfTestFolder, "ContentType"=> "EMAIL", "ParentFolder" => array("ID" => $ParentIDForEmail));
	$postResult = $postFolder->post();
	print_r('Post Status: '.($postResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResult->code."\n";
	print 'Message: '.$postResult->message."\n";	
	print 'Results Length: '. count($postResult->results)."\n";
	print 'Results: '."\n";
	print_r($postResult->results);
	print "\n---------------\n";

	// Retrieve newly created Folder
	print "Retrieve newly created Folder \n";
	$getFolder = new ET_Folder();
	$getFolder->authStub = $myclient;
	$getFolder->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $NameOfTestFolder);
	$getFolder->props = array("ID", "Client.ID", "ParentFolder.ID", "ParentFolder.CustomerKey", "ParentFolder.ObjectID", "ParentFolder.Name", "ParentFolder.Description", "ParentFolder.ContentType", "ParentFolder.IsActive", "ParentFolder.IsEditable", "ParentFolder.AllowChildren", "Name", "Description", "ContentType", "IsActive", "IsEditable", "AllowChildren", "CreatedDate", "ModifiedDate", "Client.ModifiedBy", "ObjectID", "CustomerKey", "Client.EnterpriseID", "Client.CreatedBy");
	$getResponse = $getFolder->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print 'Results: '."\n";
	print_r($getResponse->results);
	print "\n---------------\n";

	// Update Folder
	print "Update Folder \n";
	$subPatch = new ET_Folder();
	$subPatch->authStub = $myclient;
	$subPatch->props = array("CustomerKey" => $NameOfTestFolder, "Description" => "New Description");
	$patchResult = $subPatch->patch();
	print_r('Patch Status: '.($patchResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$patchResult->code."\n";
	print 'Message: '.$patchResult->message."\n";	
	print 'Results Length: '. count($patchResult->results)."\n";
	print 'Results: '."\n";
	print_r($patchResult->results);
	print "\n---------------\n";

	// Retrieve Updated Folder
	print "Retrieve Updated Folder \n";
	$getFolder = new ET_Folder();
	$getFolder->authStub = $myclient;
	$getFolder->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $NameOfTestFolder);
	$getFolder->props = array("ID", "Client.ID", "ParentFolder.ID", "ParentFolder.CustomerKey", "ParentFolder.ObjectID", "ParentFolder.Name", "ParentFolder.Description", "ParentFolder.ContentType", "ParentFolder.IsActive", "ParentFolder.IsEditable", "ParentFolder.AllowChildren", "Name", "Description", "ContentType", "IsActive", "IsEditable", "AllowChildren", "CreatedDate", "ModifiedDate", "Client.ModifiedBy", "ObjectID", "CustomerKey", "Client.EnterpriseID", "Client.CreatedBy");
	$getResponse = $getFolder->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print 'Results: '."\n";
	print_r($getResponse->results);
	print "\n---------------\n";
	
	// Delete Folder
	print "Delete Folder \n";
	$deleteFolder = new ET_Folder();
	$deleteFolder->authStub = $myclient;
	$deleteFolder->props = array("CustomerKey" => $NameOfTestFolder);
	$deleteResponse = $deleteFolder->delete();
	print_r('Delete Status: '.($deleteResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$deleteResponse->code."\n";
	print 'Message: '.$deleteResponse->message."\n";	
	print 'Results Length: '. count($deleteResponse->results)."\n";
	print 'Results: '."\n";
	print_r($deleteResponse->results);
	print "\n---------------\n";
	
	// Retrieve Folder to confirm deletion
	print "Retrieve Folder to confirm deletion \n";
	$getFolder = new ET_Folder();
	$getFolder->authStub = $myclient;
	$getFolder->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $NameOfTestFolder);
	$getFolder->props = array("ID");
	$getResponse = $getFolder->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print 'Results: '."\n";
	print_r($getResponse->results);
	print "\n---------------\n";

}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



