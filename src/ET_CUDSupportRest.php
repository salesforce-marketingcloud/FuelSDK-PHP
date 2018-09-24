<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;
use \Exception;

/**
 * This class represents the create, update, delete operation for REST service.
 */
class ET_CUDSupportRest extends ET_GetSupportRest
{
    /**
    * @var      string      Folder property e.g. "Category", "CategoryID", etc.
    */
	protected $folderProperty;
	
    /**
    * @var      string      Folder Media Type e.g. "dataextension", "triggered_send", etc.
    */
	protected $folderMediaType;

    // method for calling a Fuel API using POST
    /**
    * @return ET_PostRest     Object of type ET_PostRest which contains http status code, response, etc from the POST REST service 
    */    
	public function post()
	{
		$this->authStub->refreshToken();
		$completeURL = $this->authStub->baseUrl . $this->path;

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
		
//		echo $additionalQS["access_token"] . "\n";
		// $queryString = http_build_query($additionalQS);		
		// $completeURL = "{$completeURL}?{$queryString}";
		// $response = new ET_PostRest($this->authStub, $completeURL, $this->props);			
		$response = new ET_PostRest($this->authStub, $completeURL, $this->props, $this->authStub->getAuthToken());
		
		return $response;
	}

    // method for calling a Fuel API using PATCH
    /**
    * @return ET_PatchRest     Object of type ET_PatchRest which contains http status code, response, etc from the PATCH REST service 
    */    
	public function patch()
	{
		$this->authStub->refreshToken();
		$completeURL = $this->authStub->baseUrl . $this->path;
		$additionalQS = array();
		
		// All URL Props are required when doing Patch	
		foreach($this->urlProps as $value){
			if (is_null($this->props) || !array_key_exists($value,$this->props)){
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
//		echo $additionalQS["access_token"] . "\n";
		// $queryString = http_build_query($additionalQS);		
		// $completeURL = "{$completeURL}?{$queryString}";
		// $response = new ET_PatchRest($this->authStub, $completeURL, $this->props);	
		$response = new ET_PatchRest($this->authStub, $completeURL, $this->props, $this->authStub->getAuthToken());
		
		return $response;
	}

    // method for calling a Fuel API using DELETE
    /**
    * @return ET_DeleteRest     Object of type ET_DeleteRest which contains http status code, response, etc from the DELETE REST service 
    */ 	
	public function delete()
	{
		$this->authStub->refreshToken();
		$completeURL = $this->authStub->baseUrl . $this->path;
		$additionalQS = array();
		
		// All URL Props are required when doing Delete	
		foreach($this->urlProps as $value){
			if (is_null($this->props) || !array_key_exists($value,$this->props)){
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
//		echo $additionalQS["access_token"] . "\n";
		// $queryString = http_build_query($additionalQS);		
		// $completeURL = "{$completeURL}?{$queryString}";
		// $response = new ET_DeleteRest($this->authStub, $completeURL);	
		$response = new ET_DeleteRest($this->authStub, $completeURL, $this->authStub->getAuthToken());
		
		return $response;
	}
}
?>