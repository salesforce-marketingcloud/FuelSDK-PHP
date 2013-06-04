<?php

require('../ET_Client.php');
try {	
	$myclient = new ET_Client();

	$NameOfAttribute = 'PHPSDKTestAttribute';
	
	print "Create ProfileAttribute \n";
	$postProfileAttribute = new ET_ProfileAttribute();
	$postProfileAttribute->authStub = $myclient;
	$postProfileAttribute->props = array("Name" => $NameOfAttribute, "PropertyType"=>"string", "Description"=>"New Attribute from the SDK", "IsRequired"=>"false", "IsViewable"=>"false", "IsEditable"=>"true", "IsSendTime"=>"false");
	$postResponse = $postProfileAttribute->post();
	print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResponse->code."\n";
	print 'Message: '.$postResponse->message."\n";	
	print 'Results Length: '. count($postResponse->results)."\n";
	print 'Results: '."\n";
	print_r($postResponse->results);
	print "\n---------------\n";
	
	print "Retrieve All ProfileAttributes\n";
	$getProfileAttribute = new ET_ProfileAttribute();
	$getProfileAttribute->authStub = $myclient;
	$getResponse = $getProfileAttribute->get();
	print_r('Get Status: '.($getResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResponse->code."\n";
	print 'Message: '.$getResponse->message."\n";	
	print 'Results Length: '. count($getResponse->results)."\n";
	print 'Results: '."\n";
	print_r($getResponse->results);
	print "\n---------------\n";
	
	print "Update ProfileAttribute \n";
	$patchProfileAttribute = new ET_ProfileAttribute();
	$patchProfileAttribute->authStub = $myclient;
	$patchProfileAttribute->props = array("Name" => $NameOfAttribute, "PropertyType"=>"string");
	$patchResponse = $patchProfileAttribute->patch();
	print_r('Patch Status: '.($patchResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$patchResponse->code."\n";
	print 'Message: '.$patchResponse->message."\n";	
	print 'Results Length: '. count($patchResponse->results)."\n";
	print 'Results: '."\n";
	print_r($patchResponse->results);
	print "\n---------------\n";

	print "Delete ProfileAttribute \n";
	$deleteProfileAttribute = new ET_ProfileAttribute();
	$deleteProfileAttribute->authStub = $myclient;
	$deleteProfileAttribute->props = array("Name" => $NameOfAttribute, "PropertyType"=>"string");
	$deleteResponse = $deleteProfileAttribute->delete();
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



