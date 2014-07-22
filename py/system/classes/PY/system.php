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
	# CONSTANTS
	const CLASS_DIR = "classes";
	const MODEL_DIR = "models";
	const TRAIT_DIR = "traits";
	const TEMPLATE_DIR = "templates";
	
	const TYPE_CORE = 0;
	const TYPE_APP = 4;
	const TYPE_MODULE = 8;
	
	# STATIC VARS
	static $PY = [];
	
	# INTERNAL VARS
	public $database = null;
	
	
	#######################################
	# MAGIC METHODS
	
	# construct class
	protected function __initialize() {
		static::$PY = [];
		
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
		
		# try to load internal cache 
		# later | may be one of the last
		
		# read structure
		if (true) {				# TODO: CACHING DISABLE SCAN!
			$this->readStructure();
		}
		
		# register autoloader
		spl_autoload_register(__CLASS__.'::__autoload', true, true);
		
		# load application
		# later + only if not cached
		
		# load modules
		# later + only if not cached
		
		# load userconfig
		# later only if not cached
		// require_once(PY_ROOT.DIR_SEP.'system'.DIR_SEP.'config'.DIR_SEP.'server.php');
		
		# connect database
		$dbInit = (isset(static::$PY['database']['initalize']))?static::$PY['database']['initalize']:false;
		static::$PY['database']['initalize'] = false;
		// $this->database = \database::connect(static::$PY['database']);
		// if ($dbInit !== false) $this->database->query($dbInit);
		
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
			$paths = array_unique(array_merge($PY['CLASS_PATH'], $PY['MODEL_PATH'], $PY['TRAIT_PATH']));
			if (!is_array($paths)) {
				throw new \Exception("CLASS_PATH isn't an array!");
			}
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
		# caching mechanismus
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
	
	#	storage Management
	
	static function getStorage(string $connection_string = null) {
	
	}
	
	#######################################
	# METHODS
	
	function readStructure ($return = false) {
		if (!$return)
			$PY = &static::$PY;
		
		$PY['MODULES']			= (isset($PY['MODULES']))?$PY['MODULES']:[];				# module list
		$PY['CLASS_PATH']		= (isset($PY['CLASS_PATH']))?$PY['CLASS_PATH']:[];			# class paths
		$PY['MODEL_PATH']		= (isset($PY['MODEL_PATH']))?$PY['MODEL_PATH']:[];			# model paths
		$PY['TRAIT_PATH']		= (isset($PY['TRAIT_PATH']))?$PY['TRAIT_PATH']:[];			# trait paths
		$PY['CLASSES']			= (isset($PY['CLASSES']))?$PY['CLASSES']:[];				# class list
		$PY['TEMPLATE_PATH'][]	= (isset($PY['TEMPLATE_PATH']))?$PY['TEMPLATE_PATH']:[];	# core templates
		$PY['TEMPLATES']		= (isset($PY['TEMPLATES']))?$PY['TEMPLATES']:[];			# template list
		
		$load = [];
		foreach(scandir(PY_ROOT) AS $mod) {
			if (substr($mod, 0, 1) == ".") 
				continue;	# skip hidden & system
			if (!is_dir($path = PY_ROOT.DIR_SEP.$mod)) 
				continue;	# skip files
			if (array_search($mod, $PY['MODULES']) !== false) 
				continue;	# skip loaded
			
			if (is_file($confFile = $path.DIR_SEP."module.json")) {
				array_push($PY['MODULES'], $mod);
				# load module config
				$conf = json_decode(file_get_contents($confFile), true);
				# cache requirements
				$typeName = strtoupper(isset($conf['type'])?$conf['type']:'module');
				$type = (defined(__CLASS__ ."::TYPE_$typeName"))?constant(__CLASS__ ."::TYPE_$typeName"):static::TYPE_MODULE;
				$load[$type][$mod] = ($mod == __CLASS__)?[]:[__CLASS__];
				if (isset($conf['require'])) {
					if (is_array($conf['require'])) {
						$load[$type]['mod'] = array_merge($load[$type]['mod'], $conf['require']);
					} elseif (is_string($conf['require'])) {
						array_push($load[$type]['mod'], $conf['require']);
					}
				}
				// read registered classes
				if (isset($conf['classes']) && is_array($conf['classes'])) 
					$PY['CLASSES'] = array_merge($PY['CLASSES'], $conf['classes']);
					
				// GET DIRECTORYS
				$class_dir = isset($conf['class_dir'])?$conf['class_dir']:static::CLASS_DIR;
				if (is_dir($class_dir)) 
					array_push($PY['CLASS_PATH'], dir.DIR_SEP.$class_dir);
				$model_dir = isset($conf['model_dir'])?$conf['model_dir']:static::MODEL_DIR;
				if (is_dir($model_dir)) 
					array_push($PY['MODEL_PATH'], dir.DIR_SEP.$model_dir);
				$trait_dir = isset($conf['trait_dir'])?$conf['trait_dir']:static::TRAIT_DIR;
				if (is_dir($trait_dir)) 
					array_push($PY['TRAIT_PATH'], dir.DIR_SEP.$trait_dir);
				$template_dir = isset($conf['template_dir'])?$conf['TEMPLATE_DIR']:static::TEMPLATE_DIR;
				if (is_dir($template_dir)) 
					array_push($PY['TEMPLATE_PATH'], dir.DIR_SEP.$template_dir);
				
			}
		}
		
		$PY['LOADING']			= (isset($PY['LOADING']))?$PY['LOADING']:[];				# loading pipeline
		for ($p=0; $p < 10; $p++) {
			if (!isset($load[$p]))
				continue;	# skip unused
			
			$lastCount = PHP_INT_MAX;
			while (($c = count($load[$p]) > 0) && $c != $lastCount) {
				
				$lastCount = $c;
			}
			
		}
		
		if ($return)
			return $PY;
	}
	
}