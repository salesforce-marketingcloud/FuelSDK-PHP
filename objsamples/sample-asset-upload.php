<?php

// include_once('src/ET_Client.php');
// include_once('src/ET_Asset.php');
spl_autoload_register( function($class_name) {
    include_once 'src/'.$class_name.'.php';
});
date_default_timezone_set('UTC');

try {	

	$myclient = new ET_Client();
	
	// Retrieve All Assets with GetMoreResults
/*	print "Retrieve All Assets with GetMoreResults \n";
	$getAsset = new ET_Asset();
	$getAsset->authStub = $myclient;
	$getResult = $getAsset->get();
	print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResult->code."\n";
	print 'Message: '.$getResult->message."\n";
	print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
	print 'Results Length(Items): '. count($getResult->results->items)."\n";
	
	print 'Results: "\n"';
	print_r($getResult);
	print "\n---------------\n";
	
	print 'Asset:\n' ;
//	print_r($getAsset);
	
	while ($getResult->moreResults) {
		print "Continue Retrieve All Assets with GetMoreResults \n";
		$getResult = $getAsset->GetMoreResults();
		print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
		print 'Code: '.$getResult->code."\n";
		print 'Message: '.$getResult->message."\n";
		print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
		print 'Results Length(Items): '. count($getResult->results->items)."\n";
		print "\n---------------\n";
	}	 
*/	
	$base64EncodedString = base64_encode("This is my file contents of a text file");
/*	
	// Create a new Asset Base 64
print "Create a new Asset Base 64 \n";
	$postAsset = new ET_Asset();
	$postAsset->authStub = $myclient;
//	$postAsset->props = array("fileName" => "TestFile.txt", "mimeType" => "text/plain", "fileData" => $base64EncodedString, "displayName" => "TestFile.txt", "customerKey" => md5(uniqid()), "description" => "");

	$postAsset->props = array("name" => "Rick-Roll".uniqid(), 
//	"mimeType" => "text/plain", 
//	"fileData" => $base64EncodedString, 
//	"displayName" => "TestFile.txt", 
//	"customerKey" => md5(uniqid()), 
	"assetType" =>	array("id" => 23),
	"fileProperties" => array("sourceFileUrl" => "http://images.mentalfloss.com/sites/default/files/styles/article_640x430/public/rickrollheader.png"),
	"description" => "Ladies and Gentlemen, Rick Astley");



	$postResponse = $postAsset->Post();
	print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResponse->code."\n";
	print 'Message: '.$postResponse->message."\n";
	print 'Results Length: '. count($postResponse->results)."\n";
	
	print 'Results: '."\n";
	print_r($postResponse);
	print "\n---------------\n";
	echo "ID: \n";

	$assetID = $postResponse->results->id;
	print $assetID;


	print "Retrieve All Assets with GetMoreResults \n";
	$getAsset = new ET_Asset();
	$getAsset->authStub = $myclient;
	
	$getAsset->props = array("id" => $assetID);
	$getResult = $getAsset->get();

	print_r('Get Status: '.($getResult->status ? 'true' : 'false')."\n");
	print 'Code: '.$getResult->code."\n";
	print 'Message: '.$getResult->message."\n";
	print_r('More Results: '.($getResult->moreResults ? 'true' : 'false')."\n");
//	print 'Results Length(Items): '. count($getResult->results->items)."\n";
	
	print 'Results: "\n"';
	print_r($getResult);
	print "\n---------------\n";
*/
	
	print 'Asset:\n' ;
//	print_r($postAsset);
	$msg = '{
        "forwardHtml": {
            "content": "",
            "use": "html"
        },
        "forwardText": {
            "content": "",
            "generateFrom": "forwardHtml"
        },
        "preheader": {
            "content": "",
            "generateFrom": "html"
        },
        "subjectline": {
            "content": "HelloWorld - I am a MessageModel Subjectline",
            "contentType": "application\\/vnd.exacttarget.com\\/email\\/Message; kind=subjectline",
            "positioning": {
                "key": "defaultPostioning"
            },
            "status": {
                "id": 2,
                "name": "Published"
            },
            "tags": [
                "awesome",
                "View"
            ]
        },
        "text": {
            "content": "",
            "generateFrom": "html"
        },
        "viewAsAWebPage": {
            "content": "",
            "use": "html"
        }
    }';
	// Create a new Asset File Path
	print "Create a new Asset Message \n";
	$postAsset2 = new ET_Asset();
	$postAsset2->authStub = $myclient;

//	$postAsset2->props = array("filePath" => $_SERVER['PWD'] . '/sample-asset-TestFilePath.txt');
	$postAsset2->props = array(
		"name" => "AwesomeMessage".uniqid(), 
		"assetType" =>	array("id" => 209),
		"description" => "Ladies and Gentlemen, Rick Astley",
		"Content" => "Hello World",
		"ContentType" => "TEXT",
//		"SuperContent" => "<img src=\"https://image.s1.qa1.exacttarget.com/lib/fe611570726607797212/m/1/testImage.png\" alt=\"testImage2.png\">",
//		"views" => json_decode($msg)

	);

//	$postAsset2->props["name"] = "My test asset name";
//	$postAsset2->props["AssetType"] = array("id"=>4);;
	
//	print_r($postAsset2);
	$postResponse = $postAsset2->post();
	print_r('Post Status: '.($postResponse->status ? 'true' : 'false')."\n");
	print 'Code: '.$postResponse->code."\n";
	print 'Message: '.$postResponse->message."\n";
	print 'Results Length: '. count($postResponse->results)."\n";
	
	print 'Results: '."\n";
	print_r($postResponse);
	print "\n---------------\n";	

}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>



