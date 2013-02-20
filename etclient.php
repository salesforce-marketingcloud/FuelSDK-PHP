<?php
require('soap-wsse.php');
require('JWT.php');

class ETClient extends SoapClient {
	private $authToken, $authTokenExpiration, $internalAuthToken, $wsdlLoc, 
			$lastHTTPCode, $clientId, $clientSecret, $appsignature, $endpoint, $refreshKey;
		
	function __construct($getWSDL, $params = null) {	
		$config = include 'config.php';
		$this->wsdlLoc = $config['defaultwsdl'];
		$this->clientId = $config['clientid'];
		$this->clientSecret = $config['clientsecret'];
		$this->appsignature = $config['appsignature'];		


		if ($getWSDL){
			$this->CreateWSDL($this->wsdlLoc);	
		}
		
		if ($params && array_key_exists('jwt', $params)){
			$decodedJWT = JWT::decode($params['jwt'], $this->appsignature);					
			$this->authToken = $decodedJWT->request->user->oauthToken;			
			$this->internalAuthToken = $decodedJWT->request->user->internalOauthToken;
			$dv = new DateInterval('PT'.$decodedJWT->request->user->expiresIn.'S');
			$newexpTime = new DateTime();
			$this->authTokenExpiration = $newexpTime->add($dv);	
			$this->refreshKey = $decodedJWT->request->user->refreshToken;
		}		
		
		$this->refreshToken();

		try {
			$url = "https://www.exacttargetapis.com//platform/v1/endpoints/soap?access_token=".$this->authToken;
			$endpointResponse = restGet($url);			
			$endpointObject = json_decode($endpointResponse);			
			if ($endpointResponse && property_exists($endpointObject,"url")){		
				$this->endpoint = $endpointObject->url;			
			} else {
				throw new Exception('Unable to determine stack using /platform/v1/tokenContext:'.$endpointResponse );			
			}
			} catch (Exception $e) {
			throw new Exception('Unable to determine stack using /platform/v1/tokenContext: '.$e->getMessage());
		} 		
		parent::__construct('ExactTargetWSDL.xml', array('trace'=>1, 'exceptions'=>0));
		parent::__setLocation($this->endpoint);
	}
	
	function refreshToken() {
		
		try {							
			$currentTime = new DateTime();			
			if (is_null($this->authToken) || ($currentTime->diff($this->authTokenExpiration)->format('%i') > 5) ){
				
				$url = "https://auth.exacttargetapis.com/v1/requestToken?legacy=1";
				$jsonRequest = new stdClass(); 
				$jsonRequest->clientId = $this->clientId;
				$jsonRequest->clientSecret = $this->clientSecret;
				$jsonRequest->accessType = "offline";
				if (!is_null($this->refreshKey)){
					$jsonRequest->refreshToken = $this->refreshKey;
				}
				
				$authResponse = restPost($url, json_encode($jsonRequest));
				
				$authObject = json_decode($authResponse);
				
				if ($authResponse && property_exists($authObject,"accessToken")){		
					$this->authToken = $authObject->accessToken;
					$this->internalAuthToken = $authObject->legacyToken;
					$dv = new DateInterval('PT'.$authObject->expiresIn.'S');
					$newexpTime = new DateTime();
					$this->authTokenExpiration = $newexpTime->add($dv);	
					$this->refreshKey = $authObject->refreshToken;
				} else {
					throw new Exception('Unable to validate App Keys(ClientID/ClientSecret) provided, requestToken response:'.$authResponse );			
				}				
			}
		} catch (Exception $e) {
			throw new Exception('Unable to validate App Keys(ClientID/ClientSecret) provided.: '.$e->getMessage());
		}
		
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
		//print_r($content);
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
	public $status, $code, $message, $results, $request_id, $moreResults;	
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
	/*
	function __construct() {
		$this->status = false;
	}
	*/
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
			if ($return->OverallStatus != "OK" && $return->OverallStatus != "MoreDataAvailable")
			{
				$this->status = false;
				$this->message = $return->OverallStatus;
			}

			$this->moreResults = false;
			
			if ($return->OverallStatus == "MoreDataAvailable") {				
				$this->moreResults = true;
			}
				
			$this->request_id = $return->RequestID;
		}	
	}
}

class et_Continue extends et_Constructor {	
	function __construct($authStub, $request_id) {
		$rrm = array(); 
		$request = array(); 
		$retrieveRequest = array(); 		
		
		$retrieveRequest["ContinueRequest"] = $request_id;
		$retrieveRequest["ObjectType"] = null ;

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
			
			$this->moreResults = false;
			
			if ($return->OverallStatus == "MoreDataAvailable") {				
				$this->moreResults = true;
			}
			
			if ($return->OverallStatus != "OK" && $return->OverallStatus != "MoreDataAvailable")
			{
				$this->status = false;
				$this->message = $return->OverallStatus;
			}
			
			$this->request_id = $return->RequestID;
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

class et_Patch extends et_Constructor {	
	function __construct($authStub, $objType, $props) {				
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
	function __construct($authStub, $objType, $props) {	
	
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
	protected $obj, $lastRequestID;	
}

class et_GetObject extends et_BaseObject{
	
	public function get() {		
		$response = new et_Get($this->authStub, $this->obj, $this->props, $this->filter);
		$this->lastRequestID = $response->request_id;		
		return $response;
	}
	
	public function getMoreResults() {		
		$response = new et_Continue($this->authStub, $this->lastRequestID);
		$this->lastRequestID = $response->request_id;
		return $response;
	}
	
	public function info() {		
		$response = new et_Info($this->authStub, $this->obj);
		return $response;
	}	
}

class et_CRUDObject extends et_GetObject{

	public function post() {		
		$response = new et_Post($this->authStub, $this->obj, $this->props);
		return $response;
	}

	public function patch() {		
		$response = new et_Patch($this->authStub, $this->obj, $this->props);
		return $response;
	}
	
	public function delete() {		
		$response = new et_Delete($this->authStub, $this->obj, $this->props);
		return $response;
	}	
}



class ET_Subscriber extends et_CRUDObject {		
	function __construct() {	
		$this->obj = "Subscriber";
	}	
}

class ET_DataExtension extends et_CRUDObject {
	public  $columns;
	function __construct() {	
		$this->obj = "DataExtension";
	}
	
	public function post() {				
		$this->props["Fields"] = array("Field"=>array());		
		if (!is_null($this->columns) && is_array($this->columns)){
			foreach ($this->columns as $column){
				array_push($this->props['Fields']['Field'], $column);
			}	
		}
		$response = parent::post();		
		unset($this->props["Fields"]);		
		return $response;
	}
	
	public function patch() {				
		$this->props["Fields"] = array("Field"=>array());				
		foreach ($this->columns as $column){
			array_push($this->props['Fields']['Field'], $column);
		}	
		print_r($this->props);
		$response = parent::patch();		
		unset($this->props["Fields"]);		
		return $response;
	}
}

class ET_DataExtension_Column extends et_GetObject {
	function __construct() {	
		$this->obj = "DataExtensionField";
	}
	
	public function get() {	
		$fixCustomerKey = false;
		
		if ($this->filter && array_key_exists('Property', $this->filter) && $this->filter['Property'] == "CustomerKey" )
		{	
			$this->filter['Property'] = "DataExtension.CustomerKey";
			$fixCustomerKey = true;
		}				
		$response =  parent::get();	
		if ($fixCustomerKey )
		{
			$this->filter['Property'] = "CustomerKey";
		}
		
		return $response;
	}
}

class ET_DataExtension_Row extends et_CRUDObject {
	public $Name, $CustomerKey;
	function __construct() {	
		$this->obj = "DataExtensionObject";
	}
	
	public function get() {	
		$this->getName();		
		$this->obj = "DataExtensionObject[".$this->Name."]";		
		$response = parent::get();
		$this->obj = "DataExtensionObject";
		return $response;
	}
	
	public function post(){
		$this->getCustomerKey();
		$originalProps = $this->props;		
		$overrideProps = array();
		$fields = array();
		
		foreach ($this->props as $key => $value){
			$fields[]  = array("Name" => $key, "Value" => $value);	
		}		
		$overrideProps['CustomerKey'] = $this->CustomerKey;
		$overrideProps['Properties'] = array("Property"=> $fields);
		
		$this->props = $overrideProps;		
		$response = parent::post();		
		$this->props = $originalProps;
		return $response;
	}
	
	public function patch(){
		$this->getCustomerKey();
		$originalProps = $this->props;		
		$overrideProps = array();
		$fields = array();
		
		foreach ($this->props as $key => $value){
			$fields[]  = array("Name" => $key, "Value" => $value);	
		}		
		$overrideProps['CustomerKey'] = $this->CustomerKey;
		$overrideProps['Properties'] = array("Property"=> $fields);
		
		$this->props = $overrideProps;		
		$response = parent::patch();		
		$this->props = $originalProps;
		return $response;
	}
	
	public function delete(){
		$this->getCustomerKey();
		$originalProps = $this->props;		
		$overrideProps = array();
		$fields = array();
		
		foreach ($this->props as $key => $value){
			$fields[]  = array("Name" => $key, "Value" => $value);	
		}		
		$overrideProps['CustomerKey'] = $this->CustomerKey;
		$overrideProps['Keys'] = array("Key"=> $fields);
		
		$this->props = $overrideProps;		
		$response = parent::delete();		
		$this->props = $originalProps;
		return $response;
	}
	
	private function getName() {
		if (is_null($this->Name)){
			if (is_null($this->CustomerKey))
			{
				throw new Exception('Unable to process request due to CustomerKey and Name not being defined on ET_DataExtension_Row');			
			} else {
				$nameLookup = new ET_DataExtension();
				$nameLookup->authStub = $this->authStub;
				$nameLookup->props = array("Name","CustomerKey");
				$nameLookup->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $this->CustomerKey);
				$nameLookupGet = $nameLookup->get();
				if ($nameLookupGet->status && count($nameLookupGet->results) == 1){
					$this->Name = $nameLookupGet->results[0]->Name;
				} else {
					throw new Exception('Unable to process request due to unable to find DataExtension based on CustomerKey');				
				}								
			}					
		}		
	}
	private function getCustomerKey() {
		if (is_null($this->CustomerKey)){
			if (is_null($this->Name))
			{
				throw new Exception('Unable to process request due to CustomerKey and Name not being defined on ET_DataExtension_Row');			
			} else {
				$nameLookup = new ET_DataExtension();
				$nameLookup->authStub = $this->authStub;
				$nameLookup->props = array("Name","CustomerKey");
				$nameLookup->filter = array('Property' => 'Name','SimpleOperator' => 'equals','Value' => $this->Name);
				$nameLookupGet = $nameLookup->get();
				if ($nameLookupGet->status && count($nameLookupGet->results) == 1){
					$this->CustomerKey = $nameLookupGet->results[0]->CustomerKey;
				} else {
					throw new Exception('Unable to process request due to unable to find DataExtension based on Name');				
				}								
			}					
		}		
	}	
}


class ET_List extends et_CRUDObject {		
	function __construct() {	
		$this->obj = "List";
	}	
}

class ET_SentEvent extends et_GetObject {		
	function __construct() {	
		$this->obj = "SentEvent";
	}	
}

class ET_OpenEvent extends et_GetObject {		
	function __construct() {	
		$this->obj = "OpenEvent";
	}	
}

class ET_BounceEvent extends et_GetObject {		
	function __construct() {	
		$this->obj = "BounceEvent";
	}	
}

class ET_UnsubEvent extends et_GetObject {		
	function __construct() {	
		$this->obj = "UnsubEvent";
	}	
}

class ET_ClickEvent extends et_GetObject {		
	function __construct() {	
		$this->obj = "ClickEvent";
	}	
}

class ET_TriggeredSend extends et_CRUDObject {
	public  $subscribers;
	function __construct() {	
		$this->obj = "TriggeredSendDefinition";
	}

	public function Send() {
		$tscall = array("TriggeredSendDefinition" => $this->props , "Subscribers" => $this->subscribers);
		$response = new et_Post($this->authStub, "TriggeredSend", $tscall);
		return $response;
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
