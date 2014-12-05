<?php

namespace ExactTarget;

class ET_Email_SendDefinition extends ET_CUDSupport {
	public  $folderId,  $lastTaskID;
	function __construct()
	{
		$this->obj = "EmailSendDefinition";
		$this->folderProperty = "CategoryID";
		$this->folderMediaType = "userinitiatedsends";
	}

	function send(){
		$originalProps = $this->props;
		$response = new ET_Perform($this->authStub, $this->obj, 'start', $this->props);
		if ($response->status) {
			$this->lastTaskID = $response->results[0]->Task->ID;
		}
		$this->props = $originalProps;
		return $response;
	}

	function status(){
		$this->filter = array('Property' => 'ID','SimpleOperator' => 'equals','Value' => $this->lastTaskID);
		$response = new ET_Get($this->authStub, 'Send', array('ID','CreatedDate', 'ModifiedDate', 'Client.ID', 'Email.ID', 'SendDate','FromAddress','FromName','Duplicates','InvalidAddresses','ExistingUndeliverables','ExistingUnsubscribes','HardBounces','SoftBounces','OtherBounces','ForwardedEmails','UniqueClicks','UniqueOpens','NumberSent','NumberDelivered','NumberTargeted','NumberErrored','NumberExcluded','Unsubscribes','MissingAddresses','Subject','PreviewURL','SentDate','EmailName','Status','IsMultipart','SendLimit','SendWindowOpen','SendWindowClose','BCCEmail','EmailSendDefinition.ObjectID','EmailSendDefinition.CustomerKey'), $this->filter);
		$this->lastRequestID = $response->request_id;
		return $response;
	}
}
