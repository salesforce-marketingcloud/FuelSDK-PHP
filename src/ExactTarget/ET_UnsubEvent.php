<?php

namespace ExactTarget;

class ET_UnsubEvent extends ET_GetSupport {
	public  $getSinceLastBatch;
	function __construct()
	{
		$this->obj = "UnsubEvent";
		$this->getSinceLastBatch = true;
	}
}
