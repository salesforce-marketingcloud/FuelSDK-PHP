<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;
/**
 *	Contains information pertaining to the specific event of an email message bounce.
 */
class ET_BounceEvent extends ET_GetSupport
{
    /**
    * @var bool 	Gets or sets a boolean value indicating whether to get since last batch. true if get since last batch; otherwise, false.
    */
	public  $getSinceLastBatch;

	/** 
	* Initializes a new instance of the class and set the since last batch to true.
	*/
	function __construct() 
	{
		$this->obj = "BounceEvent";
		$this->getSinceLastBatch = true;
	}
}
?>