<?php

namespace ExactTarget;

class ET_GetRest extends ET_Constructor {
	function __construct($authStub, $url, $qs = null) {
		$restResponse = ET_RestUtils::restGet($url);
		$this->moreResults = false;
		parent::__construct($restResponse->body, $restResponse->httpcode, true);
	}
}
