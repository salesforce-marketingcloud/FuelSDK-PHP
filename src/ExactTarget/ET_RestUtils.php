<?php

namespace ExactTarget;

use stdClass;

class ET_RestUtils
{
    public static function restGet($url) {
    	$ch = curl_init();
    	$headers = array("User-Agent: ".getSDKVersion());
    	curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
    	// Uses the URL passed in that is specific to the API used
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_HTTPGET, true);

    	// Need to set ReturnTransfer to True in order to store the result in a variable
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    	// Disable VerifyPeer for SSL
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    	$outputJSON = curl_exec($ch);
    	$responseObject = new stdClass();
    	$responseObject->body = $outputJSON;
    	$responseObject->httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    	return $responseObject;
    }

    // Function for calling a Fuel API using POST
    /**
     * @param string      $url    The resource URL for the REST API
     * @param string      $content    A string of JSON which will be passed to the REST API
    	*
     * @return string     The response payload from the REST service
     */
    public static function restPost($url, $content) {
    	$ch = curl_init();

    	// Uses the URL passed in that is specific to the API used
    	curl_setopt($ch, CURLOPT_URL, $url);

    	// When posting to a Fuel API, content-type has to be explicitly set to application/json
    	$headers = array("Content-Type: application/json", "User-Agent: ".getSDKVersion());
    	curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);

    	// The content is the JSON payload that defines the request
    	curl_setopt ($ch, CURLOPT_POSTFIELDS, $content);

    	//Need to set ReturnTransfer to True in order to store the result in a variable
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    	// Disable VerifyPeer for SSL
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    	$outputJSON = curl_exec($ch);
    	$responseObject = new stdClass();
    	$responseObject->body = $outputJSON;
    	$responseObject->httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    	return $responseObject;
    }


    // Function for calling a Fuel API using PATCH
    /**
     * @param string      $url    The resource URL for the REST API
     * @param string      $content    A string of JSON which will be passed to the REST API
    	*
     * @return string     The response payload from the REST service
     */
    public static function restPatch($url, $content) {
    	$ch = curl_init();

    	// Uses the URL passed in that is specific to the API used
    	curl_setopt($ch, CURLOPT_URL, $url);

    	// When posting to a Fuel API, content-type has to be explicitly set to application/json
    	$headers = array("Content-Type: application/json", "User-Agent: ".getSDKVersion());
    	curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);

    	// The content is the JSON payload that defines the request
    	curl_setopt ($ch, CURLOPT_POSTFIELDS, $content);

    	//Need to set ReturnTransfer to True in order to store the result in a variable
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    	//Need to set the request to be a PATCH
    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH" );

    	// Disable VerifyPeer for SSL
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    	$outputJSON = curl_exec($ch);
    	$responseObject = new stdClass();
    	$responseObject->body = $outputJSON;
    	$responseObject->httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    	return $responseObject;
    }

    // Function for calling a Fuel API using PATCH
    /**
     * @param string      $url    The resource URL for the REST API
     * @param string      $content    A string of JSON which will be passed to the REST API
    	*
     * @return string     The response payload from the REST service
     */
    public static function restPut($url, $content) {
    	$ch = curl_init();

    	// Uses the URL passed in that is specific to the API used
    	curl_setopt($ch, CURLOPT_URL, $url);

    	// When posting to a Fuel API, content-type has to be explicitly set to application/json
    	$headers = array("Content-Type: application/json", "User-Agent: ".getSDKVersion());
    	curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);

    	// The content is the JSON payload that defines the request
    	curl_setopt ($ch, CURLOPT_POSTFIELDS, $content);

    	//Need to set ReturnTransfer to True in order to store the result in a variable
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    	//Need to set the request to be a PATCH
    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT" );

    	// Disable VerifyPeer for SSL
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    	$outputJSON = curl_exec($ch);
    	$responseObject = new stdClass();
    	$responseObject->body = $outputJSON;
    	$responseObject->httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    	return $responseObject;
    }

    public static function restDelete($url) {
    	$ch = curl_init();

    	$headers = array("User-Agent: ".getSDKVersion());
    	curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);

    	// Uses the URL passed in that is specific to the API used
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_HTTPGET, true);

    	// Need to set ReturnTransfer to True in order to store the result in a variable
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    	// Disable VerifyPeer for SSL
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    	// Set CustomRequest up for Delete
    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

    	$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    	$outputJSON = curl_exec($ch);

    	$responseObject = new stdClass();
    	$responseObject->body = $outputJSON;
    	$responseObject->httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    	return $responseObject;
    }
}
