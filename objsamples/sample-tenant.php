<?php

require('../ET_Client.php');
try {

	$testTenantKey = "testTenantKey";

	$myclient = new ET_OEM_Client();

	// Retrieve All Tenants 
	print "Retrieve All Tenants \n";
	$getResult = $myclient->GetTenants();
	print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResult->code."\n";
	print 'Message: '.$getResult->message."\n";
	print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
	print 'Results Length(Items): '. count($getResult->results)."\n";
	//print 'Results: "\n"';
	//print_r($getResult->results);
	print "\n---------------\n";
	
	
	// Create a new Tenant
	print "Create a new Tenant \n";
	$newTenant = array("key" => $testTenantKey, "address" => "Tenant Rd", "businessName" => "Tenant 2", "city" => "Indianapolis", "country" => "USA", "email" => "platformservices@exacttarget.com", "fax" => "123-123-1234", "fromName" => "platformservices@exacttarget.com", "name" => "Tenant 2", "phone" => "123-123-1234", "state" => "IN", "zip" => "46123");
	$postResponse = $myclient->CreateTenant($newTenant);
	print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResponse->code."\n";
	print 'Message: '.$postResponse->message."\n";
	print 'Results Length: '. count($postResponse->results)."\n";
	print 'Results: '."\n";
	print_r($postResponse->results);
	print "\n---------------\n";
	
	
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>

