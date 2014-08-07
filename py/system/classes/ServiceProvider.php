<?php

namespace PY {
	use \ReflectionClass;

	class ServiceProvider extends ObjectList {


		function register ($name, $interface) {
			$refl = new ReflectionClass($interface);
			if ( !$refl->isInterface())
				continue;
				
				$service = new Service($interface);
				$this->register($name, $service);

			#
		}


		function get ($service) {
		}
	}

}