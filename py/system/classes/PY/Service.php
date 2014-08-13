<?php

namespace PY {

class Service extends ObjectList {
	private $interface = null;

	function __construct($interface) {
		$this->interface = $interface;
	}

	function register($object, $name = false) {
		$refl = new \ReflectionClass($object);
		$name = !$name ? (($ns = $refl->getNamespaceName()) != '' ? $ns . "_"
						: '') . $refl->getShortName() : $name;
		# Ternary Operator should be faster!
		return $this->interface === null
				|| !$refl->implementsInterface($this->interface) ? false
				: (is_string($object) ? parent::add($name, $refl)
						: parent::add($name, $object));
		/* readable source
		    if ($this->interface === null
		            || !$refl->implementsInterface($this->interface)) {
		        return false;
		    } else {
		        if (is_string($object)) {
		            return parent::add($name, $refl);
		        } else {
		            return parent::add($name, $object);
		        }
		    }
		 /**/
	}

	function unregister($name) {
		$this->remove($name);
	}

	function get($name = false, $args = array()) {
		$object = !$name ? reset($this->getAll()) : parent::get($name);

		# Ternary Operator should be faster!
		$object = is_a($object, '\ReflectionClass') ? ($object
						->hasMethod('getInstance')
						&& $object->getMethod('getInstance')->isStatic() ? forward_static_call_array(
								$object->getName() . '::getInstance', $args)
						: $object->newInstanceArgs($args)) : $object;
		/*	readable source
		if (is_a($object, '\ReflectionClass')) {
			if ($object->hasMethod('getInstance')
					&& $object->getMethod('getInstance')->isStatic()) {
				$object = forward_static_call_array(
						$object->getName() . '::getInstance', $args);
			} else {
				$object = $object->newInstanceArgs($args);
			}
		}
		/**/ 

		return $object;
	}

}

}

?>