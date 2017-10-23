<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;

/**
 * This class represents a folder in a Marketing Cloud account.
 */
class ET_Folder extends ET_CUDSupport
{
	/** 
	* Initializes a new instance of the class and sets the obj property of parent.
	*/
	function __construct()
	{
		$this->obj = "DataFolder";
	}
}
?>