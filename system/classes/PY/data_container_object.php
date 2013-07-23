<?php	
/** 
 * PY Data Container Object
 *
 * @package PYApp
 * @author Martin Grossert <martin.grossert@gmail.com>
 * @version 0.1.0
 * @copyright Copyright (c) 2013, Martin Grossert
 * @license GNU GENERAL PUBLIC LICENSE
 */

namespace PY;

class data_container_object {
	use \Singleton;
	
	#######################################
	# TABLE DESCRIPTION
	
	protected static $name = '';
	protected static $view = [];
	protected static $index = [];
	protected static $fields = [];
	
	# INTERNAL VARS
	protected $db = [];
	
	
	#######################################
	# MAGIC METHODS
	
	protected function __initialize() {
		$this->db = database::getInstance();
		if (static::$name == '') static::$name = get_called_class();
		
		
		# create on first use? or send a msg?
		if (!$this->db->table(static::$name)->exist()) {
			$this->db->table(static::$name)->fields(static::fields)->index(static::index)->create();
		}
		
	}
	
}