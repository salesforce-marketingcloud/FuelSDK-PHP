<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;

/**
* ETDataExtensionColumn - Represents Data Extension Field.
*/
class ET_DataExtension_Column extends ET_GetSupport
{
	/** 
	* Initializes a new instance of the class.
	*/
	function __construct()
	{
		$this->obj = "DataExtensionField";
		$this->folderProperty = "CategoryID";
		$this->folderMediaType = "dataextension";
	}

    /**
	* Get this instance.
    * @return ET_Get     Object of type ET_Get which contains http status code, response, etc from the GET SOAP service
    */	
	public function get()
	{
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
?>