<?php

namespace PY {

class Logger implements \LoggerInterface
{
	const TYPE_INFO = 0;
	const TYPE_LOG = 1;
	const TYPE_DEBUG = 2;
	const TYPE_ASSERT = 3;
	const TYPE_WARNING = 4;
	const TYPE_EXCEPTION = 5;
	const TYPE_ERROR = 6;
	
	const OUT_UI = 1;
	const OUT_PHP = 2;
	const OUT_FILE = 4;
	const OUT_DATABASE = 8;
	const OUT_MAIL = 16;
	const OUT_CALLBACK = 32;
	
	private $defaultErrorCallback = null;
	private $defaultExceptionCallback = null;
	
	private $mailBody = "";
	private $logTypes = 0;
	private $exceptions = array();
	private $functions = array();
	
	function log ($message, $type)
	{
		
	}
	
	function error ($errNo, $errStr, $errFile, $errLine, $errContext)
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
		
		return $this->logException($exception, $errContext);
	}
	
	function exception ($exception, $errContext)
	{
	}
	
	
	
}

}
