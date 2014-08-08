<?php

namespace PY {

class HookRegistry extends ObjectList implements \HookRegistryInterface
{
	
	function prepare ($name)
	{
		$hook = new Hook($name);
		$this->add($name, $hook);
		return true;
	}
	
	function call ($name, $param = array())
	{
		$hook = $this->get($name);
		if ($hook) {
			$ret = $hook->call($param);
			$this->remove($name);
			return $ret;
		}
		return false;
	}
	
	function register ($name, $func)
	{
		$hook = $this->get($name);
		var_export($hook);
		var_export($func);
		return ($hook) ? $hook->register($func) : false;
	}
	
	function unregister ($name)
	{
		$hook = $this->get($name);
		return ($hook) ? $hook->remove($func) : false;
	}
	
}

}
