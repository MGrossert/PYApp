<?php
/**
 * PY dcoUser
 * 
 * @package PYApp
 * @author Martin Grossert <martin.grossert@gmail.com>
 * @version 0.1.0 
 * @copyright Copyright (c) 2013, Martin Grossert
 * @license GNU GENERAL PUBLIC LICENSE
 */

class py_user extends data_container_object {

	#######################################
	# INTERNAL VARS
	
	static public $overrideTable = "";
	static public $fields = array()
	static public $view = array()
	
	#######################################
	# MAGIC METHODS
	
	# construct class
	function __construct() {
		
	}
	
}