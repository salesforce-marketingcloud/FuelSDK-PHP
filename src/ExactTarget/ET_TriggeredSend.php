<?php

namespace ExactTarget;

class ET_TriggeredSend extends ET_CUDSupport {
	public  $subscribers, $folderId;
	function __construct() {
		$this->obj = "TriggeredSendDefinition";
		$this->folderProperty = "CategoryID";
		$this->folderMediaType = "triggered_send";
	}

	public function Send() {
		$tscall = array("TriggeredSendDefinition" => $this->props , "Subscribers" => $this->subscribers);
		$response = new ET_Post($this->authStub, "TriggeredSend", $tscall);
		return $response;
	}
}
