<?php
require('soap-wsse.php');
require('JWT.php');

class ET_Client extends SoapClient {
	public $authToken;
	private $authTokenExpiration, $internalAuthToken, $wsdlLoc,
			$lastHTTPCode, $clientId, $clientSecret, $appsignature, $endpoint, $refreshKey;
		
	function __construct($getWSDL = false, $params = null) {	
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
			$endpointObject = json_decode($endpointResponse->body);			
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
			if (is_null($this->authToken) || ($currentTime->diff($this->authTokenExpiration)->format('%i') < 5) ){
				$url = "https://auth.exacttargetapis.com/v1/requestToken?legacy=1";
				$jsonRequest = new stdClass(); 
				$jsonRequest->clientId = $this->clientId;
				$jsonRequest->clientSecret = $this->clientSecret;	
				$jsonRequest->accessType = "offline";				
				if (!is_null($this->refreshKey)){
					$jsonRequest->refreshToken = $this->refreshKey;
				}			
				
				$authResponse = restPost($url, json_encode($jsonRequest));
				$authObject = json_decode($authResponse->body);
				
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
				$newWSDL = file_gET_contents($wsdlLoc);
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
		
	function AddSubscriberToList($emailAddress, $listIDs, $subscriberKey = null){		
		$newSub = new ET_Subscriber;
		$newSub->authStub = $this;
		$lists = array();
		
		foreach ($listIDs as $key => $value){
			$list[] = array("ID" => $value);
		}
		
		$newSub->props = array("EmailAddress" => $emailAddress, "Lists" => $lists);
		if ($subscriberKey != null ){
			$newSub->props['SubscriberKey']  = $subscriberKey;
		}
		
		// Try to add the subscriber
		$postResponse = $newSub->post();
		
		if ($postResponse->status == false) { 
			// If the subscriber already exists in the account then we need to do an update.
			// Update Subscriber On List 
			if ($postResponse->results[0]->ErrorCode == "12014") {
				$patchResponse = $newSub->patch();
				return $patchResponse;
			}
		} 
		return $postResponse;
	}
	
	function CreateDataExtensions($dataExtensionDefinitions){		
		$newDEs = new ET_DataExtension();
		$newDEs->authStub = $this;
		
		$newDEs->props = $dataExtensionDefinitions;
		$postResponse = $newDEs->post();		
		
		return $postResponse;	
	}
}

class ET_Constructor {
	public $status, $code, $message, $results, $request_id, $moreResults;	
	function __construct($requestresponse, $httpcode, $restcall = false) {
		
		$this->code = $httpcode;
		
		if (!$restcall) {
			if(is_soap_fault($requestresponse)) {
				$this->status = false;
				$this->message = "SOAP Fault: (faultcode: {$requestresponse->faultcode}, faultstring: {$requestresponse->faultstring})";
				$this->message = "{$requestresponse->faultcode} {$requestresponse->faultstring})";
			} else {
				$this->status = true;
			}
		} else {
			if ($this->code != 200) {
				$this->status = false;
			} else {
				$this->status = true;
			}

			if (json_decode($requestresponse) != null){
				$this->results = json_decode($requestresponse);
			} else  {
				$this->message = $requestresponse;
			}						
		}
	}
}

class ET_Get extends ET_Constructor {
	function __construct($authStub, $objType, $props, $filter) {
		$authStub->refreshToken();
		$rrm = array();
		$request = array();
		$retrieveRequest = array();
		
		// If Props is not sent then Info will be used to find all retrievable properties
		if (is_null($props)){	
			$props = array();
			$info = new ET_Info($authStub, $objType);
			if (is_array($info->results)){	
				foreach ($info->results as $property){	
					if($property->IsRetrievable){	
						$props[] = $property->Name;
					}
				}	
			}
		}
		
		if (isAssoc($props)){
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
			if (array_key_exists("LogicalOperator",$filter )){				
				$cfp = new stdClass();
				$cfp->LeftOperand = new SoapVar($filter["LeftOperand"], SOAP_ENC_OBJECT, 'SimpleFilterPart', "http://exacttarget.com/wsdl/partnerAPI");
				$cfp->RightOperand = new SoapVar($filter["RightOperand"], SOAP_ENC_OBJECT, 'SimpleFilterPart', "http://exacttarget.com/wsdl/partnerAPI");				
				$cfp->LogicalOperator = $filter["LogicalOperator"];
				$retrieveRequest["Filter"] = new SoapVar($cfp, SOAP_ENC_OBJECT, 'ComplexFilterPart', "http://exacttarget.com/wsdl/partnerAPI");
				
			} else {
				$retrieveRequest["Filter"] = new SoapVar($filter, SOAP_ENC_OBJECT, 'SimpleFilterPart', "http://exacttarget.com/wsdl/partnerAPI");
			}
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

class ET_Continue extends ET_Constructor {	
	function __construct($authStub, $request_id) {
		$authStub->refreshToken();
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

class ET_Info extends ET_Constructor {
	function __construct($authStub, $objType) {
		$authStub->refreshToken();
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

class ET_Post extends ET_Constructor {	
	function __construct($authStub, $objType, $props) {
		$authStub->refreshToken();
		$cr = array(); 
		$objects = array(); 
		

		
		if (isAssoc($props)){
			$objects["Objects"] = new SoapVar($props, SOAP_ENC_OBJECT, $objType, "http://exacttarget.com/wsdl/partnerAPI");
		} else {
			$objects["Objects"] = array();
			foreach($props as $object){				
				$objects["Objects"][] = new SoapVar($object, SOAP_ENC_OBJECT, $objType, "http://exacttarget.com/wsdl/partnerAPI");
			}
		}		
		
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

class ET_Patch extends ET_Constructor {	
	function __construct($authStub, $objType, $props) {	
		$authStub->refreshToken();	
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

class ET_Delete extends ET_Constructor {	
	function __construct($authStub, $objType, $props) {	
		$authStub->refreshToken();
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

class ET_GetSupportRest extends ET_BaseObjectRest{
	protected $lastPageNumber;
	public function get() {
		$this->authStub->refreshToken();
		$completeURL = $this->endpoint;
		$additionalQS = array();
		
		if (!is_null($this->props)) {
			foreach ($this->props as $key => $value){
				if (in_array($key,$this->urlProps)){
					$completeURL = str_replace("{{$key}}",$value,$completeURL);					
				} else {
					$additionalQS[$key] = $value;
				}
			}				
		}
		
		foreach($this->urlPropsRequired as $value){
			if (is_null($this->props) || in_array($value,$this->props)){
				throw new Exception("Unable to process request due to missing required prop: {$value}");							
			}
		}
		
		// Clean up not required URL parameters
		foreach ($this->urlProps as $value){
			$completeURL = str_replace("{{$value}}","",$completeURL);								
		}		
		$additionalQS["access_token"] = $this->authStub->authToken;
		$queryString = http_build_query($additionalQS);		
		$completeURL = "{$completeURL}?{$queryString}";
		$response = new ET_GetRest($this->authStub, $completeURL, $queryString);						
		
		if (property_exists($response->results, 'page')){
			$this->lastPageNumber = $response->results->page;
			$pageSize = $response->results->pageSize;
			
			$count = null;
			if (property_exists($response->results, 'count')){
				$count = $response->results->count;
			} else if (property_exists($response->results, 'totalCount')){
				$count = $response->results->totalCount;
			}

			if ($count && ($count > ($this->lastPageNumber * $pageSize))){
				$response->moreResults = true;
			}
		}

		return $response;
	}
	
	public function getMoreResults() {		
	
		$originalPageValue = 1;
		$removePageFromProps = false;		
		
		if ($this->props && array_key_exists($this->props, '$page')) { 
			$originalPageValue = $this->props['page'];
		} else {
			$removePageFromProps = true		;	
		}
		
		if (!$this->props) { 
			$this->props = array();
		}
		
		$this->props['$page'] = $this->lastPageNumber + 1;
	
		$response = $this->get();
		
		if ($removePageFromProps) {
			unset($this->props['$page']);
		} else {
			$this->props['$page'] = $originalPageValue;
		}			
		
		return $response;
	}
}

class ET_CUDSupportRest extends ET_GetSupportRest{	
	public function post() {
		$this->authStub->refreshToken();
		$completeURL = $this->endpoint;
		$additionalQS = array();
		
		if (!is_null($this->props)) {
			foreach ($this->props as $key => $value){
				if (in_array($key,$this->urlProps)){
					$completeURL = str_replace("{{$key}}",$value,$completeURL);					
				} 
			}				
		}
		
		foreach($this->urlPropsRequired as $value){
			if (is_null($this->props) || in_array($value,$this->props)){
				throw new Exception("Unable to process request due to missing required prop: {$value}");							
			}
		}
		
		// Clean up not required URL parameters
		foreach ($this->urlProps as $value){
			$completeURL = str_replace("{{$value}}","",$completeURL);								
		}
		
		$additionalQS["access_token"] = $this->authStub->authToken;
		$queryString = http_build_query($additionalQS);		
		$completeURL = "{$completeURL}?{$queryString}";
		$response = new ET_PostRest($this->authStub, $completeURL, $this->props);				
		
		return $response;
	}
	
	public function patch() {
		$this->authStub->refreshToken();
		$completeURL = $this->endpoint;
		$additionalQS = array();
		
		// All URL Props are required when doing Delete	
		foreach($this->urlProps as $value){
			if (is_null($this->props) || in_array($value,$this->props)){
				throw new Exception("Unable to process request due to missing required prop: {$value}");							
			}
		}
		
		if (!is_null($this->props)) {
			foreach ($this->props as $key => $value){
				if (in_array($key,$this->urlProps)){
					$completeURL = str_replace("{{$key}}",$value,$completeURL);					
				} 
			}				
		}
		$additionalQS["access_token"] = $this->authStub->authToken;
		$queryString = http_build_query($additionalQS);		
		$completeURL = "{$completeURL}?{$queryString}";
		$response = new ET_PatchRest($this->authStub, $completeURL, $this->props);				
		
		return $response;
	}
	
	public function delete() {
		$this->authStub->refreshToken();
		$completeURL = $this->endpoint;
		$additionalQS = array();
		
		// All URL Props are required when doing Delete	
		foreach($this->urlProps as $value){
			if (is_null($this->props) || in_array($value,$this->props)){
				throw new Exception("Unable to process request due to missing required prop: {$value}");							
			}
		}
		
		if (!is_null($this->props)) {
			foreach ($this->props as $key => $value){
				if (in_array($key,$this->urlProps)){
					$completeURL = str_replace("{{$key}}",$value,$completeURL);					
				} 
			}				
		}
		$additionalQS["access_token"] = $this->authStub->authToken;
		$queryString = http_build_query($additionalQS);		
		$completeURL = "{$completeURL}?{$queryString}";
		$response = new ET_DeleteRest($this->authStub, $completeURL);				
		
		return $response;
	}
}

class ET_GetRest extends ET_Constructor {
	function __construct($authStub, $url, $qs = null) {
		$restResponse = restGet($url);
		$this->moreResults = false;
		parent::__construct($restResponse->body, $restResponse->httpcode, true);							
	}
}

class ET_PostRest extends ET_Constructor {
	function __construct($authStub, $url, $props) {
		$restResponse = restPost($url, json_encode($props));			
		parent::__construct($restResponse->body, $restResponse->httpcode, true);							
	}
}

class ET_DeleteRest extends ET_Constructor {
	function __construct($authStub, $url) {	
		$restResponse = restDelete($url);			
		parent::__construct($restResponse->body, $restResponse->httpcode, true);							
	}
}

class ET_PatchRest extends ET_Constructor {
	function __construct($authStub, $url, $props) {
		$restResponse = restPatch($url, json_encode($props));			
		parent::__construct($restResponse->body, $restResponse->httpcode, true);							
	}
}

class ET_Campaign extends ET_CUDSupportRest {
	function __construct() {	
		$this->endpoint = "https://www.exacttargetapis.com/hub/v1/campaigns/{id}";		
		$this->urlProps = array("id");
		$this->urlPropsRequired = array();
	}
}

class ET_Campaign_Asset extends ET_CUDSupportRest {
	function __construct() {	
		$this->endpoint = "https://www.exacttargetapis.com/hub/v1/campaigns/{id}/assets/{assetId}";		
		$this->urlProps = array("id", "assetId");
		$this->urlPropsRequired = array("id");
	}
}



class ET_BaseObject {
	public  $authStub, $props, $filter;
	protected $obj, $lastRequestID;
}

class ET_BaseObjectRest {
	public  $authStub, $props;
	protected  $endpoint, $urlProps, $urlPropsRequired;
}

class ET_GetSupport extends ET_BaseObject{
	
	public function get() {
		$response = new ET_Get($this->authStub, $this->obj, $this->props, $this->filter);
		$this->lastRequestID = $response->request_id;		
		return $response;
	}
	
	public function getMoreResults() {
		$response = new ET_Continue($this->authStub, $this->lastRequestID);
		$this->lastRequestID = $response->request_id;
		return $response;
	}
	
	public function info() {
		$response = new ET_Info($this->authStub, $this->obj);
		return $response;
	}	
}

class ET_CUDSupport extends ET_GetSupport{

	public function post() {
		$response = new ET_Post($this->authStub, $this->obj, $this->props);
		return $response;
	}

	public function patch() {
		$response = new ET_Patch($this->authStub, $this->obj, $this->props);
		return $response;
	}
	
	public function delete() {	
		$response = new ET_Delete($this->authStub, $this->obj, $this->props);
		return $response;
	}	
}



class ET_Subscriber extends ET_CUDSupport {		
	function __construct() {	
		$this->obj = "Subscriber";
	}	
}

class ET_DataExtension extends ET_CUDSupport {
	public  $columns;
	function __construct() {	
		$this->obj = "DataExtension";
	}
	
	public function post() {
		
		$originalProps = $this->props;
		if (isAssoc($this->props)){			
			$this->props["Fields"] = array("Field"=>array());		
			if (!is_null($this->columns) && is_array($this->columns)){
				foreach ($this->columns as $column){
					array_push($this->props['Fields']['Field'], $column);
				}	
			}							
		} else {
			$newProps = array();
			foreach ($this->props as $DE) {
				$newDE = $DE;
				$newDE["Fields"] = array("Field"=>array());
				if (!is_null($DE['columns']) && is_array($DE['columns'])){
					foreach ($DE['columns'] as $column){
						array_push($newDE['Fields']['Field'], $column);
					}						
				}
				array_push($newProps, $newDE);
			}
			$this->props = $newProps;					
		}
		
		$response = parent::post();
		
		$this->props = $originalProps;
		return $response;
	}
	
	public function patch() {				
		$this->props["Fields"] = array("Field"=>array());				
		foreach ($this->columns as $column){
			array_push($this->props['Fields']['Field'], $column);
		}	
		$response = parent::patch();		
		unset($this->props["Fields"]);		
		return $response;
	}
}

class ET_DataExtension_Column extends ET_GetSupport {
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

class ET_DataExtension_Row extends ET_CUDSupport {
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

class ET_ContentArea extends ET_CUDSupport {		
	function __construct() {	
		$this->obj = "ContentArea";
	}	
}

class ET_Folder extends ET_CUDSupport {		
	function __construct() {	
		$this->obj = "DataFolder";
	}	
}

class ET_Email extends ET_CUDSupport {		
	function __construct() {	
		$this->obj = "Email";
	}	
}

class ET_List extends ET_CUDSupport {		
	function __construct() {	
		$this->obj = "List";
	}	
}

class ET_List_Subscriber extends ET_GetSupport {		
	function __construct() {	
		$this->obj = "ListSubscriber";
	}	
}

class ET_SentEvent extends ET_GetSupport {		
	function __construct() {	
		$this->obj = "SentEvent";
	}	
}

class ET_OpenEvent extends ET_GetSupport {		
	function __construct() {	
		$this->obj = "OpenEvent";
	}	
}

class ET_BounceEvent extends ET_GetSupport {		
	function __construct() {	
		$this->obj = "BounceEvent";
	}	
}

class ET_UnsubEvent extends ET_GetSupport {		
	function __construct() {	
		$this->obj = "UnsubEvent";
	}	
}

class ET_ClickEvent extends ET_GetSupport {		
	function __construct() {	
		$this->obj = "ClickEvent";
	}	
}

class ET_TriggeredSend extends ET_CUDSupport {
	public  $subscribers;
	function __construct() {	
		$this->obj = "TriggeredSendDefinition";
	}

	public function Send() {
		$tscall = array("TriggeredSendDefinition" => $this->props , "Subscribers" => $this->subscribers);
		$response = new ET_Post($this->authStub, "TriggeredSend", $tscall);
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
	$responseObject = new stdClass(); 
	$responseObject->body = $outputJSON;
	$responseObject->httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	return $responseObject;
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
	$responseObject = new stdClass(); 
	$responseObject->body = $outputJSON;
	$responseObject->httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	return $responseObject;
}


// Function for calling a Fuel API using PATCH
/**
 * @param string      $url    The resource URL for the REST API
 * @param string      $content    A string of JSON which will be passed to the REST API
	*
 * @return string     The response payload from the REST service
 */
function restPatch($url, $content) {
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
	
	//Need to set the request to be a PATCH
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH" ); 
		
	// Disable VerifyPeer for SSL
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	$outputJSON = curl_exec($ch);
	$responseObject = new stdClass(); 
	$responseObject->body = $outputJSON;
	$responseObject->httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	return $responseObject;
}


function restDelete($url) {
	$ch = curl_init();
	
	// Uses the URL passed in that is specific to the API used
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPGET, true);
	
	// Need to set ReturnTransfer to True in order to store the result in a variable
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	// Disable VerifyPeer for SSL
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	// Set CustomRequest up for Delete	
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	
	$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);	
	
	$outputJSON = curl_exec($ch);

	$responseObject = new stdClass(); 
	$responseObject->body = $outputJSON;
	$responseObject->httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	return $responseObject;
}

function isAssoc($array)
{
    return ($array !== array_values($array));
}

?>
