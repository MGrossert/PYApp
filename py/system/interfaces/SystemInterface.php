<?php

interface SystemInterface {

	/**
	 * returns the service registry
	 *
	 * @return \ServiceRegistryInterface
	 */
	function service ();

	/**
	 * initalize the system
	 */
	function initialize ();
	
}