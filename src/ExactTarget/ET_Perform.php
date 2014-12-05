<?php

namespace ExactTarget;

class ET_Perform extends ET_Constructor {
	function __construct($authStub, $objType, $action, $props) {
		$authStub->refreshToken();
		$perform = array();
		$performRequest = array();
		$performRequest['Action'] = $action;
		$performRequest['Definitions'] = array();
		$performRequest['Definitions'][] = new SoapVar($props, SOAP_ENC_OBJECT, $objType, "http://exacttarget.com/wsdl/partnerAPI");

		$perform['PerformRequestMsg'] = $performRequest;
		$return = $authStub->__soapCall("Perform", $perform, null, null , $out_header);
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
