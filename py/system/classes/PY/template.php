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
	protected $path = false;
	protected $data = array();
	
	# config will be come later after the base backend
	protected $config_defintion = array();
	protected $config = array();

	#######################################
	# MAGIC METHODS
	
	# construct class
	public function __construct($p) {
		$this->type = ".".system::$PY['OUTPUT_TYPE'];
		
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
			if (is_file($cnfFile=PY_PATH.DIR_SEP."config.ini")) {
				
			} else {
				
			}
		}
		return false;
	}
	
	# select Template By Name
	public function switchType($type) {
		$type = strtolower($type);
		switch($type) {
		default:
		break; case "js": $type = "json";
		}
		$this->type = ".$type";
	}
	
	# get or set the data as array
	public function data($arr = null) {
		if (is_assoc($arr, true))
			$this->data = $arr;
		else 
			return $this->data;
	}
	
	# merge a array with the data
	public function merge($arr = null) {
		if (is_assoc($arr, true))
			$this->data = array_merge_recursive($this->data, $arr);
	}
	
	# render the template
	protected static function render ($template) {
		# unpack data
		extract($template->data, EXTR_OVERWRITE);
		$config = $template->config;
		
		@include(PY_ROOT.$template->$path.$template->type);
		
	}
	
	# build the template
	public function build($print = false) {
		
		
		static::render($this);
		
		
	}
	
}