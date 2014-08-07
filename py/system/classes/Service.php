<?php

namespace PY {
	class Service extends ObjectList {
		private $interface;

		function __construct($interface) {
			$this->$interface = $interface;
		}

	}

}


?>