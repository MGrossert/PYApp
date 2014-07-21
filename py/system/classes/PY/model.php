<?php	
/** 
 * PY Data Model
 *
 * @package PYApp
 * @author Martin Grossert <martin.grossert@gmail.com>
 * @version 0.1.0
 * @copyright Copyright (c) 2013, Martin Grossert
 * @license GNU GENERAL PUBLIC LICENSE
 */

namespace PY;

class model {
	use \Singleton;
	
	#######################################
	# TABLE DESCRIPTION
	
	protected static $name = '';
	
	# INTERNAL VARS
	protected $db = null;
	
	
	#######################################
	# MAGIC METHODS
	
	protected function __initialize() {
		switch ( strtolower(static::$type) ) {
		default:
		break; case "db":
			// $this->db = database::getInstance();
			// if (static::$name == '') static::$name = get_called_class();
			
			// # create on first use? or send a msg?
			// if (!$this->db->table(static::$name)->exist()) {
				// $this->db->table(static::$name)->fields(static::$fields)->index(static::$index)->create();
			// }
		break; case "file:
			
		}
	}
	
}