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
	# INTERNAL VARS
	
	protected static $view = [];
	protected static $fields = [];
	
	
	
	#######################################
	# MAGIC METHODS
	
	protected function __initialize() {
		
	}
	
}