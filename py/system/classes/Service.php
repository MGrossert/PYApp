<?php

namespace PY {
	class Service extends ObjectList {
		private $interface;

		function __construct($interface) {
			$this->$interface = $interface;
		}

		function register($service) {
			$refl = new ReflectionClass($service);
			#$refl->
 $idx = uniqid("hook_", true);
			parent::register($idx, $service);

		}

	}

}

?>