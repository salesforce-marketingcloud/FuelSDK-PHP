<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;
use \Exception;

/**
 * This class represents the create, update, delete operation for SOAP service.
 */
class ET_CUDSupport extends ET_GetSupport
{

    /**
    * @return ET_Post     Object of type ET_Post which contains http status code, response, etc from the POST SOAP service 
    */
	public function post()
	{
		$originalProps = $this->props;
		if (property_exists($this, 'folderProperty') && !is_null($this->folderProperty) && !is_null($this->folderId)){
			$this->props[$this->folderProperty] = $this->folderId;
		} else if (property_exists($this, 'folderProperty') && !is_null($this->authStub->packageName)){
			if (is_null($this->authStub->packageFolders)) {
				$getPackageFolder = new ET_Folder();
				$getPackageFolder->authStub = $this->authStub;
				$getPackageFolder->props = array("ID", "ContentType");
				$getPackageFolder->filter = array("Property" => "Name", "SimpleOperator" => "equals", "Value" => $this->authStub->packageName);
				$resultPackageFolder = $getPackageFolder->get();
				if ($resultPackageFolder->status){
					$this->authStub->packageFolders = array();
					foreach ($resultPackageFolder->results as $result){
						$this->authStub->packageFolders[$result->ContentType] = $result->ID;
					}	
				} else {
					throw new Exception('Unable to retrieve folders from account due to: '.$resultPackageFolder->message);
				}
			}
			
			if (!array_key_exists($this->folderMediaType,$this->authStub->packageFolders )){
				if (is_null($this->authStub->parentFolders)) {
					$parentFolders = new ET_Folder();
					$parentFolders->authStub = $this->authStub;
					$parentFolders->props = array("ID", "ContentType");
					$parentFolders->filter = array("Property" => "ParentFolder.ID", "SimpleOperator" => "equals", "Value" => "0");
					$resultParentFolders = $parentFolders->get();
					if ($resultParentFolders->status) { 
						$this->authStub->parentFolders = array();
						foreach ($resultParentFolders->results as $result){
							$this->authStub->parentFolders[$result->ContentType] = $result->ID;
						}	
					} else {
						throw new Exception('Unable to retrieve folders from account due to: '.$resultParentFolders->message);
					}
				}
				$newFolder = new ET_Folder();
				$newFolder->authStub = $this->authStub;
				$newFolder->props = array("Name" => $this->authStub->packageName, "Description" => $this->authStub->packageName, "ContentType"=> $this->folderMediaType, "IsEditable"=>"true", "ParentFolder" => array("ID" => $this->authStub->parentFolders[$this->folderMediaType]));
				$folderResult = $newFolder->post();
				if ($folderResult->status) {
					$this->authStub->packageFolders[$this->folderMediaType] = $folderResult->results[0]->NewID;
				} else {
					throw new Exception('Unable to create folder for Post due to: '.$folderResult->message);
				}
			}
			$this->props[$this->folderProperty] = $this->authStub->packageFolders[$this->folderMediaType];
		} 
		
		$response = new ET_Post($this->authStub, $this->obj, $this->props);
		$this->props = $originalProps;
		return $response;
	}

    /**
    * @return ET_Patch     Object of type ET_Patch which contains http status code, response, etc from the PATCH SOAP service 
    */
	public function patch()
	{
		$originalProps = $this->props;
		if (property_exists($this, 'folderProperty') && !is_null($this->folderProperty) && !is_null($this->folderId)){
			$this->props[$this->folderProperty] = $this->folderId;
		} 
		$response = new ET_Patch($this->authStub, $this->obj, $this->props);
		$this->props = $originalProps;
		return $response;
	}
	
    /**
    * @return ET_Delete     Object of type ET_Delete which contains http status code, response, etc from the DELETE SOAP service 
    */ 	
	public function delete()
	{	
		$response = new ET_Delete($this->authStub, $this->obj, $this->props);
		return $response;
	}	
}

?>