<?php
/** 
 * PYSystem
 *
 * @package PYApp
 * @author Martin Grossert <martin.grossert@gmail.com>
 * @version 0.1.0
 * @copyright Copyright (c) 2013, Martin Grossert
 * @license GNU GENERAL PUBLIC LICENSE
 */
 
namespace PY;

class system {
	# System is a Singleton
	use \Singleton;

	#######################################
	# STATIC VARS
	static $PY = [];
	
	# INTERNAL VARS
	public $database = null;
	
	
	#######################################
	# MAGIC METHODS
	
	# construct class
	protected function __initialize() {
		// TODO: prüfe ob nützlich
		if (!isset($GLOBALS['PY'])) $GLOBALS['PY'] = [];
		static::$PY = $GLOBALS['PY'];
		$GLOBALS['PY'] = &static::$PY;
		
		# only on first load
		if (!is_null(static::$instance)) return false;
		
		# url 
		static::$PY['self'] = $_SERVER["REQUEST_URI"];
		self::$PY['self'] = str_replace("index.php", "", static::$PY['self']);
		if (!defined("_SELF")) define("_SELF", static::$PY['self']);
		
		# output type
		if (!isset(static::$PY['OUTPUT_TYPE'])) {
			switch(true) {
			default: 
				static::$PY['OUTPUT_TYPE'] = "html";
			break; case (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=="XMLHttpRequest"):
				static::$PY['OUTPUT_TYPE'] = "js";
			break; case (isset($_REQUEST['py_output'])):
				static::$PY['OUTPUT_TYPE'] = strtolower($_REQUEST['py_output']);
			}
		}
		
		# register autoloader
		static::$PY['CLASSES'] = (isset(static::$PY['CLASSES']))?static::$PY['CLASSES']:[];				# class list
		spl_autoload_register(__CLASS__.'::__autoload', true, true);
		
		# templates paths , concept must be modified
		#static::$PY['TEMPLATE_PATH'][] = 'system'.DIR_SEP.'templates';									# core templates
		#static::$PY['TEMPLATES'] = (isset(static::$PY['TEMPLATES']))?static::$PY['TEMPLATES']:[];	# template list
		
		# try to load internal cache 
		# later | may be one of the last
		
		# load application
		# later + only if not cached
		
		# load modules
		# later + only if not cached
		
		# load userconfig
		# later only if not cached
		require_once(PY_ROOT.DIR_SEP.'system'.DIR_SEP.'config'.DIR_SEP.'server.php');
		
		# connect database
		$dbInit = (isset(static::$PY['database']['initalize']))?static::$PY['database']['initalize']:false;
		static::$PY['database']['initalize'] = false;
		$this->database = \database::connect(static::$PY['database']);
		if ($dbInit !== false) $this->database->query($dbInit);
		
		# load all internal dco?
		$py_user = \py_user::getInstance();
		
		# load templates
		# !concept must be modified!
		# later + only if not cached
		// if (!is_array(static::$PY['TEMPLATE_PATH'])) 
			// static::$PY['TEMPLATE_PATH'] = [];
			
		// foreach(static::$PY['TEMPLATE_PATH'] AS $path) {
			// if (substr($path, -1)==DIR_SEP) $path = substr($path, 0, -1);
			// foreach(scandir($basePath = PY_ROOT.DIR_SEP.$path) AS $dir) {
				// if (substr($dir, 1) == ".") continue;	# ignore parent
				// #	ignore files, simple templates must be registerd
				// if (!is_dir($fullPath = $basePath.DIR_SEP.$dir)) continue;
				// #	ignore folders without config
				// if (!is_file($tplCnf = $fullPath.DIR_SEP.'config.ini')) continue;
				// # read config 
				// $config = parse_ini_file($tplCnf, true);
				// $name = ((isset($config['info']['category']))?$config['info']['category'].'\\':'')
					 // .	((isset($config['info']['name']))?$config['info']['name']:'default');
				// # set or override template
				// static::$PY['TEMPLATES'][$name] = $path.DIR_SEP.$dir;
			// }
		// }
		
		# initalize system callback
		# later
		
		# load database
		# move into class
		# PDO  -> class 
		// $strDbLocation = DB_TYPE.':dbname='.DB_NAME.';host='.DB_HOST.';charset=UTF-8';
		// static::$PY['db'] = new PDO($strDbLocation, DB_USERNAME, DB_PASSWORD);
		// if (defined("DB_INITALIZE_QUERY") && DB_INITALIZE_QUERY != "") static::$PY['db']->query(DB_INITALIZE_QUERY);


	}

	###################################
	
	#	class autoload
	static function __autoload($class) {
		$PY = &static::$PY;
		$extensions = explode(",",spl_autoload_extensions());
		
		switch (true) {
		# search known paths
		default:
			$filename = (DIR_SEP!="\\")?str_replace("\\", DIR_SEP, $class):$class;
			$paths = array_merge($PY['CLASS_PATH'], $PY['MODEL_PATH'])
			if (!is_array($paths)) throw new Exception("CLASS_PATH is'nt an array!");
			foreach($paths AS $path) {
				if (substr($path, -1)==DIR_SEP) $path = substr($path, 0, -1);
				foreach($extensions AS $ext) {
					# if there is a namespace dir
					if (is_file($file=PY_ROOT.DIR_SEP.$path.DIR_SEP.$filename.$ext)) {
						include_once($file);
						if (class_exists($class, false)) return true;
					# without a namespace dir
					} elseif (is_file($file=PY_ROOT.DIR_SEP.$path.DIR_SEP.basename($filename).$ext)) {
						include_once($file);
						if (class_exists($class, false)) return true;
					}
				}
			}
		
		# if class exist
		break; case (class_exists($class, false)):
			# do nothing
		
		# search known classes
		break; case (isset($PY['CLASSES'][$class])):
			$filename = $PY['CLASSES'][$class];
			foreach($extensions AS $ext) {
				if (is_file($file=PY_ROOT.DIR_SEP.$filename.$ext)) {
					include_once($file);
					if (class_exists($class, false)) return true;
				}
			}
		}
		
		return false;
	}
	###################################
	
	#######################################
	# METHODS
	
}