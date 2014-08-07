<?php

class HookList extends ObjectList
{
	use Singleton;
	
	protected function __initialize ()
	{
		
	}
	
	function register ($name)
	{
		$hook = new Hook($name);
		parent::register($name, $hook);
	}
	
	function call ($name, $param = array())
	{
		$hook = $this->get($name);
		$ret = $hook->call($param);
		$this->unregister($name);
		return $ret;
	}
	
	function registerCall ($name, $func)
	{
		$hook = $this->get($name);
		return ($hook) ? $hook->register($func) : false;
	}
}
