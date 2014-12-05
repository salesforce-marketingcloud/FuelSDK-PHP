<?php

namespace ExactTarget;

class ET_DataExtension extends ET_CUDSupport {
	public  $columns;
	function __construct() {
		$this->obj = "DataExtension";
	}

	public function post() {

		$originalProps = $this->props;
		if (ET_AssocArrayUtils::isAssoc($this->props)){
			$this->props["Fields"] = array("Field"=>array());
			if (!is_null($this->columns) && is_array($this->columns)){
				foreach ($this->columns as $column){
					array_push($this->props['Fields']['Field'], $column);
				}
			}
		} else {
			$newProps = array();
			foreach ($this->props as $DE) {
				$newDE = $DE;
				$newDE["Fields"] = array("Field"=>array());
				if (!is_null($DE['columns']) && is_array($DE['columns'])){
					foreach ($DE['columns'] as $column){
						array_push($newDE['Fields']['Field'], $column);
					}
				}
				array_push($newProps, $newDE);
			}
			$this->props = $newProps;
		}

		$response = parent::post();

		$this->props = $originalProps;
		return $response;
	}

	public function patch() {
		$this->props["Fields"] = array("Field"=>array());
		foreach ($this->columns as $column){
			array_push($this->props['Fields']['Field'], $column);
		}
		$response = parent::patch();
		unset($this->props["Fields"]);
		return $response;
	}
}
