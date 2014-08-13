<?php

interface SystemInterface
{
	
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

	/**
	 * get a specific template
	 *
	 * @param string $view template name
	 * @return boolean|string the template path
	 */
	function getTemplate ($view);

	/**
	 * returns the current structure
	 *
	 * @return Ambigous <boolean, multitype:, mixed>
	 */
	function getStructure ();

	/**
	 * read & returns the default structure
	 *
	 * @return Ambigous <boolean, multitype:, mixed>
	 */
	function parseStructure ();
}
