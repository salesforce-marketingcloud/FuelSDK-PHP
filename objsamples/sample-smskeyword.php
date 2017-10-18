<?php
include_once('tests/UnitBootstrap.php');
use FuelSdk\ET_Client;
use FuelSdk\ET_SMSKeyword;

try {
    $myclient = new ET_Client(true);
    $postKey = new ET_SMSKeyword();
	$postKey->authStub = $myclient;
    $postKey->props = array("ShortCode" => "29860", "Keyword"=> "AWESOMENESS", "CountryCode"=> "US" );	
    $postResult = $postKey->post();
    print_r($postResult);
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>    