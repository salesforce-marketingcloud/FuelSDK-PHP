<?php
date_default_timezone_set('America/New_York');
require('../ET_Client.php');
try {	
	$params = array();		
	$myclient = new ET_Client(true, $params);

	// Retrieve All Organizations with GetMoreResults
	print "Retrieve All Organizations with GetMoreREsults \n";
	$getOrganization = new ET_Organization();
	$getOrganization->authStub = $myclient;
	$getOrganization->props = array("ID", "Name", "AccountType", "Address", "BrandID", "BusinessName", "City", "Country", "DeletedDate", "EditionID", "Email", "Fax", "FromName", "InheritAddress", "IsActive", "IsTestAccount", "IsTrialAccount", "ParentAccount.ID", "ParentID", "ParentName", "Phone", "PrivateLabelID", "Roles", "State", "Zip", "CreatedDate", "ModifiedDate", "CustomerKey", "Client.EnterpriseID");
	$getResponse = $getOrganization->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print 'Results: '.print_r($getResponse->results)."\n";
	print "\n---------------\n";
	
	while ($getResponse->moreResults) {
		print "Continue Retrieve All Organizations with GetMoreResults \n";
		$getResponse = $getOrganization->GetMoreResults();
		print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResponse->code."\n";
		print 'Message: '.$getResponse->message."\n";
		print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
		print 'Results Length: '. count($getResponse->results)."\n";
		print "\n---------------\n";
	}
	
	$CustomerKeyofExistingOrganization = '1CC25B77-8BF9-4BF9-A718-168928AB0607';
	$CustomerKeyOfTestOrganization = "TestOrganizationCustomerKey::" . substr(md5(rand()),0,7);
	$NameOfTestOrganization = "TestOrganizationName";


	// Retreive Specific Organization
	print "Retrieve Specific Organization \n";
	$getOrganization = new ET_Organization();
	$getOrganization->authStub = $myclient;
	$getOrganization->props = array("ID", "Name", "IsActive");
	$getOrganization->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $CustomerKeyofExistingOrganization);
	$getResponse = $getOrganization->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print "\n---------------\n";
	
	$TestOrganizationID = $getResponse->results[0]->ID;
	$TestOrganizationName = $getResponse->results[0]->Name;
	$TestOrganizationIsActive = $getResponse->results[0]->IsActive;
	print_r('Test Organization ID: '.$TestOrganizationID.', Name: '.$TestOrganizationName.", IsActrive: " .$TestOrganizationIsActive."\n");	
	print "\n---------------\n";


	// Create Organization
	print "Create Organization \n";
	$postOrganization = new ET_Organization();
	$postOrganization->authStub = $myclient;
	$postOrganization->props = array("CustomerKey" =>  $CustomerKeyOfTestOrganization, "Name" => $NameOfTestOrganization, "AccountType" => "PRO_CONNECT", "Email" => "test@organization.com", "FromName" => "AGENCY CLIENT", "Business Name" => "Test Organization", "Address" => "123 ABC Street", "City" => "Indianapolis", "State" => "IN", "Zip" => "46202", "IsTestAccount" => true, "EditionID" => 3, "IsActive" => true);
	$postResult = $postOrganization->post();
	print_r('Post Status: '.($postResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResult->code."\n";
	print 'Message: '.$postResult->message."\n";	
	print 'Results Length: '. count($postResult->results)."\n";
	print 'Results: '."\n";
	print_r($postResult->results);
	print "\n---------------\n";
	
	
	// Retrieve newly created Organization
	print "Retrieve newly created Organization \n";
	$getOrganization = new ET_Organization();
	$getOrganization->authStub = $myclient;
	$getOrganization->props = array("ID", "Name", "IsActive");
	$getOrganization->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $CustomerKeyOfTestOrganization);
	$getResponse = $getOrganization->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print "\n---------------\n";
	
	$TestOrganizationID = $getResponse->results[0]->ID;
	$TestOrganizationName = $getResponse->results[0]->Name;
	$TestOrganizationIsActive = $getResponse->results[0]->IsActive;
	print_r('Test Organization ID: '.$TestOrganizationID.', Name: '.$TestOrganizationName.", IsActrive: " .$TestOrganizationIsActive."\n");
	print "\n---------------\n";
	
	
	// Update Organization
	print "Update Organization \n";
	$patchOrganization = new ET_Organization();
	$patchOrganization->authStub = $myclient;
	$patchOrganization->props = array("CustomerKey" =>  $CustomerKeyOfTestOrganization, "Name" => "New TestOrganizationName", "AccountType" => "PRO_CONNECT", "Email" => "test@organization.com", "FromName" => "AGENCY CLIENT", "Business Name" => "Test Organization", "Address" => "123 ABC Street", "City" => "Indianapolis", "State" => "IN", "Zip" => "46202", "IsTestAccount" => true, "EditionID" => 3, "IsActive" => true);
	$patchResult = $patchOrganization->patch();
	print_r('Patch Status: '.($patchResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$patchResult->code."\n";
	print 'Message: '.$patchResult->message."\n";	
	print 'Results Length: '. count($patchResult->results)."\n";
	print 'Results: '."\n";
	print_r($patchResult->results);
	print "\n---------------\n";

	
	
	// Retrieve updated Organization
	print "Retrieve updated Organization \n";
	$getOrganization = new ET_Organization();
	$getOrganization->authStub = $myclient;
	$getOrganization->props = array("ID", "Name", "IsActive");
	$getOrganization->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $CustomerKeyOfTestOrganization);
	$getResponse = $getOrganization->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print "\n---------------\n";
	
	$TestOrganizationID = $getResponse->results[0]->ID;
	$TestOrganizationName = $getResponse->results[0]->Name;
	$TestOrganizationIsActive = $getResponse->results[0]->IsActive;
	print_r('Test Organization ID: '.$TestOrganizationID.', Name: '.$TestOrganizationName.", IsActrive: " .$TestOrganizationIsActive."\n");
	print "\n---------------\n";
	
	
	// Delete Organization
	print "Delete Organization \n";
	$deleteOrganization = new ET_Organization();
	$deleteOrganization->authStub = $myclient;
	$deleteOrganization->props = array("CustomerKey" => $CustomerKeyOfTestOrganization, "AccountType" => "PRO_CONNECT");
	$deleteResponse = $deleteOrganization->delete();
	print_r('Delete Status: '.($deleteResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$deleteResponse->code."\n";
	print 'Message: '.$deleteResponse->message."\n";	
	print 'Results Length: '. count($deleteResponse->results)."\n";
	print 'Results: '."\n";
	print_r($deleteResponse->results);
	print "\n---------------\n";
	
	
	
	// Retrieve Organization to confirm deletion
	print "Retrieve Organization to confirm deletion \n";
	$getOrganization = new ET_Organization();
	$getOrganization->authStub = $myclient;
	$getOrganization->props = array("ID", "Name", "IsActive");
	$getOrganization->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $CustomerKeyOfTestOrganization);
	$getResponse = $getOrganization->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";
	print_r('More Results: '.($getResponse->moreResults ? 'true' : 'false')."\n");
	print 'Results Length: '. count($getResponse->results)."\n";
	print "\n---------------\n";
	
	$TestOrganizationID = $getResponse->results[0]->ID;
	$TestOrganizationName = $getResponse->results[0]->Name;
	$TestOrganizationIsActive = $getResponse->results[0]->IsActive;
	print_r('Test Organization ID: '.$TestOrganizationID.', Name: '.$TestOrganizationName.", IsActrive: " .$TestOrganizationIsActive."\n");
	print "\n---------------\n";
	

}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



