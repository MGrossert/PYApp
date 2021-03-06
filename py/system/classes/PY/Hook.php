<?php

namespace PY {

class Hook extends ObjectList
{
	var $name;
	
	function __construct ($name)
	{
		$this->name = $name;
	}
	
	function register ($func)
	{
		$idx = uniqid("hook_", true);
		$ret = $this->add($idx, $func);
		return $ret ? $idx : false;
	}
	
	function unregister ($name)
	{
		$this->remove($name);
	}
	
	function call ($param = array())
	{
		$ret = [];
		foreach ($this->getAll() as $idx => $func) {
			$ret[] = call_user_func_array($func, $param);
		}
		return $ret;
	}
	
}

}
