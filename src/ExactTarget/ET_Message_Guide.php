<?php

namespace ExactTarget;

class ET_Message_Guide extends ET_CUDSupportRest {
	function __construct() {
		$this->endpoint = "https://www.exacttargetapis.com/guide/v1/messages/{id}";
		$this->urlProps = array("id");
		$this->urlPropsRequired = array();
	}
	function get() {
		$origEndpoint = $this->endpoint;
		$origProps = $this->urlProps;
		if (count($this->props) == 0) {
			$this->endpoint = "https://www.exacttargetapis.com/guide/v1/messages/f:@all";
		} elseif (array_key_exists('key',$this->props)){
			$this->endpoint = "https://www.exacttargetapis.com/guide/v1/messages/key:{key}";
			$this->urlProps = array("key");
		}
		$response = parent::get();
		$this->endpoint = $origEndpoint;
		$this->urlProps = $origProps;

		return $response;
	}

	function convert() {
		$completeURL = "https://www.exacttargetapis.com/guide/v1/messages/convert?access_token=" . $this->authStub->getAuthToken();

		$response = new ET_PostRest($this->authStub, $completeURL, $this->props);
		return $response;

	}

	function sendProcess() {
		$renderMG = new ET_Message_Guide();
		$renderMG->authStub = $this->authStub;
		$renderMG->props = array("id" => $this->props['messageID']);
		$renderResult = $renderMG->render();
		if(!$renderResult->status){
			return $renderResult;
		}

		$html = $renderResult->results->emailhtmlbody;
		$send = array();
		$send["Email"] = array("Subject"=> $this->props['subject'], "HTMLBody"=> $html);
		$send["List"] = array("ID"=> $this->props['listID']);
		$response = new ET_Post($this->authStub, "Send", $send);
		return $response;
	}

	function render() {

		$completeURL = null;
		$response = null;

		if (is_array($this->props) && array_key_exists("id", $this->props)) {
			$completeURL = "https://www.exacttargetapis.com/guide/v1/messages/render/{$this->props['id']}?access_token=" . $this->authStub->getAuthToken();
			$response = new ET_GetRest($this->authStub, $completeURL, null);
		} else {
			$completeURL = "https://www.exacttargetapis.com/guide/v1/messages/render?access_token=" . $this->authStub->getAuthToken();
			$response = new ET_PostRest($this->authStub, $completeURL, $this->props);
		}
		return $response;
	}
}
