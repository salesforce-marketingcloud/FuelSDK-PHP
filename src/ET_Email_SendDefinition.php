<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;

/**
 * This class contains the message information, sender profile, delivery profile, and audience information.
 */
class ET_Email_SendDefinition extends ET_CUDSupport
{
    /** @var int 			Gets or sets the folder identifier. */
	public  $folderId;
	/** @var string|null 	contains last task ID if available */
	public  $lastTaskID;

	/** 
	* Initializes a new instance of the class.
	*/
	function __construct() 
	{
		$this->obj = "EmailSendDefinition";
		$this->folderProperty = "CategoryID";
		$this->folderMediaType = "userinitiatedsends";
	}

	/** 
	* Send this instance.
	* @return ET_Perform     Object of type ET_Perform which contains http status code, response, etc from the START SOAP service
	*/	
	function send()
	{
		$originalProps = $this->props;		
		$response = new ET_Perform($this->authStub, $this->obj, 'start', $this->props);
		if ($response->status) {
			$this->lastTaskID = $response->results[0]->Task->ID;
		}
		$this->props = $originalProps;
		return $response;
	}

	/** 
	* Status of this instance.
	* @return ET_Get     Object of type ET_Get which contains http status code, response, etc from the GET SOAP service 
	*/	
	function status()
	{
		$this->filter = array('Property' => 'ID','SimpleOperator' => 'equals','Value' => $this->lastTaskID);
		$response = new ET_Get($this->authStub, 'Send', array('ID','CreatedDate', 'ModifiedDate', 'Client.ID', 'Email.ID', 'SendDate','FromAddress','FromName','Duplicates','InvalidAddresses','ExistingUndeliverables','ExistingUnsubscribes','HardBounces','SoftBounces','OtherBounces','ForwardedEmails','UniqueClicks','UniqueOpens','NumberSent','NumberDelivered','NumberTargeted','NumberErrored','NumberExcluded','Unsubscribes','MissingAddresses','Subject','PreviewURL','SentDate','EmailName','Status','IsMultipart','SendLimit','SendWindowOpen','SendWindowClose','BCCEmail','EmailSendDefinition.ObjectID','EmailSendDefinition.CustomerKey'), $this->filter);
		$this->lastRequestID = $response->request_id;
		return $response;
	}
}
?>