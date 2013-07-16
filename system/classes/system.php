<?php
/**
 * PYSystem
 *
 * @package PYApp
 * @author Martin Grossert <martin.grossert@gmail.com>
 * @version 0.1.0
 * @copyright Copyright (c) 2013, Martin Grossert
 * @license http://www.opensource.org/licenses/mit-license.php
 */
 
namespace PY;

class system {

	#######################################
	# INTERNAL VARS
	static private $PY = array();
	static private $instance = null;

	#######################################
	# MAGIC METHODS
	
	# construct class
	private function __construct() {
		# initalize globals
		self::$PY = &$GLOBALS['PY'];
		
		# paths
		self::$PY['CLASSES'][__CLASS__] = str_replace(PY_PATH.DIRECTORY_SEPARATOR, '', __FILE__);
		self::$PY['CLASSES_PATH'][] = str_replace(PY_PATH.DIRECTORY_SEPARATOR, '', dirname(__FILE__));
		
		# autoload
		spl_autoload_register(__CLASS__.'::__autoload', true, true);
		
		# url 
		self::$PY['SELF'] = $_SERVER['PHP_SELF'].((isset($_SERVER['QUERY_STRING'])&&$_SERVER['QUERY_STRING']!="")?"?".$_SERVER['QUERY_STRING']:"");
		self::$PY['SELF'] = str_replace("index.php", "", self::$PY['SELF']);
		
		# output type
		if (!isset(self::$PY['OUTPUT_TYPE'])) {
			switch(true) {
			default: 
				self::$PY['OUTPUT_TYPE'] = "HTML";
			break; case (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=="XMLHttpRequest"):
				self::$PY['OUTPUT_TYPE'] = "JS";
			break; case (isset($_REQUEST['py_output'])):
				self::$PY['OUTPUT_TYPE'] = strtoupper($_REQUEST['py_output']);
			}
		}

		
		# PDO  -> class 
		// global self::$PY_DB;
		// $strDbLocation = DB_TYPE.':dbname='.DB_NAME.';host='.DB_HOST.';charset=UTF-8';
		// self::$PY_DB = new PDO($strDbLocation, DB_USERNAME, DB_PASSWORD);
		// if (defined("DB_INITALIZE_QUERY") && DB_INITALIZE_QUERY != "") self::$PY_DB->query(DB_INITALIZE_QUERY);


	}
	
	#	clone class
    private function __clone(){}
	
	#	class autoload
	static function __autoload($class) {
		# search known classes
		if (isset($GLOBALS['PY']['CLASSES'][$class]))
			require_once($GLOBALS['PY']['CLASSES'][$class]);
		
		# search known paths
		if (DIRECTORY_SEPARATOR!="\\") $file = str_replace("\\", DIRECTORY_SEPARATOR, $class);
		$extensions = explode(",",spl_autoload_extensions());
		foreach($GLOBALS['PY']['CLASSES_PATH'] AS $path) {
			if (substr($path, -1)==DIRECTORY_SEPARATOR) $path = substr($path, 0, -1);
			foreach($extensions AS $ext) {
				
			}
		}
		
		die ("autoload: $class");
	}
	
	# get singleton instance
	static function getInstance() {
		if (null === self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}
 
	#######################################
	# METHODS
	function initalizeFrontend() {
		print "<pre>";
		
		
		$test = new \PY\template();
		
		
		
		
	}
}