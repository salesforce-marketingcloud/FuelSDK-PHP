<?php

namespace ExactTarget;

use Exception;

class ET_GetSupportRest extends ET_BaseObjectRest{
	protected $lastPageNumber;
	public function get() {
		$this->authStub->refreshToken();
		$completeURL = $this->endpoint;
		$additionalQS = array();

		if (!is_null($this->props)) {
			foreach ($this->props as $key => $value){
				if (in_array($key,$this->urlProps)){
					$completeURL = str_replace("{{$key}}",$value,$completeURL);
				} else {
					$additionalQS[$key] = $value;
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
		$response = new ET_GetRest($this->authStub, $completeURL, $queryString);

		if (property_exists($response->results, 'page')){
			$this->lastPageNumber = $response->results->page;
			$pageSize = $response->results->pageSize;

			$count = null;
			if (property_exists($response->results, 'count')){
				$count = $response->results->count;
			} else if (property_exists($response->results, 'totalCount')){
				$count = $response->results->totalCount;
			}

			if ($count && ($count > ($this->lastPageNumber * $pageSize))){
				$response->moreResults = true;
			}
		}

		return $response;
	}

	public function getMoreResults() {

		$originalPageValue = 1;
		$removePageFromProps = false;

		if ($this->props && array_key_exists($this->props, '$page')) {
			$originalPageValue = $this->props['page'];
		} else {
			$removePageFromProps = true		;
		}

		if (!$this->props) {
			$this->props = array();
		}

		$this->props['$page'] = $this->lastPageNumber + 1;

		$response = $this->get();

		if ($removePageFromProps) {
			unset($this->props['$page']);
		} else {
			$this->props['$page'] = $originalPageValue;
		}

		return $response;
	}
}
