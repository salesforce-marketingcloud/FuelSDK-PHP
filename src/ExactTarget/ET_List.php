<?php

namespace ExactTarget;

class ET_List extends ET_CUDWithUpsertSupport {
	public  $folderId;
	function __construct() {
		$this->obj = "List";
		$this->folderProperty = "Category";
		$this->folderMediaType = "list";
	}
}
