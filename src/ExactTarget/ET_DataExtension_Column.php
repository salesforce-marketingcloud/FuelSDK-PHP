<?php

namespace ExactTarget;

class ET_DataExtension_Column extends ET_GetSupport {
	function __construct() {
		$this->obj = "DataExtensionField";
		$this->folderProperty = "CategoryID";
		$this->folderMediaType = "dataextension";
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
