<?php

namespace PY;

class TemplateEngine implements \TemplateEngineInterface
{
	private $_system = null;
	private $_view = null;
	private $_input = array();
	private $_output = null;
	
	#######################################
	# MAGIC METHODS
	
	function __construct ($view = null)
	{
		if ( !empty($view)) {
			$this->setView($view);
		}
		$this->_system = System::getInstance();
	}
	
	function __set ($name, $value)
	{
		$this->setParam($name, $value);
	}
	
	function __get ($name)
	{
		return $this->getParam($name);
	}
	
	#######################################
	# FUNCTIONS
	
	function setParam ($name, $value)
	{
		$this->_input[$name] = $value;
	}
	
	function getParam ($name)
	{
		return $this->_input[$name];
	}
	
	function setView ($view)
	{
		if (empty($view)) {
			new \Exception("Empty Template");
		}
		$this->_view = $view;
	}
	
	function getResult ()
	{
		if ($this->_output == null) {
			$this->processTemplate();
		}
		return $this->_output;
	}
	
	private function processTemplate ()
	{
		if (empty($this->_view)) {
			new \Exception("Empty Template");
		}
		$this->_output = "";
		$__INTPUT = $this->_input;
		$__TEMPLATE = $this->_system->getTemplate($this->_view);
		if ( !$__TEMPLATE) {
			return;
		}
		
		ob_start();
		call_user_func_array(function () use ($__TEMPLATE, $__INTPUT)
		{
			extract($__INTPUT);
			include($__TEMPLATE);
		}, array());
		$this->_output = ob_get_clean();
	}
	
}
