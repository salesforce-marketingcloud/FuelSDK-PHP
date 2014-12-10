<?php

namespace ExactTarget;

use SoapVar;

class ET_Import extends ET_CUDSupport {
	public  $lastTaskID;
	function __construct()
	{
		$this->obj = "ImportDefinition";
	}

	function post() {
		$originalProp = $this->props;

		# If the ID property is specified for the destination then it must be a list import
		if (array_key_exists('DestinationObject', $this->props)) {
			if (array_key_exists('ID', $this->props['DestinationObject'])){
				$this->props['DestinationObject'] = new SoapVar($this->props['DestinationObject'], SOAP_ENC_OBJECT, 'List', "http://exacttarget.com/wsdl/partnerAPI");
			}
		}

		$obj = parent::post();
		$this->props = $originalProp;
		return $obj;
	}

	function start(){
		$originalProps = $this->props;
		$response = new ET_Perform($this->authStub, $this->obj, 'start', $this->props);
		if ($response->status) {
			$this->lastTaskID = $response->results[0]->Task->ID;
		}
		$this->props = $originalProps;
		return $response;
	}

	function status(){
		$this->filter = array('Property' => 'TaskResultID','SimpleOperator' => 'equals','Value' => $this->lastTaskID);
		$response = new ET_Get($this->authStub, 'ImportResultsSummary', array('ImportDefinitionCustomerKey','TaskResultID','ImportStatus','StartDate','EndDate','DestinationID','NumberSuccessful','NumberDuplicated','NumberErrors','TotalRows','ImportType'), $this->filter);
		$this->lastRequestID = $response->request_id;
		return $response;
	}
}
