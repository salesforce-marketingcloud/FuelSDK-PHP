<?php

namespace ExactTarget;

use Exception;

class ET_CUDSupportRest extends ET_GetSupportRest{
	protected $folderProperty, $folderMediaType;
	public function post() {
		$this->authStub->refreshToken();
		$completeURL = $this->endpoint;
		$additionalQS = array();

		if (!is_null($this->props)) {
			foreach ($this->props as $key => $value){
				if (in_array($key,$this->urlProps)){
					$completeURL = str_replace("{{$key}}",$value,$completeURL);
				}
			}
		}

		foreach($this->urlPropsRequired as $value){
			if (is_null($this->props) || in_array($value,$this->props)){
				throw new Exception("Unable to process request due to missing required prop: {$value}");
			}
		}

		// Clean up not required URL parameters
		foreach ($this->urlProps as $value){
			$completeURL = str_replace("{{$value}}","",$completeURL);
		}

		$additionalQS["access_token"] = $this->authStub->getAuthToken();
		$queryString = http_build_query($additionalQS);
		$completeURL = "{$completeURL}?{$queryString}";
		$response = new ET_PostRest($this->authStub, $completeURL, $this->props);

		return $response;
	}

	public function patch() {
		$this->authStub->refreshToken();
		$completeURL = $this->endpoint;
		$additionalQS = array();

		// All URL Props are required when doing Patch
		foreach($this->urlProps as $value){
			if (is_null($this->props) || !array_key_exists($value,$this->props)){
				throw new Exception("Unable to process request due to missing required prop: {$value}");
			}
		}


		if (!is_null($this->props)) {
			foreach ($this->props as $key => $value){
				if (in_array($key,$this->urlProps)){
					$completeURL = str_replace("{{$key}}",$value,$completeURL);
				}
			}
		}
		$additionalQS["access_token"] = $this->authStub->getAuthToken();
		$queryString = http_build_query($additionalQS);
		$completeURL = "{$completeURL}?{$queryString}";
		$response = new ET_PatchRest($this->authStub, $completeURL, $this->props);

		return $response;
	}

	public function delete() {
		$this->authStub->refreshToken();
		$completeURL = $this->endpoint;
		$additionalQS = array();

		// All URL Props are required when doing Delete
		foreach($this->urlProps as $value){
			if (is_null($this->props) || !array_key_exists($value,$this->props)){
				throw new Exception("Unable to process request due to missing required prop: {$value}");
			}
		}

		if (!is_null($this->props)) {
			foreach ($this->props as $key => $value){
				if (in_array($key,$this->urlProps)){
					$completeURL = str_replace("{{$key}}",$value,$completeURL);
				}
			}
		}
		$additionalQS["access_token"] = $this->authStub->getAuthToken();
		$queryString = http_build_query($additionalQS);
		$completeURL = "{$completeURL}?{$queryString}";
		$response = new ET_DeleteRest($this->authStub, $completeURL);

		return $response;
	}
}
