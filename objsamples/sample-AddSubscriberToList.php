<?php
include_once('tests/UnitBootstrap.php');
use FuelSdk\ET_Client;
use FuelSdk\ET_List;

try {	

	$myclient = new ET_Client();	
	$NewListName = "PHPSDKList";
	$EmailAddressesArray = array("PHPSDKListSubscriber121@bh.exacttarget.com", "PHPSDKListSubscriber212@bh.exacttarget.com", "PHPSDKListSubscriber312@bh.exacttarget.com", "PHPSDKListSubscriber412@bh.exacttarget.com", "PHPSDKListSubscriber512@bh.exacttarget.com", "PHPSDKListSubscriber612@bh.exacttarget.com");
	
	// Example using AddSubscriberToList() method
	// Typically this method will be used with a pre-existing list but for testing purposes one is being created.

	// Create List
	print "Create List \n";
	$postContent = new ET_List();
	$postContent->authStub = $myclient;
	$postContent->props = array("ListName" => $NewListName, "Description" => "This list was created with the PHPSDK", "Type" => "Private");
	$postResponse = $postContent->post();
	print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResponse->code."\n";
	print 'Message: '.$postResponse->message."\n";	
	print 'Results Length: '. count($postResponse->results)."\n";
	print 'Results: '."\n";
	print_r($postResponse->results);
	print "\n---------------\n";
	
	if ($postResponse->status){		
		$newListID = $postResponse->results[0]->NewID;
		// Adding Subscriber To a List
		print "Adding Subscriber To a List \n";
		$response = $myclient->AddSubscriberToList("AddSubTesting@bh.exacttarget.com", array($newListID));			
		print_r('Response Status: '.($response->status ? 'true' : 'false')."\n");
		print 'Code: '.$response->code."\n";
		print 'Message: '.$response->message."\n";	
		print 'Results Length: '. count($response->results)."\n";
		print "Results: \n";
		print_r($response->results);
		print "\n---------------\n";
		
		// Delete List
		print "Delete List \n";
		$deleteList = new ET_List();
		$deleteList->authStub = $myclient;
		$deleteList->props = array("ID" => $newListID);
		$deleteResponse = $deleteList->delete();
		print_r('Delete Status: '.($deleteResponse->status ? 'true' : 'false')."\n");
		print 'Code: '.$deleteResponse->code."\n";
		print 'Message: '.$deleteResponse->message."\n";	
		print 'Results Length: '. count($deleteResponse->results)."\n";
		print 'Results: '."\n";
		print_r($deleteResponse->results);
		print "\n---------------\n";
		
	}
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



