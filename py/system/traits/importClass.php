<?php
/** 
 * ImportClass Trait
 *
 * @package Traits
 * @author Martin Grossert <martin.grossert@gmail.com>
 * @version 0.1.0
 * @copyright Copyright (c) 2013, Martin Grossert
 * @license GNU GENERAL PUBLIC LICENSE
 */
 
trait importClass {

	function importClass($class) {
		if (!class_exists($class)) 
			return false;
			
		$traits = class_uses($class);
		if (!is_array($traits)) 
			return false;
		
		$obj = null;
		if (array_search("singleton", $traits) !== false) {
			try {
				$obj = $class::getInstance();
			} catch (e) {}
		} else {
			try {
				$obj = new $class();
			} catch (e) {}
		}
		
		if (is_null($obj)) 
			return false;
		
		$refC = new \ReflectionClass($obj);
		$name = $refC->getShortName();
		$this->$name = $obj;
		
		return true;
	}

}

 