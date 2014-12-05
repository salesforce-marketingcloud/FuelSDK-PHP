<?php

namespace ExactTarget;

class ET_Continue extends ET_Constructor {
	function __construct($authStub, $request_id) {
		$authStub->refreshToken();
		$rrm = array();
		$request = array();
		$retrieveRequest = array();

		$retrieveRequest["ContinueRequest"] = $request_id;
		$retrieveRequest["ObjectType"] = null ;

		$request["RetrieveRequest"] = $retrieveRequest;
		$rrm["RetrieveRequestMsg"] = $request;

		$return = $authStub->__soapCall("Retrieve", $rrm, null, null , $out_header);
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
				$this->results = array();
			}

			$this->moreResults = false;

			if ($return->OverallStatus == "MoreDataAvailable") {
				$this->moreResults = true;
			}

			if ($return->OverallStatus != "OK" && $return->OverallStatus != "MoreDataAvailable")
			{
				$this->status = false;
				$this->message = $return->OverallStatus;
			}

			$this->request_id = $return->RequestID;
		}

	}
}
