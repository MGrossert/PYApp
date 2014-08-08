<?php

interface HookRegistryInterface
{
	
	/**
	 * prepare a hook
	 *
	 * @param string $name Hook-Name
	 * @return boolean
	 */
	function prepare ($name);
	
	/**
	 * execute all hook callables
	 *
	 * @param string $name hook name
	 * @param array $param function parameters
	 */
	function call ($name, $param = array());
	
	/**
	 * register a callable at a hook
	 *
	 * @param string $name hook name
	 * @param function $func
	 * @return boolean | referenz
	 */
	function register ($name, $func);
	
	/**
	 * remove a callable from a hook
	 *
	 * @param string $name hook name
	 * @return boolean
	 */
	function unregister ($name);
	
	/**
	 * remove a prepared hook
	 *
	 * @param string $name hook name
	 */
	function remove ($name);
}
