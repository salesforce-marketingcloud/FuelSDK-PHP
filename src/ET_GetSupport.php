<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;

/**
 * This class represents the get operation for SOAP service.
 */
class ET_GetSupport extends ET_BaseObject
{
    /**
    * @return ET_Get     Object of type ET_Get which contains http status code, response, etc from the GET SOAP service 
    */
	public function get()
	{
                $retrieveRequest=array();
		if (property_exists($this,'retrieveRequest' )){
			$retrieveRequest = $this->retrieveRequest;
		}
		if (property_exists($this,'getSinceLastBatch')){
			$retrieveRequest["RetrieveAllSinceLastBatch"] = $this->getSinceLastBatch;
		}
                
		$response = new ET_Get($this->authStub, $this->obj, $this->props, $this->filter, $retrieveRequest);
		$this->lastRequestID = $response->request_id;		
		return $response;
	}

    /**
    * @return ET_Continue    returns more response from the SOAP service
    */	
	public function getMoreResults()
	{
		$response = new ET_Continue($this->authStub, $this->lastRequestID);
		$this->lastRequestID = $response->request_id;
		return $response;
	}

    /**
    * @return ET_Info    returns information from the SOAP service
    */	
	public function info()
	{
		$response = new ET_Info($this->authStub, $this->obj);
		return $response;
	}	
}
?>