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
	
	protected static $field = [
		'id' => [
			'sql'	=> 'INT (11) NOT NULL AUTO INCEMENT'
		]
	,	'name'	=> [
			'sql'	=> 'VARCHAR(25) NOT NULL'
		,	'type'	=> 'text'
		]
	,	'password'	=> [
			'sql'	=> 'VARCHAR(25) NOT NULL'
		,	'type'	=> 'password'
		]
	,	'email'	=> [
			'sql'	=> 'VARCHAR(50) NOT NULL'
		,	'type'	=> 'text'
		]
	];
	
	#######################################
	# MAGIC METHODS
	
	protected function __initalize() {
		
	}
	
}
