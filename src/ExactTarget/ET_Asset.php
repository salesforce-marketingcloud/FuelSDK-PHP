<?php

namespace ExactTarget;

use stdClass;

class ET_Asset extends ET_CUDSupportRest {
	function __construct() {
		$this->endpoint = "https://www.exacttargetapis.com/guide/v1/contentItems/portfolio/{id}";
		$this->urlProps = array("id");
		$this->urlPropsRequired = array();
	}

	public function upload() {
		$completeURL = "https://www.exacttargetapis.com/guide/v1/contentItems/portfolio/fileupload?access_token=" . $this->authStub->getAuthToken();

		$post = array('file_contents'=>'@'.$this->attrs['filePath']);

        $ch = curl_init();

		$headers = array("User-Agent: ".getSDKVersion());
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);

		curl_setopt($ch, CURLOPT_URL, $completeURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

		// Disable VerifyPeer for SSL
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$outputJSON = curl_exec($ch);
		curl_close ($ch);

		$responseObject = new stdClass();
		$responseObject->body = $outputJSON;
		$responseObject->httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		return $responseObject;
	}

	public function patch() {
		return null;
	}

	public function delete() {
		return null;
	}
}
