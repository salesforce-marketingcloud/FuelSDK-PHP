<?php

namespace ExactTarget;

class ET_ContentArea extends ET_CUDSupport {
	public  $folderId;
	function __construct() {
		$this->obj = "ContentArea";
		$this->folderProperty = "CategoryID";
		$this->folderMediaType = "content";
	}
}
