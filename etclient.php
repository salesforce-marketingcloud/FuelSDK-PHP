<?php
require('soap-wsse.php');

class ETClient extends SoapClient {
	private $authToken, $authTokenExpiration, $internalAuthToken, $wsdlLoc, $lastHTTPCode, $clientId, $clientSecret;
		
	function __construct($istack, $iclientId, $iclientSecret, $getWSDL) {	
		$this->clientId = $iclientSecret;
		$this->clientSecret = $iclientSecret;

		$stacks = array ('S1' => array('wsdl' => 'https://webservice.exacttarget.com/ETFramework.wsdl', 'endpoint'=>'https://webservice.exacttarget.com/Service.asmx'), 
							'S4' => array('wsdl' =>'https://webservice.s4.exacttarget.com/ETFramework.wsdl', 'endpoint'=>'https://webservice.s4.exacttarget.com/Service.asmx'),
							'S6' => array('wsdl' =>'https://webservice.s6.exacttarget.com/ETFramework.wsdl', 'endpoint'=>'https://webservice.s6.exacttarget.com/Service.asmx'));
		
		if ($getWSDL){
			$this->CreateWSDL($stacks[$istack]["wsdl"]);	
		}
		
		$url = "https://auth.exacttargetapis.com/v1/requestToken?legacy=1";
		$jsonRequest = new stdClass(); 
		$jsonRequest->clientId = $iclientId;
		$jsonRequest->clientSecret = $iclientSecret;
		$jsonRequest->accessType = "offline";
		$authResponse = restPost($url, json_encode($jsonRequest));
		
		$authObject = json_decode($authResponse);
		
		if ($authResponse && property_exists($authObject,"accessToken")){		
			$authObject = json_decode($authResponse);
			$this->authToken = $authObject->accessToken;
			$this->internalAuthToken = $authObject->legacyToken;
			$dv = new DateInterval('PT'.$authObject->expiresIn.'S');
			$newexpTime = new DateTime();
			$this->authTokenExpiration = $newexpTime->add($dv);				
		} else {
			throw new Exception('Unable to validate App Keys(ClientID/ClientSecret) provided.');			
		}						
		
		parent::__construct('ETFramework.wsdl', array('trace'=>1, 'exceptions'=>0));		
		parent::__setLocation($stacks[$istack]["endpoint"]);
	}
	
	function __getLastResponseHTTPCode(){

		return $this->lastHTTPCode;		
	}
	
	function CreateWSDL($wsdlLoc) {
		
		try{
			$getNewWSDL = true;
			
			$remoteTS = $this->GetLastModifiedDate($wsdlLoc);
			
			if (file_exists("ExactTargetWSDL.xml")){
				$localTS = filemtime("ExactTargetWSDL.xml");
				if ($remoteTS <= $localTS) 
				{
					$getNewWSDL = false;
				}		
			}
			
			if ($getNewWSDL){
				$newWSDL = file_get_contents($wsdlLoc);
				file_put_contents("ExactTargetWSDL.xml", $newWSDL);
			}	
		}
		catch (Exception $e) {
			throw new Exception('Unable to store local copy of WSDL file'."\n");
		}
	}
	
	function GetLastModifiedDate($remotepath) {
			$curl = curl_init($remotepath);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_NOBODY, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FILETIME, true);
			
			$result = curl_exec($curl);
			
			if ($result === false) {
				die (curl_error($curl)); 
			}
			
			return curl_getinfo($curl, CURLINFO_FILETIME);
		
	}
				
	function __doRequest($request, $location, $saction, $version) {
		$doc = new DOMDocument();
		$doc->loadXML($request);
		
		$objWSSE = new WSSESoap($doc);
		
		$objWSSE->addUserToken("*", "*", FALSE);
		$objWSSE->addOAuth($this->internalAuthToken);
		
		$content = utf8_encode($objWSSE->saveXML());
		$content_length = strlen($content); 
		
		$headers = array("Content-Type: text/xml","SOAPAction: ".$saction);

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $location);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $content);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$output = curl_exec($ch);
		$this->lastHTTPCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch); 
						
		return $output;
	}
}

class et_Constructor {
	public $status, $code, $message, $results;	
	function __construct($soapresponse, $httpcode) {
		
		$this->code = $httpcode;
			
		if(is_soap_fault($soapresponse)) {
			$this->status = false;
			$this->message = "SOAP Fault: (faultcode: {$soapresponse->faultcode}, faultstring: {$soapresponse->faultstring})";
			$this->message = "{$soapresponse->faultcode} {$soapresponse->faultstring})";
		} else {
			$this->status = true;
		}
	}
}

class et_Get extends et_Constructor {
	function __construct($authStub, $objType, $props, $filter) {	
		$rrm = array(); 
		$request = array(); 
		$retrieveRequest = array(); 
		
		// If Props is not sent then Info will be used to find all retrievable properties
		if (is_null($props)){	
			$props = array();
			$info = new et_Info($authStub, $objType);
			if (is_array($info->results)){			
				foreach ($info->results as $property){				
					if($property->IsRetrievable){				
						$props[] = $property->Name;
					}
				}	
			}
		}
		
		if ($props !== array_values($props)){
			$retrieveProps = array();
			foreach ($props as $key => $value){				
				if (!is_array($value))
				{
					$retrieveProps[] = $key;
				}
				$retrieveRequest["Properties"] = $retrieveProps;
			}
		} else {
			$retrieveRequest["Properties"] = $props;	
		}
		
		$retrieveRequest["ObjectType"] = $objType;
		if ($filter){
			$retrieveRequest["Filter"] = new SoapVar($filter, SOAP_ENC_OBJECT, 'SimpleFilterPart', "http://exacttarget.com/wsdl/partnerAPI");
		}
		$request["RetrieveRequest"] = $retrieveRequest;
		$rrm["RetrieveRequestMsg"] = $request;
				
		$return = $authStub->__soapCall("Retrieve", $rrm, null, null , $out_header);
		parent::__construct($return, $authStub->__getLastResponseHTTPCode());
					
		if ($this->status){
			if (property_exists($return, "Results")){
				// We always want the results property when doing a retrieve to be an array
				if (is_array($return->Results)){
					$this->results = $return->Results;
				} else {
					$this->results = array($return->Results);
				}
			} else {
				$this->results = array();
			} 
			if ($return->OverallStatus != "OK")
			{
				$this->status = false;
				$this->message = $return->OverallStatus;
			}
		}		
	}
}

class et_Info extends et_Constructor {
	function __construct($authStub, $objType) {	
		$drm = array(); 
		$request = array(); 
		$describeRequest = array(); 
		
		$describeRequest["ObjectDefinitionRequest"] = array("ObjectType" => $objType);

		$request["DescribeRequests"] = $describeRequest;
		$drm["DefinitionRequestMsg"] = $request;
		
		$return = $authStub->__soapCall("Describe", $drm, null, null , $out_header);
		parent::__construct($return, $authStub->__getLastResponseHTTPCode());
		
		if ($this->status){
			if (property_exists($return->ObjectDefinition, "Properties")){
				$this->results = $return->ObjectDefinition->Properties;				
			} else {
				$this->status = false;				
			}
		}		
	}
}

class et_Post extends et_Constructor {	
	function __construct($authStub, $objType, $props = null) {					
		$cr = array(); 
		$objects = array(); 
		$object = $props; 				
		
		$objects["Objects"] = new SoapVar($props, SOAP_ENC_OBJECT, $objType, "http://exacttarget.com/wsdl/partnerAPI");
		$objects["Options"] = "";
		$cr["CreateReqest"] = $objects;
		
		$return = $authStub->__soapCall("Create", $cr, null, null , $out_header);
		parent::__construct($return, $authStub->__getLastResponseHTTPCode());		
		
		if ($this->status){
			if (property_exists($return, "Results")){
				// We always want the results property when doing a retrieve to be an array
				if (is_array($return->Results)){
					$this->results = $return->Results;
				} else {
					$this->results = array($return->Results);
				}
			} else {
				$this->status = false;
				
			}
			if ($return->OverallStatus != "OK")
			{
				$this->status = false;
			}
		}			
	}
}

class et_Put extends et_Constructor {	
	function __construct($authStub, $objType, $props = null) {				
		$cr = array(); 
		$objects = array(); 
		$object = $props; 				
		
		$objects["Objects"] = new SoapVar($props, SOAP_ENC_OBJECT, $objType, "http://exacttarget.com/wsdl/partnerAPI");
		$objects["Options"] = "";
		$cr["UpdateRequest"] = $objects;
		
		$return = $authStub->__soapCall("Update", $cr, null, null , $out_header);
		parent::__construct($return, $authStub->__getLastResponseHTTPCode());		
		
		if ($this->status){
			if (property_exists($return, "Results")){
				// We always want the results property when doing a retrieve to be an array
				if (is_array($return->Results)){
					$this->results = $return->Results;
				} else {
					$this->results = array($return->Results);
				}
			} else {
				$this->status = false;
				
			}
			if ($return->OverallStatus != "OK")
			{
				$this->status = false;
			}
		}	
	}
}

class et_Delete extends et_Constructor {	
	function __construct($authStub, $objType, $props = null) {	
	
		$cr = array(); 
		$objects = array(); 
		$object = $props; 				
		
		$objects["Objects"] = new SoapVar($props, SOAP_ENC_OBJECT, $objType, "http://exacttarget.com/wsdl/partnerAPI");
		$objects["Options"] = "";
		$cr["DeleteRequest"] = $objects;
		
		$return = $authStub->__soapCall("Delete", $cr, null, null , $out_header);
		parent::__construct($return, $authStub->__getLastResponseHTTPCode());		
		
		if ($this->status){
			if (property_exists($return, "Results")){
				// We always want the results property when doing a retrieve to be an array
				if (is_array($return->Results)){
					$this->results = $return->Results;
				} else {
					$this->results = array($return->Results);
				}
			} else {
				$this->status = false;
				
			}
			if ($return->OverallStatus != "OK")
			{
				$this->status = false;
			}
		}	
	}
}

class et_BaseObject {
	public  $authStub, $props, $filter;
	protected $obj;	
}

class et_CRUDObject extends et_BaseObject{

	public function Get() {		
		$response = new et_Get($this->authStub, $this->obj, $this->props, $this->filter);
		return $response;
	}
	
	public function Post() {		
		$response = new et_Post($this->authStub, $this->obj, $this->props);
		return $response;
	}

	public function Put() {		
		$response = new et_Put($this->authStub, $this->obj, $this->props);
		return $response;
	}
	
	public function Delete() {		
		$response = new et_Delete($this->authStub, $this->obj, $this->props);
		return $response;
	}
	
	public function Info() {		
		$response = new et_Info($this->authStub, $this->obj);
		return $response;
	}	
}

class et_List extends et_CRUDObject {		
	function __construct() {	
		$this->obj = "List";
	}	
}

class et_Subscriber extends et_CRUDObject {		
	function __construct() {	
		$this->obj = "Subscriber";
	}	
}

function restGet($url) {
	$ch = curl_init();
	
	// Uses the URL passed in that is specific to the API used
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPGET, true);
	
	// Need to set ReturnTransfer to True in order to store the result in a variable
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	// Disable VerifyPeer for SSL
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	$outputJSON = curl_exec($ch);
	
	// If there are no errors then we just pass the response returned back	
	if(!curl_errno($ch)) {
		curl_close($ch);
		return $outputJSON;
	} else {
		// If there are errors then return a false
		curl_close($ch);
		return false;
	}
}

// Function for calling a Fuel API using POST
/**
 * @param string      $url    The resource URL for the REST API
 * @param string      $content    A string of JSON which will be passed to the REST API
	*
 * @return string     The response payload from the REST service
 */
function restPost($url, $content) {
	$ch = curl_init();
	
	// Uses the URL passed in that is specific to the API used
	curl_setopt($ch, CURLOPT_URL, $url);	
	
	// When posting to a Fuel API, content-type has to be explicitly set to application/json
	$headers = array("Content-Type: application/json");
	curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
	
	// The content is the JSON payload that defines the request
	curl_setopt ($ch, CURLOPT_POSTFIELDS, $content);
	
	//Need to set ReturnTransfer to True in order to store the result in a variable
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	// Disable VerifyPeer for SSL
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	$outputJSON = curl_exec($ch);
	
	// If there are no errors then we just pass the response returned back	
	if(!curl_errno($ch)) {
		curl_close($ch);
		return $outputJSON;
	} else {
		// If there are errors then return a false
		curl_close($ch);
		return false;
	}
}


?>
