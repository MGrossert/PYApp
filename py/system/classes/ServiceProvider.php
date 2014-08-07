<?php

namespace PY {

use \ReflectionClass;

class ServiceProvider extends ObjectList
{
	var $service;
	
	function register ($service, $interface)
	{
		$refl = new ReflectionClass($interface);
		if ( !$refl->isInterface())
			continue;
		
		#
	}
	
	
	function get ($set) {
		
	}
}

}
