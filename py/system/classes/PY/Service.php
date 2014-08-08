<?php

namespace PY {

class Service extends ObjectList
{
	private $interface = null;
	
	function __construct ($interface)
	{
		$this->interface = $interface;
	}
	
	function register ($object, $name = false)
	{
		if ($this->interface === null)
			return false;
		
		$refl = new \ReflectionClass($object);
		if ($refl->implementsInterface($this->interface)) {
			if ( !$name) {
				$name = (($ns = $refl->getNamespaceName()) != '' ? $ns . "_" : '') . $refl->getShortName();
			}
			parent::add($name, $object);
			return true;
		}
		return false;
	}
	
	function unregister ($name)
	{
		$this->remove($name);
	}
	
}

}

?>