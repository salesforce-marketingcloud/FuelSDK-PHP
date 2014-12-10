<?php

namespace ExactTarget;

use SoapVar;

class ET_Delete extends ET_Constructor {
	function __construct($authStub, $objType, $props) {
		$authStub->refreshToken();
		$cr = array();
		$objects = array();
		$object = $props;

		$objects["Objects"] = new SoapVar($props, SOAP_ENC_OBJECT, $objType, "http://exacttarget.com/wsdl/partnerAPI");
		$objects["Options"] = "";
		$cr["DeleteRequest"] = $objects;

		$return = $authStub->__soapCall("Delete", $cr, null, null , $out_header);
		parent::__construct($return, $authStub->__getLastResponseHTTPCode());

		if ($this->status){
			if (property_exists($return, "Results")){
				// We always want the results property when doing a retrieve to be an array
				if (is_array($return->Results)){
					$this->results = $return->Results;
				} else {
					$this->results = array($return->Results);
				}
			} else {
				$this->status = false;
			}
			if ($return->OverallStatus != "OK")
			{
				$this->status = false;
			}
		}
	}
}
