<?php

namespace ExactTarget;

class ET_OpenEvent extends ET_GetSupport {
	public  $getSinceLastBatch;
	function __construct()
	{
		$this->obj = "OpenEvent";
		$this->getSinceLastBatch = true;
	}
}
