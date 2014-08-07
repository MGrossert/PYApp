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

trait Singleton
{
	#######################################
	# INTERNAL VARS
	protected static $instance = null;
	
	#######################################
	# MAGIC METHODS
	
	final private function __construct ()
	{
		static::__initialize();
	}
	
	# protect functions
	
	final private function __wakeup ()
	{
	}
	
	final private function __sleep ()
	{
	}
	
	final private function __clone ()
	{
	}
	
	#######################################
	# METHODS
	
	abstract protected function __initialize ();
	
	final public static function &getInstance ()
	{
		# create if not exist
		if (null === static::$instance)
			static::$instance = new static();
		
		#check type
		$class = get_called_class();
		if (get_class(static::$instance) == $class || is_subclass_of(static::$instance, $class, false))
			return static::$instance;
		else
			return false;
	}
	
}
 