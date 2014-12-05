<?php

namespace ExactTarget;

class ET_Configure extends ET_Constructor {
	function __construct($authStub, $objType, $action, $props) {
		$authStub->refreshToken();
		$configure = array();
		$configureRequest = array();
		$configureRequest['Action'] = $action;
		$configureRequest['Configurations'] = array();

		if (!ET_AssocArrayUtils::isAssoc($props)) {
			foreach ($props as $value){
				$configureRequest['Configurations'][] = new SoapVar($value, SOAP_ENC_OBJECT, $objType, "http://exacttarget.com/wsdl/partnerAPI");
			}
		} else {
			$configureRequest['Configurations'][] = new SoapVar($props, SOAP_ENC_OBJECT, $objType, "http://exacttarget.com/wsdl/partnerAPI");
		}

		$configure['ConfigureRequestMsg'] = $configureRequest;
		$return = $authStub->__soapCall("Configure", $configure, null, null , $out_header);
		parent::__construct($return, $authStub->__getLastResponseHTTPCode());

		if ($this->status){
			if (property_exists($return->Results, "Result")){
				if (is_array($return->Results->Result)){
					$this->results = $return->Results->Result;
				} else {
					$this->results = array($return->Results->Result);
				}
				if ($return->OverallStatus != "OK"){
					$this->status = false;
				}
			} else {
				$this->status = false;
			}
		}
	}
}
