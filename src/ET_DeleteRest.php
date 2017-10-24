<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;

/**
 * This class represents the DELETE operation for REST service.
 */
class ET_DeleteRest extends ET_Constructor
{
	/** 
	* Initializes a new instance of the class.
	* @param 	ET_Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	* @param 	string 		$url 		The endpoint URL
	*/	
	function __construct($authStub, $url, $qs="")
	{
//		$restResponse = ET_Util::restDelete($url, $authStub);			
		$restResponse = ET_Util::restDelete($url, $authStub, $qs);			
		parent::__construct($restResponse->body, $restResponse->httpcode, true);							
	}
}
?>