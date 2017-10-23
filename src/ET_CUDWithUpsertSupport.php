<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;

/**
 * This class represents the put operation for SOAP service.
 */
class ET_CUDWithUpsertSupport extends ET_CUDSupport
{
    /**
    * @return ET_Patch     Object of type ET_Patch which contains http status code, response, etc from the PATCH SOAP service 
    */
	public function put()
	{
		$originalProps = $this->props;
		if (property_exists($this, 'folderProperty') && !is_null($this->folderProperty) && !is_null($this->folderId)){
			$this->props[$this->folderProperty] = $this->folderId;
		} 
		$response = new ET_Patch($this->authStub, $this->obj, $this->props, true);
		$this->props = $originalProps;
		return $response;
	}
}
?>