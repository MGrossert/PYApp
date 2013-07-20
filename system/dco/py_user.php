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
	
	#######################################
	# MAGIC METHODS
	
	# construct class
	function __construct() {
		
	}
	
}

py_user::field = [
	'id' => [
		'keys'	=> 'primary'
		'sql'	=> 'INT (11) NOT NULL AUTO INCEMENT'
	]
];