<?php
//require __DIR__ . '/../vendor/autoload.php';
namespace FuelSdk;

use \RobRichards\WsePhp\WSSESoap;
use \Firebase\JWT;

use \Datetime;
use \SoapClient;
use \stdClass;
use \DateInterval;
use \DOMDocument;
use \DOMXPath;
use \Exception;

/**
 * Auto load method to load dependent classes
 */
/**
* Defines a Client interface class which manages the authentication process.
* This is the main client class which performs authentication, obtains auth token, if expired refresh auth token.
* Settings/Configuration can be passed to this class during construction or set it in config.php file.
* Configuration passed as parameter overrides the values from the configuration file.
* 
*/
class ET_Client extends SoapClient 
{

/**
 * @var string $packageName Folder/Package Name
 */
	public $packageName;
	/**
	 * @var array $packageFolders Array of Folder object properties.
	 */
	public $packageFolders;
	/**
	 * @var ET_Folder Parent folder object.
	 */
	public $parentFolders;

	/**
	 * @var string Proxy host.
	 */
	public $proxyHost;
	/**
	 * @var string Proxy port.
	 */
	public $proxyPort;
	/**
	 * @var string Proxy username.
	 */
	public $proxyUserName;
	/**
	 * @var string Proxy password.
	 */
	public $proxyPassword;
	/**
	 * @var boolean Require verification of peer name.
	 */
	public $sslVerifyPeer;

	/**
	 * @var string APIs hostname
	 */
	public $baseUrl;

	private $wsdlLoc, $debugSOAP, $lastHTTPCode, $clientId, 
			$clientSecret, $appsignature, $endpoint, 
			$tenantTokens, $tenantKey, $xmlLoc, $baseAuthUrl, $baseSoapUrl, $useOAuth2Authentication, $accountId, $scope;

	private $defaultBaseSoapUrl = 'https://webservice.exacttarget.com/Service.asmx';

	/**
	 * Initializes a new instance of the ET_Client class.
	 *
	 * @param boolean $getWSDL Flag to indicate whether to load WSDL from source. 
	 * 	If true, WSDL is load from the source and saved in to path set in xmlLoc variable.
	 * If false, WSDL stored in the path set in xmlLoc is loaded.
	 * @param boolean $debug Flag to indicate whether debug information needs to be logged. 
	 * Logging is enabled when the value is set to true and disabled when set to false.
	 * @param array $params Array of settings as string.</br>
	 * <b>Following are the possible settings.</b></br>
	 * <i><b>defaultwsdl</b></i> - WSDL location/path</br>
	 * <i><b>clientid</b></i> - Client Identifier optained from App Center</br>
	 * <i><b>clientsecred</b></i> - Client secret associated with clientid</br>
	 * <i><b>appsignature</b></i> - Application signature optained from App Center</br>
	 * <i><b>baseUrl</b></i> - ExactTarget SOAP API Url</br>
	 * <i><b>baseAuthUrl</b></i> - ExactTarget authentication rest api resource url</br>
	 * <b>If your application behind a proxy server, use the following setting</b></br>
	 * <i><b>proxyhost</b></i> - proxy server host name or ip address</br>
	 * <i><b>proxyport</b></i> - proxy server prot number</br>
	 * <i><b>proxyusername</b></i> - proxy server user name</br>
	 * <i><b>proxypassword</b></i> - proxy server password</br>
	 * <i><b>sslverifypeer</b></i> - Require verification of peer name</br>
	 */
	function __construct($getWSDL = false, $debug = false, $params = null) 
	{
		$tenantTokens = array();
		$config = false;

		$this->xmlLoc = 'ExactTargetWSDL.xml';

		if (file_exists(realpath("config.php")))
			$config = include 'config.php';

		if ($config)
		{
			$this->wsdlLoc = $config['defaultwsdl'];
			$this->clientId = $config['clientid'];
			$this->clientSecret = $config['clientsecret'];
			$this->appsignature = $config['appsignature'];
			$this->baseAuthUrl = $config["baseAuthUrl"];
			if (array_key_exists('baseSoapUrl', $config)) {
				if (!empty($config["baseSoapUrl"])) {
					$this->baseSoapUrl = $config["baseSoapUrl"];
				} else {
					$this->baseSoapUrl = $this->defaultBaseSoapUrl;
				}
			}
			if(array_key_exists('useOAuth2Authentication', $config)){$this->useOAuth2Authentication = $config['useOAuth2Authentication'];}
			if(array_key_exists("baseUrl", $config))
			{
				$this->baseUrl = $config["baseUrl"];
			}
			else
			{
				if($this->isNullOrEmptyString($this->useOAuth2Authentication) || $this->useOAuth2Authentication === false) {
					throw new Exception("baseUrl is null: Must be provided in config file when instantiating ET_Client");
				}
			}
            if (array_key_exists('accountId', $config)){$this->accountId = $config['accountId'];}
            if (array_key_exists('scope', $config)){$this->scope = $config['scope'];}

			if (array_key_exists('xmlloc', $config)){$this->xmlLoc = $config['xmlloc'];}

			if(array_key_exists('proxyhost', $config)){$this->proxyHost = $config['proxyhost'];}
			if (array_key_exists('proxyport', $config)){$this->proxyPort = $config['proxyport'];}
			if (array_key_exists('proxyusername', $config)){$this->proxyUserName = $config['proxyusername'];}
			if (array_key_exists('proxypassword', $config)){$this->proxyPassword = $config['proxypassword'];}
			if (array_key_exists('sslverifypeer', $config)){$this->sslVerifyPeer = $config['sslverifypeer'];}
		} 
		if ($params) 
		{
			if (array_key_exists('defaultwsdl', $params)){$this->wsdlLoc = $params['defaultwsdl'];}
			else {$this->wsdlLoc = "https://webservice.exacttarget.com/etframework.wsdl";}
			if (array_key_exists('clientid', $params)){$this->clientId = $params['clientid'];}
			if (array_key_exists('clientsecret', $params)){$this->clientSecret = $params['clientsecret'];}
			if (array_key_exists('appsignature', $params)){$this->appsignature = $params['appsignature'];}
			if (array_key_exists('xmlloc', $params)){$this->xmlLoc = $params['xmlloc'];}

			if (array_key_exists('proxyhost', $params)){$this->proxyHost = $params['proxyhost'];}
			if (array_key_exists('proxyport', $params)){$this->proxyPort = $params['proxyport'];}
			if (array_key_exists('proxyusername', $params)) {$this->proxyUserName = $params['proxyusername'];}
			if (array_key_exists('proxypassword', $params)) {$this->proxyPassword = $params['proxypassword'];}
			if (array_key_exists('sslverifypeer', $params)) {$this->sslVerifyPeer = $params['sslverifypeer'];}
			if (array_key_exists('baseUrl', $params))
			{
				$this->baseUrl = $params['baseUrl'];
			}
			else 
			{
				$this->baseUrl = "https://www.exacttargetapis.com";
			}
			if (array_key_exists('baseAuthUrl', $params))
			{
				$this->baseAuthUrl = $params['baseAuthUrl'];
			}
			else 
			{
				$this->baseAuthUrl = "https://auth.exacttargetapis.com";
			}
			if (array_key_exists('baseSoapUrl', $params))
			{
				if (!empty($params["baseSoapUrl"])) {
					$this->baseSoapUrl = $params['baseSoapUrl'];
				} else {
                    $this->baseSoapUrl = $this->defaultBaseSoapUrl;
				}
			}
			if (array_key_exists('useOAuth2Authentication', $params))
			{
                $this->useOAuth2Authentication = $params['useOAuth2Authentication'];
			}
            if (array_key_exists('accountId', $params))
            {
                $this->accountId = $params['accountId'];
            }
            if (array_key_exists('scope', $params))
            {
                $this->scope = $params['scope'];
            }
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

		if ($this->baseSoapUrl) {
            $this->endpoint = $this->baseSoapUrl;
		} else {
            $cache = new ET_CacheService($this->clientId, $this->clientSecret);
            $cacheData = $cache->get();
            if (!is_null($cacheData) && $cacheData->url) {
                $this->endpoint = $cacheData->url;
            } else {
                try {
                    $url = $this->baseUrl."/platform/v1/endpoints/soap";
                    $endpointResponse = ET_Util::restGet($url, $this, $this->getAuthToken($this->tenantKey));
                    $endpointObject = json_decode($endpointResponse->body);
                    if ($endpointObject && property_exists($endpointObject,"url")){
                        $this->endpoint = $endpointObject->url;
                    } else {
                        $this->endpoint = $this->defaultBaseSoapUrl;
                    }
                } catch (Exception $e) {
                    $this->endpoint = $this->defaultBaseSoapUrl;
                }
                $cache->write($this->endpoint);
            }
		}

		$context = stream_context_create([
			'ssl' => [
				'verify_peer' => ET_Util::shouldVerifySslPeer($this->sslVerifyPeer)
			]
		]);

        $soapOptions = array(
			'stream_context' => $context
        );
        if (!empty($this->proxyHost)) {
            $soapOptions['proxy_host'] = $this->proxyHost;
        }
        if (!empty($this->proxyPort)) {
            $soapOptions['proxy_port'] = $this->proxyPort;
        }
        if (!empty($this->proxyUserName)) {
            $soapOptions['proxy_username'] = $this->proxyUserName;
        }
        if (!empty($this->proxyPassword)) {
            $soapOptions['proxy_password'] = $this->proxyPassword;
        }
		parent::__construct($this->xmlLoc, $soapOptions);

		parent::__setLocation($this->endpoint);
	}
	/**
	 * Gets the refresh token using the authentication URL.
	 *
	 * @param boolean $forceRefresh Flag to indicate a force refresh of authentication toekn.
	 * @return void
	 */	
	function refreshToken($forceRefresh = false) 
	{
		if ($this->useOAuth2Authentication === true){
            $this->refreshTokenWithOAuth2($forceRefresh);
            return;
		}
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
						? $this->baseAuthUrl."/v1/requestToken?legacy=1"
						: $this->baseUrl."/provisioning/v1/tenants/{$this->tenantKey}/requestToken?legacy=1";
						
				$jsonRequest = new stdClass(); 
				$jsonRequest->clientId = $this->clientId;
				$jsonRequest->clientSecret = $this->clientSecret;
				$jsonRequest->accessType = "offline";
				if (!is_null($this->getRefreshToken($this->tenantKey))){
					$jsonRequest->refreshToken = $this->getRefreshToken($this->tenantKey);
				}
				$authResponse = ET_Util::restPost($url, json_encode($jsonRequest), $this);
				$authObject = json_decode($authResponse->body);
				//echo "auth:  \n";
				//print_r($authResponse);
				
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

    function refreshTokenWithOAuth2($forceRefresh = false)
    {
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

                $url = $this->baseAuthUrl."/v2/token";

                $jsonRequest = new stdClass();
                $jsonRequest->client_id = $this->clientId;
                $jsonRequest->client_secret = $this->clientSecret;
                $jsonRequest->grant_type = "client_credentials";

                if ($this->isNullOrEmptyString($this->accountId) == false){
                    $jsonRequest->account_id = $this->accountId;
				}
				if ($this->isNullOrEmptyString($this->scope) == false){
                    $jsonRequest->scope = $this->scope;
				}

                $authResponse = ET_Util::restPost($url, json_encode($jsonRequest), $this);
                $authObject = json_decode($authResponse->body);

                if ($authResponse && property_exists($authObject,"access_token")){
                    $dv = new DateInterval('PT'.$authObject->expires_in.'S');
                    $newexpTime = new DateTime();
                    $this->setAuthToken($this->tenantKey, $authObject->access_token, $newexpTime->add($dv));
                    $this->setInternalAuthToken($this->tenantKey, $authObject->access_token);
                    $this->baseUrl = $authObject->rest_instance_url;
                    $this->baseSoapUrl= $authObject->soap_instance_url."Service.asmx";
                } else {
                    throw new Exception('Unable to validate App Keys(ClientID/ClientSecret) provided, requestToken response:'.$authResponse->body );
                }
            }
        } catch (Exception $e) {
            throw new Exception('Unable to validate App Keys(ClientID/ClientSecret) provided.: '.$e->getMessage());
        }
    }
	/**
	 * Returns the  HTTP code return by the last SOAP/Rest call
	 *
	 * @return lastHTTPCode
	 */
	function __getLastResponseHTTPCode()
	{

		return $this->lastHTTPCode;		
	}

    /**
	* Create the WSDL file at specified location.
	* @param  string 		location or path of the WSDL file to be created.
	* @return void
    */	
	function CreateWSDL($wsdlLoc) 
	{
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
				$newWSDL = file_get_contents($wsdlLoc);
				file_put_contents($this->xmlLoc, $newWSDL);
			}	
		}
		catch (Exception $e) {
			throw new Exception('Unable to store local copy of WSDL file:'.$e->getMessage()."\n");
		}
	}
	
	/**
	 * Returns last modified date of the URL
	 *
	 * @param [type] $remotepath
	 * @return string Last modified date
	 */
	function GetLastModifiedDate($remotepath) 
	{
		$curl = curl_init($remotepath);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, ET_Util::shouldVerifySslPeer($this->sslVerifyPeer));
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FILETIME, true);

		if (!empty($this->proxyHost)) {
			curl_setopt($curl, CURLOPT_PROXY, $this->proxyHost);
		}
		if (!empty($this->proxyPort)) {
			curl_setopt($curl, CURLOPT_PROXYPORT, $this->proxyPort);
		}
		if (!empty($this->proxyUserName) && !empty($this->proxyPassword)) {
			curl_setopt($curl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_PROXYUSERPWD, $this->proxyUserName.':'.$this->proxyPassword);
		}

		$result = curl_exec($curl);
		
		if ($result === false) {
			throw new Exception(curl_error($curl)); 
		}
		
		return curl_getinfo($curl, CURLINFO_FILETIME);
	}
	/**
	 * Perfoms an soap request.
	 *
	 * @param string $request Soap request xml 
	 * @param string $location Url as string
	 * @param string $saction Soap action name
	 * @param string $version Future use
	 * @param integer $one_way Future use
	 * @return string Soap web service request result
	 */			
	function __doRequest($request, $location, $saction, $version, $one_way = 0) 
	{
		$doc = new DOMDocument();
		$doc->loadXML($request);

        if($this->useOAuth2Authentication === true){
            $this->addOAuth($doc, $this->getAuthToken($this->tenantKey));
			$content = $doc->saveXML();
		}
		else{
            $objWSSE = new WSSESoap($doc);
            $objWSSE->addUserToken("*", "*", FALSE);
			$this->addOAuth($doc, $this->getInternalAuthToken($this->tenantKey));

			$content = $objWSSE->saveXML();
		}

		if ($this->debugSOAP){
			error_log ('FuelSDK SOAP Request: ');
			error_log (str_replace($this->getInternalAuthToken($this->tenantKey),"REMOVED",$content));
		}
		
		$headers = array("Content-Type: text/xml","SOAPAction: ".$saction, "User-Agent: ".ET_Util::getSDKVersion());

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $location);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $content);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, ET_Util::shouldVerifySslPeer($this->sslVerifyPeer));
		curl_setopt($ch, CURLOPT_USERAGENT, ET_Util::getSDKVersion());

		if (!empty($this->proxyHost)) {
			curl_setopt($ch, CURLOPT_PROXY, $this->proxyHost);
		}
		if (!empty($this->proxyPort)) {
			curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxyPort);
		}
		if (!empty($this->proxyUserName) && !empty($this->proxyPassword)) {
			curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyUserName.':'.$this->proxyPassword);
		}

		$output = curl_exec($ch);
		$this->lastHTTPCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch); 

		return $output;
	}
	/**
	 * Add OAuth token to the header of the soap request
	 *
	 * @param string $doc Soap request as xml string
	 * @param string $token OAuth token
	 * @return void
	 */
	public function addOAuth( $doc, $token) 
	{		
		$soapDoc = $doc;
		$envelope = $doc->documentElement;
		$soapNS = $envelope->namespaceURI;
		$soapPFX = $envelope->prefix;
		$SOAPXPath = new DOMXPath($doc);
		$SOAPXPath->registerNamespace('wssoap', $soapNS);

		$headers = $SOAPXPath->query('//wssoap:Envelope/wssoap:Header');
		$header = $headers->item(0);
		if (! $header) {
			$header = $soapDoc->createElementNS($soapNS, $soapPFX.':Header');
			$envelope->insertBefore($header, $envelope->firstChild);
		}

		if ($this->useOAuth2Authentication === true){
            $authnode = $soapDoc->createElementNS('http://exacttarget.com','fueloauth',$token);
		}
		else{
            $SOAPXPath->registerNamespace('wswsse', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');

            $authnode = $soapDoc->createElementNS('http://exacttarget.com', 'oAuth');
            $oauthtoken = $soapDoc->createElementNS(null,'oAuthToken',$token);
            $authnode->appendChild($oauthtoken);
		}
		$header->appendChild($authnode);
	}

	/** 
	* Get the authentication token.
	* @return string 
	*/	
	public function getAuthToken($tenantKey = null) 
	{
		$tenantKey = $tenantKey == null ? $this->tenantKey : $tenantKey;
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}		
		return isset($this->tenantTokens[$tenantKey]['authToken']) 
			? $this->tenantTokens[$tenantKey]['authToken']
			: null;
	}
	
	/** 
	* Set the authentication token in the tenantTokens array.
	* @param  string $tenantKey Tenant key for which auth toke to be set
	* @param  string $authToken Authentication token to be set
	* @param  string $authTokenExpiration Authentication token expiration value
	*/
	function setAuthToken($tenantKey, $authToken, $authTokenExpiration) 
	{
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		$this->tenantTokens[$tenantKey]['authToken'] = $authToken;
		$this->tenantTokens[$tenantKey]['authTokenExpiration'] = $authTokenExpiration;
	}
	
	/** 
	* Get the Auth Token Expiration.
	* @param  string $tenantKey Tenant key for which authenication token is returned
	* @return string Authenticaiton token for the tenant key
	*/
	function getAuthTokenExpiration($tenantKey) 
	{
		$tenantKey = $tenantKey == null ? $this->tenantKey : $tenantKey;
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		return isset($this->tenantTokens[$tenantKey]['authTokenExpiration'])
			? $this->tenantTokens[$tenantKey]['authTokenExpiration']
			: null;
	}

	/** 
	* Get the internal authentication token.
	* @param  string $tenantKey
	* @return string Internal authenication token
	*/
	function getInternalAuthToken($tenantKey) 
	{
		$tenantKey = $tenantKey == null ? $this->tenantKey : $tenantKey;	
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		return isset($this->tenantTokens[$tenantKey]['internalAuthToken'])
			? $this->tenantTokens[$tenantKey]['internalAuthToken']
			: null;
	}

	/** 
	* Set the internal auth tokan.
	* @param  string $tenantKey
	* @param string $internalAuthToken
	*/
	function setInternalAuthToken($tenantKey, $internalAuthToken) {
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}	
		$this->tenantTokens[$tenantKey]['internalAuthToken'] = $internalAuthToken;
	}
	
	/** 
	* Set the refresh authentication token.
	* @param  string $tenantKey Tenant key to which refresh token is set
	* @param  string $refreshToken Refresh authenication token
	*/
	function setRefreshToken($tenantKey, $refreshToken) 
	{
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}	
		$this->tenantTokens[$tenantKey]['refreshToken'] = $refreshToken;
	}

	/**
	 * Get the refresh token for the tenant.
	 *
	 * @param string $tenantKey
	 * @return string Refresh token for the tenant
	 */
	function getRefreshToken($tenantKey) 
	{
		$tenantKey = $tenantKey == null ? $this->tenantKey : $tenantKey;	
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		return isset($this->tenantTokens[$tenantKey]['refreshToken']) 
			? $this->tenantTokens[$tenantKey]['refreshToken']
			: null;
	}	

	/**
	 * Add subscriber to list.
	 *
	 * @param string $emailAddress Email address of the subscriber
	 * @param array $listIDs Array of list id to which the subscriber is added
	 * @param string $subscriberKey Newly added subscriber key
	 * @return mixed post or patch response object. If the subscriber already existing patch response is returned otherwise post response returned.
	 */
	function AddSubscriberToList($emailAddress, $listIDs, $subscriberKey = null)
	{                   
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
	
	function AddSubscribersToLists($subs, $listIDs)
	{
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
	/**
	 * Create a new data extension based on the definition passed
	 *
	 * @param array $dataExtensionDefinitions Data extension definition properties as an array
	 * @return mixed post response object
	 */
	function CreateDataExtensions($dataExtensionDefinitions)
	{
		$newDEs = new ET_DataExtension();
		$newDEs->authStub = $this;
		$newDEs->props = $dataExtensionDefinitions;
		$postResponse = $newDEs->post();
		
		return $postResponse;
	}
	/**
	 * Starts an send operation for the TriggerredSend records
	 *
	 * @param array $arrayOfTriggeredRecords Array of TriggeredSend records
	 * @return mixed Send reponse object
	 */
	function SendTriggeredSends($arrayOfTriggeredRecords)
	{
		$sendTS = new ET_TriggeredSend();
		$sendTS->authStub = $this;
		$sendTS->props = $arrayOfTriggeredRecords;
		$sendResponse = $sendTS->send();
		return $sendResponse;
	}
	/**
	 * Create an email send definition, send the email based on the definition and delete the definition.
	 *
	 * @param string $emailID Email identifier for which the email is sent
	 * @param string $listID Send definition list identifier
	 * @param string $sendClassficationCustomerKey Send classification customer key 
	 * @return mixed Final delete action result
	 */
	function SendEmailToList($emailID, $listID, $sendClassficationCustomerKey) 
	{
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
	/**
	 * Create an email send definition, send the email based on the definition and delete the definition.
	 *
	 * @param string $emailID Email identifier for which the email is sent
	 * @param string $sendableDataExtensionCustomerKey Sendable data extension customer key 
	 * @param string $sendClassficationCustomerKey Send classification customer key 
	 * @return mixed Final delete action result
	 */
	function SendEmailToDataExtension($emailID, $sendableDataExtensionCustomerKey, $sendClassficationCustomerKey)
	{
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
	/**
	 * Create an import definition and start the import process
	 *
	 * @param string $listId List identifier. Used as the destination object identifier.
	 * @param string $fileName Name of the file to be imported
	 * @return mixed Returns the import process result
	 */
	function CreateAndStartListImport($listId,$fileName)
	{
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
	/**
	 * Create an import definition and start the import process
	 *
	 * @param string $dataExtensionCustomerKey Data extension customer key. Used as the destination object identifier.
	 * @param string $fileName Name of the file to be imported
	 * @param bool $overwrite Flag to indicate to overwrite the uploaded file
	 * @return mixed Returns the import process result
	 */
	function CreateAndStartDataExtensionImport($dataExtensionCustomerKey, $fileName, $overwrite) 
	{
		$import = new ET_Import();
		$import->authStub = $this;
		$import->props = array("Name"=> "SDK Generated Import ".uniqid());
		$import->props["CustomerKey"] = uniqid();
		$import->props["Description"] = "SDK Generated Import";
		$import->props["AllowErrors"] = "true";
		$import->props["DestinationObject"] = array("CustomerKey"=>$dataExtensionCustomerKey);
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
	/**
	 * Create a profile attribute
	 *
	 * @param array $allAttributes Profile attribute properties as an array.
	 * @return mixed Post operation result
	 */
	function CreateProfileAttributes($allAttributes) 
	{
		$attrs = new ET_ProfileAttribute();
		$attrs->authStub = $this;
		$attrs->props = $allAttributes;
		return $attrs->post();
	}
	/**
	 * Create one or more content areas
	 *
	 * @param array $arrayOfContentAreas Content areas properties as an array
	 * @return void
	 */
	function CreateContentAreas($arrayOfContentAreas) 
	{
		$postC = new ET_ContentArea();
		$postC->authStub = $this;
		$postC->props = $arrayOfContentAreas;
		$sendResponse = $postC->post();
		return $sendResponse;
	}

    /**
	* Function for basic field validation (present and neither empty nor only white space)
	 *
	 * @param string $str string to be validated
	 */
	function isNullOrEmptyString($str){
        return (!isset($str) || trim($str) === '');
    }
}

?>
