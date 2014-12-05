<?php

namespace ExactTarget;

class ET_BounceEvent extends ET_GetSupport {
	public  $getSinceLastBatch;
	function __construct()
	{
		$this->obj = "BounceEvent";
		$this->getSinceLastBatch = true;
	}
}
