<?php

namespace ExactTarget;

class ET_SentEvent extends ET_GetSupport {
	public  $getSinceLastBatch;
	function __construct()
	{
		$this->obj = "SentEvent";
		$this->getSinceLastBatch = true;
	}
}
