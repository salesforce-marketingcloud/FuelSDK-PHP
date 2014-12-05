<?php

namespace ExactTarget;

class ET_Email extends ET_CUDSupport {
	public  $folderId;
	function __construct()
	{
		$this->obj = "Email";
		$this->folderProperty = "CategoryID";
		$this->folderMediaType = "email";
	}
}
