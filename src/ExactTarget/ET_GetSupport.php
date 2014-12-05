<?php

namespace ExactTarget;

class ET_GetSupport extends ET_BaseObject{

	public function get() {
		$lastBatch = false;
		if (property_exists($this,'getSinceLastBatch' )){
			$lastBatch = $this->getSinceLastBatch;
		}
		$response = new ET_Get($this->authStub, $this->obj, $this->props, $this->filter, $lastBatch);
		$this->lastRequestID = $response->request_id;
		return $response;
	}

	public function getMoreResults() {
		$response = new ET_Continue($this->authStub, $this->lastRequestID);
		$this->lastRequestID = $response->request_id;
		return $response;
	}

	public function info() {
		$response = new ET_Info($this->authStub, $this->obj);
		return $response;
	}
}
