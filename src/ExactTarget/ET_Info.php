<?php

namespace ExactTarget;

class ET_Info extends ET_Constructor {
	function __construct($authStub, $objType, $extended = false) {
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

				if ($extended) {
					$this->results = $return->ObjectDefinition->ExtendedProperties->ExtendedProperty;
				}
				else {
					$this->results = $return->ObjectDefinition->Properties;
				}
			} else {
				$this->status = false;
			}
		}
	}
}
