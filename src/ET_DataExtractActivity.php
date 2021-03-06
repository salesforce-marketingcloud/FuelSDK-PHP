<?php
namespace FuelSdk;


class ET_DataExtractActivity extends ET_GetSupport
{
	/** @var string|null 	contains last task ID if available */
	public  $lastTaskID;

	private $extractmap;

	/** 
	* Initializes a new instance of the class.
	*/
	function __construct() 
	{
		//$this->populateExtractType();
	}    

    /**
	* This method start this import process.
    * @return ET_PerformExtract     Object of type ET_PerformExtract which contains http status, request ID, etc from the Extract SOAP service 
    */	
	function start()
	{
		$originalProps = $this->props;		
		$response = new ET_PerformExtract($this->authStub, $this->props);
		//print_r($response);
		return $response;
	}

    private function populateExtractType()
    {
        $extractdesc = new ET_ExtractDescription();
        $extractdesc->authStub = $this->authStub;
        $extractdesc->props = array("ID","CustomerKey","Name", "Description","InteractionObjectID", "ObjectID","PartnerKey","CreatedDate","Client.ID","EventType","BatchID","TriggeredSendDefinitionObjectID","PartnerKey");
        $extractResponse = $extractdesc->get();
        foreach($extractResponse->results as $obj){
            $this->extractmap[$obj->Name] = $obj->ObjectID;
        }
		//print_r($this->extractmap);
    }	

    /**
	* This method start this import process.
    * @return ET_PerformExtract     Object of type ET_PerformExtract which contains http status, request ID, etc from the Extract SOAP service 
	* @param 	string 		$deCustomerKey	data extension customer key
	* @param 	string 		$outputFileName	name of the ouput file
    */	
	function extractDataExtension($deCustomerKey, $outputFileName="PHP_data_extract_DE.csv")
	{
		$this->populateExtractType();
		$extracttype = "Data Extension Extract";
		$Parameters= array( 				
			"Parameter"=>array(
				array("Name"=>"StartDate", "Value"=>"1900-01-01 01:00 AM"),
				array("Name"=>"EndDate", "Value"=>"1900-01-01 01:00 AM"),
				array("Name"=>"OutputFileName", "Value"=>$outputFileName),
				array("Name"=>"DECustomerKey", "Value"=>$deCustomerKey),
				array("Name"=>"_AsyncID", "Value"=>"0")
			)
		);

		$this->props = array("ID"=>$this->extractmap[$extracttype], "Options"=>"", "Parameters"=>$Parameters);

		$startResponse = $this->start();
		return $startResponse;
	}

    /**
	* This method start this import process.
    * @return ET_PerformExtract     Object of type ET_PerformExtract which contains http status, request ID, etc from the Extract SOAP service 
	* @param 	string 		$startDate		start date time in YYYY-mm-dd hh:MM AM format
	* @param 	string 		$endDate		end date time in YYYY-mm-dd hh:MM AM format
	* @param 	string 		$outputFileName	name of the ouput file
    */	
	function extractTrackingData($startDate, $endDate, $outputFileName="PHP_data_extract_tracking.csv")
	{
		$this->populateExtractType();
		$extracttype = "Tracking Extract";
		$Parameters= array( 				
			"Parameter"=>array(
				array("Name"=>"StartDate", "Value"=>$startDate),
				array("Name"=>"EndDate", "Value"=>$endDate),
				array("Name"=>"OutputFileName", "Value"=>$outputFileName)
			)
		);

		$this->props = array("ID"=>$this->extractmap[$extracttype], "Options"=>"", "Parameters"=>$Parameters);

		$startResponse = $this->start();
		return $startResponse;
	}

}

?>