<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;

/**
 * The class can create and retrieve specific tenant.
 */
class ET_OEM_Client extends ET_Client
{
    /**
    * @param array   $tenantInfo   Dictionary type array which may hold e.g. array('key' => '')
    */
	function CreateTenant($tenantInfo)
	{
		$key = $tenantInfo['key'];
		unset($tenantInfo['key']);
		$completeURL = $this->baseUrl . "/provisioning/v1/tenants/{$key}";
		return new ET_PutRest($this, $completeURL, $tenantInfo, $this->getAuthToken());
	}

    /**
    * @return ET_GetRest     Object of type ET_GetRest which contains http status code, response, etc from the GET REST service 
    */
	function GetTenants()
	{
		$completeURL = $this->baseUrl . "/provisioning/v1/tenants/";
		return new ET_GetRest($this, $completeURL, $this->getAuthToken());
	}
}
?>