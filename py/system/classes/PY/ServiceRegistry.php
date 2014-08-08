<?php

namespace PY {
use \ReflectionClass;
use \ServiceRegistryInterface;

class ServiceRegistry extends ObjectList implements ServiceRegistryInterface
{
	
	function prepare ($type, $interface, $default = null)
	{
		$refl = new ReflectionClass($interface);
		if ( !$refl->isInterface())
			return false;
		
		$service = new Service($interface);
		if ($default != null) {
			$service->register($default);
		}
		$this->add($type, $service);
		return true;
	}
	
	function register ($type, $object, $name = false)
	{
		$service = $this->get($type);
		return ($service) ? $service->register($object, $name) : false;
	}
	
	function unregister ($type)
	{
		$service = $this->get($name);
		return ($service) ? $service->unregister($object) : false;
		
	}
	
	function getServiceTypeList ()
	{
		return $this->getList();
	}
	
	function getServiceList ($type)
	{
		$service = $this->get($name);
		if ($service) {
			return $service->getList();
		}
	}
	
	function get ($type, $name = false)
	{
		$service = parent::get($type);
		return !$service ? false : ( !$name ? reset($service->getAll()) : $service->get($name));
	}
	
	function getServiceType ($type)
	{
		return parent::get($type);
	}
	
}

}
