<?php

class log
{
	const LOG_FILE_FOLDER = "../log";
	
	const LOGTYPE_UI = 1;
	const LOGTYPE_PHP = 2;
	const LOGTYPE_FILE = 4;
	const LOGTYPE_DATABASE = 8;
	const LOGTYPE_MAIL = 16;
	const LOGTYPE_OLD_CALLBACK = 32;
	
	private $defaultErrorCallback = null;
	private $defaultExceptionCallback = null;
	
	private $mailBody = "";
	private $logTypes = 0;
	private $exceptions = array();
	private $functions = array();
	
	/**
	 *
	 * @param string $ui
	 * @param string $level
	 * @param string $logtypes
	 */
	function __construct ($ui = true, $level = false, $logtypes = false)
	{
		parent::__construct();
		
		$this->logTypes = ($ui ? static::LOGTYPE_UI : 0) + static::LOGTYPE_PHP + static::LOGTYPE_FILE + static::LOGTYPE_MAIL;
		
		/*
		 * SET ERROR HANDLER, IF NOT SET
		 */
		if (get_class($this->defaultErrorCallback[0]) != get_class($this)) {
			$func = array(
			    $this,
			    "onError"
			);
			$this->defaultErrorCallback = set_error_handler($func);
			
			$func = array(
			    $this,
			    "onException"
			);
			$this->defaultExceptionCallback = set_exception_handler($func);
		}
		
	}
	
	function __destruct ()
	{
		if ($this->logTypes | static::LOGTYPE_MAIL) {
			// TODO: sendMail
		}
	}
	
	/**
	 *
	 * @param int $errNo
	 * @param string $errStr
	 * @param string $errFile
	 * @param int $errLine
	 * @param array $errContext
	 */
	
	function onError ($errNo, $errStr, $errFile, $errLine, $errContext)
	{
		$exit = false;
		switch ($errno) {
			case E_USER_ERROR:
				$type = 'Fatal Error';
				$exit = true;
				break;
			case E_USER_WARNING:
			case E_WARNING:
				$type = 'Warning';
				break;
			case E_USER_NOTICE:
			case E_NOTICE:
			case @E_STRICT:
				$type = 'Notice';
				break;
			case @E_RECOVERABLE_ERROR:
				$type = 'Catchable';
				break;
			default:
				$type = 'Unknown Error';
				break;
		}
		$obj = new ErrorException($type . ': ' . $errStr, 0, $errNo, $errFile, $errLine);
		
		$return = $this->processException($exception, $errContext);
		if ($exit)
			exit($obj->getMessage());
		return $return;
	}
	
	/**
	 *
	 * @param exception $exception
	 */
	
	function onException ($exception)
	{
		return $this->processException($exception);
	}
	
	/**
	 *
	 * @param exception $exception
	 * @param array $context
	 * @return mixed
	 */
	
	private function processException ($exception, $context = array())
	{
		array_push(array_push($this->exceptions, $exception));
		
		$return = true;
		foreach (str_split(decbin($this->logTypes), 1) AS $key => $val) {
			$continue = true;
			$flag = pow(2, $key);
			if ($val == 1 && isset($this->functions[$flag]) && is_array($this->functions[$flag])) {
				foreach ($this->functions[$flag] AS $key => $func) {
					$continue &= function () use ($func, $exception)
					{
						return call_user_func_array($func, $exception);
					};
				}
			}
			
			switch ($flag) {
				case static::LOGTYPE_UI:
					break;
				
				case static::LOGTYPE_PHP:
					$return = false;
					break;
				
				case static::LOGTYPE_FILE:
					break;
				
				case static::LOGTYPE_DATABASE:
					break;
				
				case static::LOGTYPE_MAIL:
					break;
				
				case static::LOGTYPE_OLD_CALLBACK:
					if (get_class($exception) == "ErrorException") {
						$this->call($this->defaultErrorCallback, $exception->getSeverity(), $exception->getMessage(), $exception->getFile(), $exception->getLine());
					} else {
						$this->call($this->defaultExceptionCallback, $exception);
					}
					break;
			}
		}
	}
	
	/**
	 *
	 * @param callable $aCallable
	 * @return mixed
	 */
	
	private function call ($aCallable)
	{
		if (is_callable($aCallable, false, $callable_name)) {
			return function () use ($aCallable)
			{
				return call_user_func_array($aCallable, func_get_args());
			};
		}
	}
	
	/**
	 *
	 * @return multitype:
	 */
	function getLastErrors ()
	{
		return $this->exceptions;
	}
	
	/**
	 *
	 * @param function $function
	 */
	function addLogFunction ($type, $function)
	{
		if ( !isset($this->functions[$type]))
			$this->functions[$type] = array();
		$this->functions[$type][] = $function;
	}
	
	/**
	 *
	 * @param string $text
	 * @param number $no
	 */
	function log ($text, $no = 0)
	{
		
	}
	
}
