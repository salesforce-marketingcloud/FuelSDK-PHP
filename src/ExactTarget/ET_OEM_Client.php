<?php

namespace ExactTarget;

class ET_OEM_Client extends ET_Client {

	function CreateTenant($tenantInfo) {
		$key = $tenantInfo['key'];
		unset($tenantInfo['key']);
		$additionalQS = array();
		$additionalQS["access_token"] = $this->getAuthToken();
		$queryString = http_build_query($additionalQS);
		$completeURL = "https://www.exacttargetapis.com/provisioning/v1/tenants/{$key}?{$queryString}";
		return new ET_PutRest($this, $completeURL, $tenantInfo);
	}

	function GetTenants() {
		$additionalQS = array();
		$additionalQS["access_token"] = $this->getAuthToken();
		$queryString = http_build_query($additionalQS);
		$completeURL = "https://www.exacttargetapis.com/provisioning/v1/tenants/?{$queryString}";
		return new ET_GetRest($this, $completeURL, $queryString);
	}

}
