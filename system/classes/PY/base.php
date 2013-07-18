<?php
/** 
 * PYObjectBase
 *
 * @package PYApp
 * @author Martin Grossert <martin.grossert@gmail.com>
 * @version 0.1.0
 * @copyright Copyright (c) 2013, Martin Grossert
 * @license GNU GENERAL PUBLIC LICENSE
 */

namespace PY;

trait base {
	#######################################
	# INTERNAL VARS
	protected $PY = array();

	#######################################
	# MAGIC METHODS
	
	# construct class
	public function construct() {
		# initalize globals
		$this->PY = &$GLOBALS['PY'];
	
	}

	#######################################
	# METHODS
	
}