<?php

namespace ExactTarget;

class ET_Campaign_Asset extends ET_CUDSupportRest {
	function __construct() {
		$this->endpoint = "https://www.exacttargetapis.com/hub/v1/campaigns/{id}/assets/{assetId}";
		$this->urlProps = array("id", "assetId");
		$this->urlPropsRequired = array("id");
	}
}
