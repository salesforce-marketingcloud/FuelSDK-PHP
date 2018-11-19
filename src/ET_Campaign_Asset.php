<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;

/**
* Represents an asset associated with a campaign.
*/ 
class ET_Campaign_Asset extends ET_CUDSupportRest 
{
    /**
    * Initializes a new instance of the class and will assign endpoint, urlProps, urlPropsRequired fields of parent ET_BaseObjectRest
    */ 
	function __construct()
	{
		$this->path = "/hub/v1/campaigns/{id}/assets/{assetId}";		
		$this->urlProps = array("id", "assetId");
		$this->urlPropsRequired = array("id");
	}
}
?>