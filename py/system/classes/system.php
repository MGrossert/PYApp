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

class system
{
	# System is a Singleton
	use \Singleton;
	
	#######################################
	# CONSTANTS
	const SYSTEM = "system";
	
	const CLASS_DIR = "classes";
	const MODEL_DIR = "models";
	const TRAIT_DIR = "traits";
	const TEMPLATE_DIR = "templates";
	
	const TYPES = 4;
	const TYPE_CORE = 0;
	const TYPE_ELEMENT = 1;
	const TYPE_APP = 2;
	const TYPE_MODULE = 3;
	
	# STATIC VARS
	static $PY = [];
	
	# INTERNAL VARS
	public $storage = null;
	public $config = null;
	
	###################################
	# AUTOLOAD
	
	static function __autoload ($class)
	{
		$PY = &static::$PY;
		$extensions = explode(",", spl_autoload_extensions());
		
		if (class_exists($class, false))
			return true;
		
		$classes = array_unique(array_merge($PY['CLASS'], $PY['MODEL'], $PY['TRAIT']));
		if (isset($classes[$class])) {
			$file = PY_ROOT . DIR_SEP . $classes[$class];
			require($file);
			if (class_exists($class, false))
				return true;
		}
		
		$filename = (DIR_SEP != "\\") ? str_replace("\\", DIR_SEP, $class) : $class;
		$paths = array_unique(array_merge($PY['CLASS_PATH'], $PY['MODEL_PATH'], $PY['TRAIT_PATH']));
		foreach ($paths AS $path) {
			if (substr($path, -1) == DIR_SEP)
				$path = substr($path, 0, -1);
			foreach ($extensions AS $ext) {
				# if there is a namespace dir
				if (is_file($file = PY_ROOT . DIR_SEP . $path . DIR_SEP . $filename . $ext)) {
					require($file);
					if (class_exists($class, false))
						return true;
					# without a namespace dir
				} elseif (is_file($file = PY_ROOT . DIR_SEP . $path . DIR_SEP . basename($filename) . $ext)) {
					require($file);
					if (class_exists($class, false))
						return true;
				}
			}
		}
		
	}
	
	#######################################
	# MAGIC METHODS
	
	# construct class
	
	protected function __initialize ()
	{
		$PY = &static::$PY;
		
		# only on first load
		ob_start();
		if ( !is_null(static::$instance))
			return false;
		
		# url
		$PY['SELF'] = str_replace("index.php", "", $_SERVER["REQUEST_URI"]);
		if ( !defined("_SELF"))
			define("_SELF", $PY['SELF']);
		
		# output type
		if ( !isset($PY['OUTPUT_TYPE'])) {
			switch (true) {
				default:
					$PY['OUTPUT_TYPE'] = "html";
					break;
				case (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest"):
					$PY['OUTPUT_TYPE'] = "js";
					break;
				case (false):
					$PY['OUTPUT_TYPE'] = "xml";
					break;
				case (isset($_REQUEST['py_output_type'])):
					$PY['OUTPUT_TYPE'] = strtolower($_REQUEST['py_output_type']);
			}
		}
		
		# try to load internal structure cache
		# TODO: later | may be one of the last
		
		# read structure, if not cached
		if ( !isset($PY['MODULES'])) {
			$this->readStructure();
		}
		
		# register autoloader
		spl_autoload_register(__CLASS__ . '::__autoload', true, true);
		
	}
	
	#######################################
	# METHODS
	
	function initialize ($mode = null)
	{
		$PY = &static::$PY;
		$PY['MESSAGE'] = (isset($PY['MESSAGE'])) ? $PY['MESSAGE'] : []; # hook list
		
		# load config
		# TODO: cache
		$system = $this;
		$this->config = config::getInstance();
		
		foreach ($PY['LOADING'] AS $module) {
			$mod = isset($PY['MODULES'][$module]) ? $PY['MODULES'][$module] : [];
			if ( !is_dir($path = PY_ROOT . DIR_SEP . $module))
				continue;
			
			if (is_file($initFile = $path . DIR_SEP . "initialize.php")) {
				ob_start();
				require $initFile;
				$ret = ob_get_clean();
				if (!empty($ret)) 
					$PY['MESSAGE'][] = $ret;
			}
		}
		
	}
	
	#######################################
	# HOOKS
	
	function registerHook ($hook, $desc = "", $vars = array())
	{
		$PY = &static::$PY;
		$PY['HOOKS'] = (isset($PY['HOOKS'])) ? $PY['HOOKS'] : []; # hook list
		$HOOKS = &static::$PY['HOOKS'];
		$HOOKS[$hook] = array(
		    "hook" => $hook,
		    "desc" => $desc,
		    "vars" => $vars,
		    "func" => array()
		);
	}
	
	function listHook ()
	{
		$HOOKS = &static::$PY['HOOKS'];
		$hookList = array();
		foreach ($HOOKS AS $name => $hook) {
			$hookList[$name] = $hook["desc"];
		}
		return $hookList;
	}
	
	function getHookParam ($hook)
	{
		if (isset($HOOKS[$hook])) {
			return $HOOKS[$hook]["vars"];
		}
		return;
	}
	
	function registerHookCallback ($hook, $func)
	{
		$HOOKS = &static::$PY['HOOKS'];
		if ( !is_callable(func))
			return false;
		
		if (isset($HOOKS[$hook])) {
			return (array_push($HOOKS[$hook]["func"], $func) === 1);
		}
		return false;
	}
	
	function callHook ($hook, $vars = array())
	{
		$HOOKS = &static::$PY['HOOKS'];
		$ret = array();
		if (isset($HOOKS[$hook])) {
			foreach ($HOOKS[$hook]["func"] as $idx => $func) {
				$ret[] = call_user_func_array($func, $vars);
			}
		}
		return $ret;
	}
	
	#######################################
	# STRUCTURE
	
	function readStructure ($return = false)
	{
		if ( !$return)
			$PY = &static::$PY;
		
		$PY['MODULES'] = (isset($PY['MODULES'])) ? $PY['MODULES'] : []; # module list
		$PY['LOADING'] = (isset($PY['LOADING'])) ? $PY['LOADING'] : []; # loading pipeline
		$types = array(
		    'CLASS',
		    'MODEL',
		    'TRAIT'
		);
		foreach ($types AS $type) {
			$PY["{$type}_PATH"] = (isset($PY["{$type}_PATH"])) ? $PY["{$type}_PATH"] : []; # class paths
			$PY[$type] = (isset($PY[$type])) ? $PY[$type] : []; # class list
		}
		$PY['TEMPLATE_PATH'] = (isset($PY['TEMPLATE_PATH'])) ? $PY['TEMPLATE_PATH'] : []; # core templates
		$PY['TEMPLATES'] = (isset($PY['TEMPLATES'])) ? $PY['TEMPLATES'] : []; # template list
		
		$load = array_fill(0, static::TYPES + 1, []);
		foreach (scandir(PY_ROOT) AS $mod) {
			if (substr($mod, 0, 1) == ".")
				continue;
			# skip hidden & system
			if ( !is_dir($path = PY_ROOT . DIR_SEP . $mod))
				continue;
			# skip files
			if (isset($PY['MODULES'][$mod]))
				continue;
			# skip loaded
			
			if (is_file($confFile = $path . DIR_SEP . "module.json")) {
				# load module config
				$PY['MODULES'][$mod] = $conf = json_decode(file_get_contents($confFile), true);
				# cache requirements
				$typeName = strtoupper(isset($conf['type']) ? $conf['type'] : 'module');
				$type = (defined(__CLASS__ . "::TYPE_$typeName")) ? constant(__CLASS__ . "::TYPE_$typeName") : static::TYPE_MODULE;
				$load[$type][$mod] = ($mod == static::SYSTEM) ? [] :
				    [static::SYSTEM];
				if (isset($conf['require'])) {
					if (is_array($conf['require'])) {
						$load[$type]['mod'] = array_merge($load[$type]['mod'], $conf['require']);
					} elseif (is_string($conf['require'])) {
						array_push($load[$type]['mod'], $conf['require']);
					}
				}
				
				// read registered classes
				foreach ($types AS $type) {
					if (isset($conf[$type]) && is_array($conf[$type]))
						$PY[$type] = array_merge($PY[$type], $conf[$type]);
				}
				
				// GET DIRECTORYS
				foreach ($types AS $type) {
					$dir = isset($conf[$cnf_name = (strtolower($type) . "_dir")]) ? $conf[$cnf_name] : constant("static::{$type}_DIR");
					if (is_dir(PY_ROOT . DIR_SEP . $mod . DIR_SEP . $dir))
						array_push($PY["{$type}_PATH"], $mod . DIR_SEP . $dir);
				}
				$template_dir = isset($conf['template_dir']) ? $conf['TEMPLATE_DIR'] : static::TEMPLATE_DIR;
				if (is_dir(PY_ROOT . DIR_SEP . $mod . DIR_SEP . $template_dir))
					array_push($PY['TEMPLATE_PATH'], $mod . DIR_SEP . $template_dir);
				
			}
		}
		
		$extensions = explode(",", spl_autoload_extensions());
		foreach ($types AS $type) {
			foreach ($PY["{$type}_PATH"] AS $path) {
				if (substr($path, -1) == DIR_SEP)
					$path = substr($path, 0, -1);
				foreach (scandir(PY_ROOT . DIR_SEP . $path) AS $file) {
					if (array_search("." . pathinfo($file, PATHINFO_EXTENSION), $extensions) !== false) {
						$base = substr($file, 0, strpos($file, "."));
						$filePath = $path . DIR_SEP . $file;
						if ( !isset($PY[$type][$base])) {
							$PY[$type][$base] = $filePath;
						}
					}
				}
			}
		}
		
		for ($p = 0; $p < static::TYPES; $p++ ) {
			if ( !isset($load[$p]))
				continue;
			# skip unused
			
			$lastCount = PHP_INT_MAX;
			while (($c = count($load[$p])) > 0 && $c != $lastCount) {
				$lastCount = $c;
				foreach ($load[$p] AS $mod => $req) {
					$loading = true;
					foreach ($req AS $idx => $reqMod) {
						if ($mod == $reqMod)
							continue;
						if (array_search($reqMod, $PY['LOADING']) === false)
							$loading = false;
					}
					if ($loading) {
						array_push($PY['LOADING'], $mod);
						unset($load[$p][$mod]);
					}
				}
			}
			
			// move unused to next stage
			$load[$p + 1] = array_merge($load[$p + 1], $load[$p]);
			unset($load[$p]);
		}
		
		if ($return)
			return $PY;
	}
	
}
