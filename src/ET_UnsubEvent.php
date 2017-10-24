<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;

/**
* Contains information regarding a specific unsubscription action taken by a subscriber.
*/
class ET_UnsubEvent extends ET_GetSupport
{
    /**
    * @var bool 	Gets or sets a boolean value indicating whether this object get since last batch. true if get since last batch; otherwise, false.
    */
	public  $getSinceLastBatch;

	/** 
	* Initializes a new instance of the class and set the since last batch to true.
	*/	
	function __construct() 
	{
		$this->obj = "UnsubEvent";
		$this->getSinceLastBatch = true;
	}
}
?>