<?php

namespace ExactTarget;

class ET_PutRest extends ET_Constructor {
	function __construct($authStub, $url, $props) {
		$restResponse = ET_RestUtils::restPut($url, json_encode($props));
		parent::__construct($restResponse->body, $restResponse->httpcode, true);
	}
}
