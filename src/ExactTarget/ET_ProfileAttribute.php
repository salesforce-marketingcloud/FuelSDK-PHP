<?php

namespace ExactTarget;

class ET_ProfileAttribute extends ET_BaseObject {
	function __construct()
	{
		$this->obj = "PropertyDefinition";
	}

	function post(){
		return new ET_Configure($this->authStub, $this->obj, "create", $this->props);
	}

	function get(){
		return new ET_Info($this->authStub, 'Subscriber', true);
	}

	function patch() {
		return new ET_Configure($this->authStub, $this->obj, "update", $this->props);
	}

	function delete() {
		return new ET_Configure($this->authStub, $this->obj, "delete", $this->props);
	}

}
