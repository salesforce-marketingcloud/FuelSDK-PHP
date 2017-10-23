<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;
use \SoapVar;
/**
 * This class represents configurations required for SOAP operation.
 */
class ET_Configure extends ET_Constructor
{
	/** 
	* Initializes a new instance of the class.
	* @param 	ET_Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	* @param    string      $objType 	Object name, e.g. "ImportDefinition", "DataExtension", etc
	* @param 	string 		$action 	Action names e.g. "create", "delete", "update", etc
	* @param 	array       $props 		Dictionary type array which may hold e.g. array('id' => '', 'key' => '')
	*/	
	function __construct($authStub, $objType, $action, $props)
	{
		$authStub->refreshToken();
		$configure = array();
		$configureRequest = array();
		$configureRequest['Action'] = $action;
		$configureRequest['Configurations'] = array();
		
		if (!ET_Util::isAssoc($props)) {
			foreach ($props as $value){
				$configureRequest['Configurations'][] = new SoapVar($value, SOAP_ENC_OBJECT, $objType, "http://exacttarget.com/wsdl/partnerAPI");
			}
		} else {
			$configureRequest['Configurations'][] = new SoapVar($props, SOAP_ENC_OBJECT, $objType, "http://exacttarget.com/wsdl/partnerAPI");
		} 

		$configure['ConfigureRequestMsg'] = $configureRequest;
		$return = $authStub->__soapCall("Configure", $configure, null, null , $out_header);
		parent::__construct($return, $authStub->__getLastResponseHTTPCode());
		
		if ($this->status){
			if (property_exists($return->Results, "Result")){
				if (is_array($return->Results->Result)){
					$this->results = $return->Results->Result;
				} else {
					$this->results = array($return->Results->Result);
				}
				if ($return->OverallStatus != "OK"){
					$this->status = false;
				}
			} else {
				$this->status = false;
			}
		}
	}
}
?>