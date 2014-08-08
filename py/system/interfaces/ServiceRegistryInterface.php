<?php

interface ServiceRegistryInterface
{
	
	/**
	 * prepare/setup a service type
	 *
	 * @param string $type
	 * @param interface|string $interface
	 * @param string $default (optional) default class/object
	 * @return boolean
	 */
	function prepare ($type, $interface, $default = null);
	
	/**
	 *
	 * @param unknown $type
	 * @param unknown $name
	 * @param unknown $oObject
	 * @return boolean
	 */
	function register ($type, $oObject, $name = false);
	
	/**
	 *
	 * @param unknown $type
	 * @return boolean
	 */
	function unregister ($type);
	
	/**
	 *
	 * @return array
	 */
	function getServiceTypeList ();
	
	/**
	 *
	 * @param string $type
	 * @return array
	 */
	function getServiceList ($type);
	
	/**
	 *	get a special service
	 *
	 * @param unknown $name
	 * @param string $name (optional)
	 * @return Ambigous <boolean, stdClass>
	 */
	function get ($type, $name = false);
	
	/**
	 * get a service type object
	 *
	 * @param unknown $type
	 */
	function getServiceType ($type);
	
}
