<?php

namespace ExactTarget;

use DOMDocument;
use DateTime;
use Exception;
use SoapClient;
use Wse\WSSESoap;
use stdClass;

class ET_Client extends SoapClient {
	public $packageName, $packageFolders, $parentFolders;
	private $wsdlLoc, $debugSOAP, $lastHTTPCode, $clientId,
			$clientSecret, $appsignature, $endpoint,
			$tenantTokens, $tenantKey, $xmlLoc;

	function __construct($getWSDL = false, $debug = false, $params = null) {
		$tenantTokens = array();
		$config = false;

		$this->xmlLoc = 'ExactTargetWSDL.xml';

		if (file_exists(realpath(__DIR__ . "/config.php")))
			$config = include 'config.php';

		if ($config){
			$this->wsdlLoc = $config['defaultwsdl'];
			$this->clientId = $config['clientid'];
			$this->clientSecret = $config['clientsecret'];
			$this->appsignature = $config['appsignature'];
			if (array_key_exists('xmlloc', $config)){$this->xmlLoc = $config['xmlloc'];}
		} else {
			if ($params && array_key_exists('defaultwsdl', $params)){$this->wsdlLoc = $params['defaultwsdl'];}
			else {$this->wsdlLoc = "https://webservice.exacttarget.com/etframework.wsdl";}
			if ($params && array_key_exists('clientid', $params)){$this->clientId = $params['clientid'];}
			if ($params && array_key_exists('clientsecret', $params)){$this->clientSecret = $params['clientsecret'];}
			if ($params && array_key_exists('appsignature', $params)){$this->appsignature = $params['appsignature'];}
			if ($params && array_key_exists('xmlloc', $params)){$this->xmlLoc = $params['xmlloc'];}
		}

		$this->debugSOAP = $debug;

		if (!property_exists($this,'clientId') || is_null($this->clientId) || !property_exists($this,'clientSecret') || is_null($this->clientSecret)){
			throw new Exception('clientid or clientsecret is null: Must be provided in config file or passed when instantiating ET_Client');
		}

		if ($getWSDL){$this->CreateWSDL($this->wsdlLoc);}

		if ($params && array_key_exists('jwt', $params)){
			if (!property_exists($this,'appsignature') || is_null($this->appsignature)){
				throw new Exception('Unable to utilize JWT for SSO without appsignature: Must be provided in config file or passed when instantiating ET_Client');
			}
			$decodedJWT = JWT::decode($params['jwt'], $this->appsignature);
			$dv = new DateInterval('PT'.$decodedJWT->request->user->expiresIn.'S');
			$newexpTime = new DateTime();
			$this->setAuthToken($this->tenantKey, $decodedJWT->request->user->oauthToken, $newexpTime->add($dv));
			$this->setInternalAuthToken($this->tenantKey, $decodedJWT->request->user->internalOauthToken);
			$this->setRefreshToken($this->tenantKey, $decodedJWT->request->user->refreshToken);
			$this->packageName = $decodedJWT->request->application->package;
		}
		$this->refreshToken();

		try {
			$url = "https://www.exacttargetapis.com/platform/v1/endpoints/soap?access_token=".$this->getAuthToken($this->tenantKey);
			$endpointResponse = ET_RestUtils::restGet($url);
			$endpointObject = json_decode($endpointResponse->body);
			if ($endpointResponse && property_exists($endpointObject,"url")){
				$this->endpoint = $endpointObject->url;
			} else {
				throw new Exception('Unable to determine stack using /platform/v1/endpoints/:'.$endpointResponse->body);
			}
			} catch (Exception $e) {
			throw new Exception('Unable to determine stack using /platform/v1/endpoints/: '.$e->getMessage());
		}
		parent::__construct($this->xmlLoc, array('trace'=>1, 'exceptions'=>0));
		parent::__setLocation($this->endpoint);
	}

	function refreshToken($forceRefresh = false) {
		if (property_exists($this, "sdl") && $this->sdl == 0){
			parent::__construct($this->xmlLoc, array('trace'=>1, 'exceptions'=>0));
		}
		try {
			$currentTime = new DateTime();
			if (is_null($this->getAuthTokenExpiration($this->tenantKey))){
				$timeDiff = 0;
			} else {
				$timeDiff = $currentTime->diff($this->getAuthTokenExpiration($this->tenantKey))->format('%i');
				$timeDiff = $timeDiff  + (60 * $currentTime->diff($this->getAuthTokenExpiration($this->tenantKey))->format('%H'));
			}

			if (is_null($this->getAuthToken($this->tenantKey)) || ($timeDiff < 5) || $forceRefresh  ){
				$url = $this->tenantKey == null
						? "https://auth.exacttargetapis.com/v1/requestToken?legacy=1"
						: "https://www.exacttargetapis.com/provisioning/v1/tenants/{$this->tenantKey}/requestToken?legacy=1";
				$jsonRequest = new stdClass();
				$jsonRequest->clientId = $this->clientId;
				$jsonRequest->clientSecret = $this->clientSecret;
				$jsonRequest->accessType = "offline";
				if (!is_null($this->getRefreshToken($this->tenantKey))){
					$jsonRequest->refreshToken = $this->getRefreshToken($this->tenantKey);
				}
				$authResponse = ET_RestUtils::restPost($url, json_encode($jsonRequest));
				$authObject = json_decode($authResponse->body);

				if ($authResponse && property_exists($authObject,"accessToken")){

					$dv = new DateInterval('PT'.$authObject->expiresIn.'S');
					$newexpTime = new DateTime();
					$this->setAuthToken($this->tenantKey, $authObject->accessToken, $newexpTime->add($dv));
					$this->setInternalAuthToken($this->tenantKey, $authObject->legacyToken);
					if (property_exists($authObject,'refreshToken')){
						$this->setRefreshToken($this->tenantKey, $authObject->refreshToken);
					}
				} else {
					throw new Exception('Unable to validate App Keys(ClientID/ClientSecret) provided, requestToken response:'.$authResponse->body );
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

			if (file_exists($this->xmlLoc)){
				$localTS = filemtime($this->xmlLoc);
				if ($remoteTS <= $localTS)
				{
					$getNewWSDL = false;
				}
			}

			if ($getNewWSDL){
				$newWSDL = file_gET_contents($wsdlLoc);
				file_put_contents($this->xmlLoc, $newWSDL);
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
			throw new Exception(curl_error($curl));
		}

		return curl_getinfo($curl, CURLINFO_FILETIME);
	}

	function __doRequest($request, $location, $saction, $version, $one_way = 0) {
		$doc = new DOMDocument();
		$doc->loadXML($request);

		$objWSSE = new WSSESoap($doc);
		$objWSSE->addUserToken("*", "*", FALSE);
		$objWSSE->addOAuth($this->getInternalAuthToken($this->tenantKey));

		$content = $objWSSE->saveXML();
		$content_length = strlen($content);
		if ($this->debugSOAP){
			error_log ('FuelSDK SOAP Request: ');
			error_log (str_replace($this->getInternalAuthToken($this->tenantKey),"REMOVED",$content));
		}

		$headers = array("Content-Type: text/xml","SOAPAction: ".$saction, "User-Agent: ".getSDKVersion());

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $location);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $content);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, "FuelSDK-PHP-v0.9");
		$output = curl_exec($ch);
		$this->lastHTTPCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return $output;
	}

	public function getAuthToken($tenantKey = null) {
		$tenantKey = $tenantKey == null ? $this->tenantKey : $tenantKey;
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		return isset($this->tenantTokens[$tenantKey]['authToken'])
			? $this->tenantTokens[$tenantKey]['authToken']
			: null;
	}

	function setAuthToken($tenantKey, $authToken, $authTokenExpiration) {
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		$this->tenantTokens[$tenantKey]['authToken'] = $authToken;
		$this->tenantTokens[$tenantKey]['authTokenExpiration'] = $authTokenExpiration;
	}

	function getAuthTokenExpiration($tenantKey) {
		$tenantKey = $tenantKey == null ? $this->tenantKey : $tenantKey;
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		return isset($this->tenantTokens[$tenantKey]['authTokenExpiration'])
			? $this->tenantTokens[$tenantKey]['authTokenExpiration']
			: null;
	}

	function getInternalAuthToken($tenantKey) {
		$tenantKey = $tenantKey == null ? $this->tenantKey : $tenantKey;
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		return isset($this->tenantTokens[$tenantKey]['internalAuthToken'])
			? $this->tenantTokens[$tenantKey]['internalAuthToken']
			: null;
	}

	function setInternalAuthToken($tenantKey, $internalAuthToken) {
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		$this->tenantTokens[$tenantKey]['internalAuthToken'] = $internalAuthToken;
	}

	function setRefreshToken($tenantKey, $refreshToken) {
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		$this->tenantTokens[$tenantKey]['refreshToken'] = $refreshToken;
	}

	function getRefreshToken($tenantKey) {
		$tenantKey = $tenantKey == null ? $this->tenantKey : $tenantKey;
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		return isset($this->tenantTokens[$tenantKey]['refreshToken'])
			? $this->tenantTokens[$tenantKey]['refreshToken']
			: null;
	}

	function AddSubscriberToList($emailAddress, $listIDs, $subscriberKey = null){
		$newSub = new ET_Subscriber;
		$newSub->authStub = $this;
		$lists = array();

		foreach ($listIDs as $key => $value){
			$lists[] = array("ID" => $value);
		}

		//if (is_string($emailAddress)) {
			$newSub->props = array("EmailAddress" => $emailAddress, "Lists" => $lists);
			if ($subscriberKey != null ){
				$newSub->props['SubscriberKey']  = $subscriberKey;
			}
		/*} else if (is_array($emailAddress)) {
			$newSub->props = array();
			for ($i = 0; $i < count($emailAddress); $i++) {
				$copyLists = array();
				foreach ($lists as $k => $v) {
					$NewProps = array();
					foreach($v as $prop => $value) {
						$NewProps[$prop] = $value;
					}
					$copyLists[$k] = $NewProps;
				}

				$p = array("EmailAddress" => $emailAddress[$i], "Lists" => $copyLists);
				if (is_array($subscriberKey) && $subscriberKey[$i] != null) {
					$p['SubscriberKey']  = $subscriberKey[$i];
				}
				$newSub->props[] = $p;
			}
		}*/

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

	function AddSubscribersToLists($subs, $listIDs){
		//Create Lists
		foreach ($listIDs as $key => $value){
			$lists[] = array("ID" => $value);
		}

		for ($i = 0; $i < count($subs); $i++) {
			$copyLists = array();
			foreach ($lists as $k => $v) {
				$NewProps = array();
				foreach($v as $prop => $value) {
					$NewProps[$prop] = $value;
				}
				$copyLists[$k] = $NewProps;
			}
			$subs[$i]["Lists"] = $copyLists;
		}

		$response = new ET_Post($this, "Subscriber", $subs, true);
		return $response;
    }


	function CreateDataExtensions($dataExtensionDefinitions){
		$newDEs = new ET_DataExtension();
		$newDEs->authStub = $this;
		$newDEs->props = $dataExtensionDefinitions;
		$postResponse = $newDEs->post();

		return $postResponse;
	}

	function SendTriggeredSends($arrayOfTriggeredRecords){
		$sendTS = new ET_TriggeredSend();
		$sendTS->authStub = $this;
		$sendTS->props = $arrayOfTriggeredRecords;
		$sendResponse = $sendTS->send();
		return $sendResponse;
	}

	function SendEmailToList($emailID, $listID, $sendClassficationCustomerKey) {
		$email = new ET_Email_SendDefinition();
		$email->props = array("Name"=> uniqid(), "CustomerKey"=>uniqid(), "Description"=>"Created with FuelSDK");
		$email->props["SendClassification"] = array("CustomerKey"=>$sendClassficationCustomerKey);
		$email->props["SendDefinitionList"] = array("List"=> array("ID"=>$listID), "DataSourceTypeID"=>"List");
		$email->props["Email"] = array("ID"=>$emailID);
		$email->authStub = $this;
		$result = $email->post();

		if ($result->status) {
			$sendresult = $email->send();
			if ($sendresult->status) {
				$deleteresult = $email->delete();
				return $sendresult;
			} else {
				throw new Exception("Unable to send using send definition due to: ".print_r($result,true));
			}
		} else {
			throw new Exception("Unable to create send definition due to: ".print_r($result,true));
		}
	}

	function SendEmailToDataExtension($emailID, $sendableDataExtensionCustomerKey, $sendClassficationCustomerKey){
		$email = new ET_Email_SendDefinition();
		$email->props = array("Name"=>uniqid(), "CustomerKey"=>uniqid(), "Description"=>"Created with FuelSDK");
		$email->props["SendClassification"] = array("CustomerKey"=> $sendClassficationCustomerKey);
		$email->props["SendDefinitionList"] = array("CustomerKey"=> $sendableDataExtensionCustomerKey, "DataSourceTypeID"=>"CustomObject");
		$email->props["Email"] = array("ID"=>$emailID);
		$email->authStub = $this;
		$result = $email->post();
		if ($result->status) {
			$sendresult = $email->send();
			if ($sendresult->status) {
				$deleteresult = $email->delete();
				return $sendresult;
			} else {
				throw new Exception("Unable to send using send definition due to:".print_r($result,true));
			}
		} else {
			throw new Exception("Unable to create send definition due to: ".print_r($result,true));
		}
	}

	function CreateAndStartListImport($listId,$fileName){
		$import = new ET_Import();
		$import->authStub = $this;
		$import->props = array("Name"=> "SDK Generated Import ".uniqid());
		$import->props["CustomerKey"] = uniqid();
		$import->props["Description"] = "SDK Generated Import";
		$import->props["AllowErrors"] = "true";
		$import->props["DestinationObject"] = array("ID"=>$listId);
		$import->props["FieldMappingType"] = "InferFromColumnHeadings";
		$import->props["FileSpec"] = $fileName;
		$import->props["FileType"] = "CSV";
		$import->props["RetrieveFileTransferLocation"] = array("CustomerKey"=>"ExactTarget Enhanced FTP");
		$import->props["UpdateType"] = "AddAndUpdate";
		$result = $import->post();

		if ($result->status) {
			return $import->start();
		} else {
			throw new Exception("Unable to create import definition due to: ".print_r($result,true));
		}
	}

	function CreateAndStartDataExtensionImport($dataExtensionCustomerKey, $fileName, $overwrite) {
		$import = new ET_Import();
		$import->authStub = $this;
		$import->props = array("Name"=> "SDK Generated Import ".uniqid());
		$import->props["CustomerKey"] = uniqid();
		$import->props["Description"] = "SDK Generated Import";
		$import->props["AllowErrors"] = "true";
		$import->props["DestinationObject"] = array("ObjectID"=>$dataExtensionCustomerKey);
		$import->props["FieldMappingType"] = "InferFromColumnHeadings";
		$import->props["FileSpec"] = $fileName;
		$import->props["FileType"] = "CSV";
		$import->props["RetrieveFileTransferLocation"] = array("CustomerKey"=>"ExactTarget Enhanced FTP");
		if ($overwrite) {
			$import->props["UpdateType"] = "Overwrite";
		} else {
			$import->props["UpdateType"] = "AddAndUpdate";
		}

		$result = $import->post();

		if ($result->status) {
			return $import->start();
		} else {
			throw new Exception("Unable to create import definition due to: ".print_r($result,true));
		}
	}


	function CreateProfileAttributes($allAttributes) {
		$attrs = new ET_ProfileAttribute();
		$attrs->authStub = $this;
		$attrs->props = $allAttributes;
		return $attrs->post();
	}


	function CreateContentAreas($arrayOfContentAreas) {
		$postC = new ET_ContentArea();
		$postC->authStub = $this;
		$postC->props = $arrayOfContentAreas;
		$sendResponse = $postC->post();
		return $sendResponse;
	}

}
