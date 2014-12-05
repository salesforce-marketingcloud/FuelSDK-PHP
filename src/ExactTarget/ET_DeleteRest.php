<?php

namespace ExactTarget;

class ET_DeleteRest extends ET_Constructor {
	function __construct($authStub, $url) {
		$restResponse = ET_RestUtils::restDelete($url);
		parent::__construct($restResponse->body, $restResponse->httpcode, true);
	}
}
