<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;
use \Exception;

/**
* ETDataExtensionRow - Represents Data Extension Row.
*/
class ET_DataExtension_Row extends ET_CUDWithUpsertSupport
{
	/**
	* @var string 			Gets or sets the name of the data extension.
	*/
	public $Name;
	/**
	* @var string 			Gets or sets the data extension customer key.
	*/
	public $CustomerKey;

	/** 
	* Initializes a new instance of the class.
	*/
	function __construct()
	{	
		$this->obj = "DataExtensionObject";
	}

    /**
	* Get this instance.
    * @return ET_Get     Object of type ET_Get which contains http status code, response, etc from the GET SOAP service
    */	
	public function get()
	{
		$this->getName();		
		$this->obj = "DataExtensionObject[".$this->Name."]";		
		$response = parent::get();
		$this->obj = "DataExtensionObject";
		return $response;
	}
	
    /**
	* Post this instance.
    * @return ET_Post     Object of type ET_Post which contains http status code, response, etc from the POST SOAP service
    */	
	public function post()
	{
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

    /**
	* Patch this instance.
    * @return ET_Patch     Object of type ET_Patch which contains http status code, response, etc from the PATCH SOAP service
    */		
	public function patch()
	{
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
	
    /**
	* Delete this instance.
    * @return ET_Delete     Object of type ET_Delete which contains http status code, response, etc from the DELETE SOAP service
    */	
	public function delete()
	{
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
	
	private function getName()
	{
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

	private function getCustomerKey()
	{
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
?>