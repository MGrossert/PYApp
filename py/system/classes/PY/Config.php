<?php

/**
 * PYConfig
 *
 * @package PYApp
 * @author Martin Grossert <martin.grossert@gmail.com>
 * @version 0.1.0
 * @copyright Copyright (c) 2013, Martin Grossert
 * @license GNU GENERAL PUBLIC LICENSE
 */

class Config
{
	# config is a Singleton
	use \Singleton;
	
	function __initialize ()
	{
		
		foreach (system::$PY['MODULES'] AS $module => $config) {
		}
		
	}
	
}
