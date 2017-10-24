<?php
include_once('tests/UnitBootstrap.php');
use FuelSdk\ET_Client;
use FuelSdk\ET_DataExtractActivity;
use FuelSdk\ET_ExtractDescription;


try {
    $myclient = new ET_Client();
	$extracttype = "Data Extension Extract";
	$filename = "extract_from_php.csv";
	$DECustKey = "017dce26-b61f-43c2-bb15-0e46de82d177";

	$extractdesc = new ET_ExtractDescription();
	$extractdesc->authStub = $myclient;
	$extractdesc->props = array("ID","CustomerKey","Name", "Description","InteractionObjectID", "ObjectID","PartnerKey","CreatedDate","Client.ID","EventType","BatchID","TriggeredSendDefinitionObjectID","PartnerKey");
	$extractResponse = $extractdesc->get();
	$extractmap = array();
	foreach($extractResponse->results as $obj){
		$extractmap[$obj->Name] = $obj->ObjectID;
	}
	//print_r($extractmap);


	print "Start Data Extraction\n";
	$startImport = new ET_DataExtractActivity();
	$startImport->authStub = $myclient;
	//$startImport->props["ID"] = $extractmap[$extracttype];

	$Parameters= array( 				
		"Parameter"=>array(
			array("Name"=>"StartDate", "Value"=>"2017-06-01 01:00 AM"),
			array("Name"=>"EndDate", "Value"=>"2017-09-01 01:00 AM"),
			array("Name"=>"OutputFileName", "Value"=>$filename),
			array("Name"=>"DECustomerKey", "Value"=>$DECustKey),
			array("Name"=>"HasColumnHeaders", "Value"=>"true"),
			array("Name"=>"_AsyncID", "Value"=>"0")
		)
	);

	$startImport->props = array("ID"=>$extractmap[$extracttype], "Options"=>"", "Parameters"=>$Parameters);

	$startResponse = $startImport->start();
	print_r('Status: '.$startResponse->status ."\n");
	print 'Code: '.$startResponse->code."\n";
	print 'Req ID: '.$startResponse->request_id."\n";	
	print 'Results Length: '. count($startResponse->results)."\n";
	print 'Results: '."\n";
	print_r($startResponse->results);
	print "\n---------------\n";	
	print_r($startResponse);

}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>