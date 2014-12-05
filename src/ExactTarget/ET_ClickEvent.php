<?php

namespace ExactTarget;

class ET_ClickEvent extends ET_GetSupport {
	public  $getSinceLastBatch;
	function __construct()
	{
		$this->obj = "ClickEvent";
		$this->getSinceLastBatch = true;
	}
}
