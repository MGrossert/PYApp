<?php

/**
 * Singleton Trait
 *
 * @package Traits
 * @author Martin Grossert <martin.grossert@gmail.com>
 * @version 0.1.0
 * @copyright Copyright (c) 2013, Martin Grossert
 * @license GNU GENERAL PUBLIC LICENSE
 */

trait Multiton
{
	#######################################
	# INTERNAL VARS
	protected static $instances = array();
	
	#######################################
	# METHODS
	
	public static function &getInstance ($key)
	{
		# create if not exist
		if (isset(static::$instances[$key])) {
			$rc = new ReflectionClass(get_called_class());
			self::$instances[$key] = $rc->newInstanceArgs(func_get_args());
		}
		
		#check type
		$class = get_called_class();
		if (get_class(static::$instances[$key]) == $class || is_subclass_of(static::$instances[$key], $class, false))
			return static::$instances[$key];
		else
			return false;
	}
	
}
 