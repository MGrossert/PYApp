<?php

namespace PY {

class ObjectList implements \Iterator {
	protected $objects = [];
	protected $position = 0;

	#######################################
	# Iterator

	function rewind() {
		$this->position = 0;
	}

	function current() {
		return $this->objects[$this->position];
	}

	function key() {
		return $this->position;
	}

	function next() {
		++$this->position;
	}

	function valid() {
		return isset($this->objects[$this->position]);
	}

	#######################################
	# FUNCTIONS

	function add($name, $obj) {
		$this->objects[$name] = $obj;
		return true;
	}

	function remove($name) {
		if (isset($this->objects[$name])) {
			unset($this->objects[$name]);
			return true;
		}
		return false;
	}

	function get($name) {
		if (!$name)
			return false;

		return isset($this->objects[$name]) ? $this->objects[$name] : false;
	}

	function has($name) {
		return isset($this->objects[$name]);
	}

	function getList() {
		return array_keys($this->objects);
	}

	function getAll() {
		return $this->objects;
	}

	function getLength() {
		count($this->objects);
	}
}

}
