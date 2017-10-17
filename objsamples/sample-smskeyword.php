<?php
spl_autoload_register( function($class_name) {
    include_once 'src/'.$class_name.'.php';
});
date_default_timezone_set('UTC');
try {
    $myclient = new ET_Client(true);
    $postKey = new ET_SMSKeyword();
	$postKey->authStub = $myclient;
    $postKey->props = array("ShortCode" => "29860", "Keyword"=> "AWESOME", "CountryCode"=> "US" );	
    $postResult = $postKey->post();
    print_r($postResult);
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>    