<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;

/** 
* Represents a program in an account
*/
class ET_Campaign extends ET_CUDSupportRest
{
    /**
    * Initializes a new instance of the class and will assign endpoint, urlProps, urlPropsRequired fields of parent ET_BaseObjectRest
    */ 
	function __construct()
	{
		$this->path = "/hub/v1/campaigns/{id}";		
		$this->urlProps = array("id");
		$this->urlPropsRequired = array();
	}
}
?>