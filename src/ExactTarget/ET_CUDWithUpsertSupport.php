<?php

namespace ExactTarget;

class ET_CUDWithUpsertSupport extends ET_CUDSupport{
	public function put(){
		$originalProps = $this->props;
		if (property_exists($this, 'folderProperty') && !is_null($this->folderProperty) && !is_null($this->folderId)){
			$this->props[$this->folderProperty] = $this->folderId;
		}
		$response = new ET_Patch($this->authStub, $this->obj, $this->props, true);
		$this->props = $originalProps;
		return $response;
	}
}
