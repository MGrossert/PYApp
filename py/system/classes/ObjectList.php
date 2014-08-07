<?php

namespace PY {

class ObjectList
{
	private $objects = [];
	var $length = 0;
	
	function register ($name, $obj)
	{
		$this->objects[$name] = $obj;
		$length = count($this->objects);
		return true;
	}
	
	function unregister ($name)
	{
		if (isset($this->objects[$name])) {
			unset($this->objects[$name]);
			$length = count($this->objects);
		}
	}
	
	function get ($name)
	{
		return isset($this->objects[$name]) ? $this->objects[$name] : false;
	}
	
	function has ($name)
	{
		return isset($this->objects[$name]);
	}
	
	function getAll ()
	{
		return $this->objects;
	}
	
}

}
