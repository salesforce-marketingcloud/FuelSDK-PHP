<?php

namespace ExactTarget;

use \Exception;

class ET_DataExtension_Row extends ET_CUDWithUpsertSupport {
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
