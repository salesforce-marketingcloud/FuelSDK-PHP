<?php

namespace ExactTarget;

class ET_Constructor {
	public $status, $code, $message, $results, $request_id, $moreResults;
	function __construct($requestresponse, $httpcode, $restcall = false) {

		$this->code = $httpcode;

		if (!$restcall) {
			if(is_soap_fault($requestresponse)) {
				$this->status = false;
				$this->message = "SOAP Fault: (faultcode: {$requestresponse->faultcode}, faultstring: {$requestresponse->faultstring})";
				$this->message = "{$requestresponse->faultcode} {$requestresponse->faultstring})";
			} else {
				$this->status = true;
			}
		} else {
			if ($this->code != 200 && $this->code != 201 && $this->code != 202) {
				$this->status = false;
			} else {
				$this->status = true;
			}

			if (json_decode($requestresponse) != null){
				$this->results = json_decode($requestresponse);
			} else  {
				$this->message = $requestresponse;
			}
		}
	}
}
