<?php
/**
 * This class represents the base object for REST operation.
 */
class ET_BaseObjectRest
{
   /**
     * @var      ET_Client   The ET client object which performs the auth token, refresh token using clientID clientSecret
    */
    public  $authStub;
    /**
    * @var      array       Dictionary type array which may hold e.g. array('id' => '', 'key' => '')
    */
    public  $props;
    /**
    * @var      string      Organization Identifier.
    */
    public  $organizationId;
    /**
    * @var      string      Organization Key.
    */
    public  $organizationKey;
    /**
     * @var      string      $endpoint         URL of the API endpoint
     * @var      string[]    $urlProps         array of string having properties or fields name found in the endpoint URL
     * @var      string[]    $urlPropsRequired array of string having only required fields
     */
	protected  $endpoint;
    /**
     * @var      string[]    $urlProps         array of string having properties or fields name found in the endpoint URL
     */
    protected $urlProps;
    /**
     * @var      string[]    $urlPropsRequired array of string having only required fields*/
    protected $urlPropsRequired;
}
?>