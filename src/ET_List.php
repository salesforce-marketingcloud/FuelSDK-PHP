<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;

/**
 * This class represents a marketing list of subscribers.
 */
class ET_List extends ET_CUDWithUpsertSupport
{
    /**
    * @var int 		Gets or sets the folder identifier.
    */
	public  $folderId;

	/** 
	* Initializes a new instance of the class and set the property obj, folderProperty and folderMediaType to appropriate values.
	*/		
	function __construct()
	{
		$this->obj = "List";
		$this->folderProperty = "Category";
		$this->folderMediaType = "list";
	}
}
?>