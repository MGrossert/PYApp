<?php	
/** 
 * PYTemplate
 *
 * @package PYApp
 * @author Martin Grossert <martin.grossert@gmail.com>
 * @version 0.1.0
 * @copyright Copyright (c) 2013, Martin Grossert
 * @license GNU GENERAL PUBLIC LICENSE
 */
 
namespace PY;

class template {

	#######################################
	# INTERNAL VARS
	
	# object
	protected $type = false;
	protected $data = array();

	#######################################
	# MAGIC METHODS
	
	# construct class
	public function __construct($p) {
		$this->type = system::$PY['type'];
		
		switch(true) {
		default: # nothing
			
		break; case (is_string($p)): # name
			$this->selectTemplate($p);
			
		break; case (is_string($array)): # config array
			if (isset($p['name']))
				$this->selectTemplate($p['name']);
			
		}
	}
	
	# template setter & getter 
	public function __set($name,$value)  { 
		$data[$name] = $value;
	} 
	
	public function &__get($name) { 
		return (isset($data[$name]))?$data[$name]:'';
	} 
	
	public function __isset($name) { 
		return isset($data[$name]);
	} 
	
	#######################################
	# METHODS
	
	# select Template By Name
	function selectTemplate($name) {
		if (isset(system::$PY['TEMPLATES'][$name])){
			$filename = system::$PY['TEMPLATES'][$name];
			foreach($extensions AS $ext) {
				if (is_file($file=PY_PATH.DIR_SEP.$filename.$ext)) {
					
					
				}
			}
		}
		return false;
	}
	
	function parse($print = false) {
		
		
		
		
		
	}
	
}