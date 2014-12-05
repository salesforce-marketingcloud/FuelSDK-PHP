<?php

namespace ExactTarget;

class ET_Campaign extends ET_CUDSupportRest {
	function __construct() {
		$this->endpoint = "https://www.exacttargetapis.com/hub/v1/campaigns/{id}";
		$this->urlProps = array("id");
		$this->urlPropsRequired = array();
	}
}
