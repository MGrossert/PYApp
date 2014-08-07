<?php

namespace PY {

include_once('system' . DIR_SEP . 'interfaces' . DIR_SEP . 'SingletonInterface.php'); # singleton trait
use \SingletonInterface;
include_once('system' . DIR_SEP . 'traits' . DIR_SEP . 'Singleton.php'); # singleton trait
use \Singleton;

/**
 * PYSystem
 *
 * @package PYApp
 * @author Martin Grossert <martin.grossert@gmail.com>
 * @version 0.1.0
 * @copyright Copyright (c) 2013, Martin Grossert
 * @license GNU GENERAL PUBLIC LICENSE
 */
class System implements SingletonInterface
{
	# System is a Singleton
	use Singleton;
	
	#######################################
	# CONSTANTS
	const SYSTEM = "system";
	
	const DIR_CLASS = "classes";
	const DIR_INTERFACE = "interfaces";
	const DIR_MODEL = "models";
	const DIR_TRAIT = "traits";
	const DIR_TEMPLATE = "templates";
	
	const TYPE_CORE = 0;
	const TYPE_FRAMEWORK = 1;
	const TYPE_ELEMENT = 2;
	const TYPE_MODULE = 3;
	const TYPE_APP = 4;
	
	# STATIC VARS
	private static $PY = [];
	private static $TYPE_COUNT = null;
	var $provider = null;
	
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
		
		if (static::$TYPE_COUNT == null) {
			$reflection = new \ReflectionClass(get_class());
			static::$TYPE_COUNT = count(preg_grep_keys("/^TYPE_/i", $reflection->getConstants()));
		}
		
		# try to load internal structure cache
		# TODO: later | may be one of the last
		
		# read structure, if not cached
		if ( !isset($PY['MODULES'])) {
			$this->readStructure();
		}
		
		# register autoloader
		spl_autoload_register(__CLASS__ . '::__autoload', true, true);
		
		$this->provider = new ServiceProvider();
	}
	
	function initialize ($mode = null)
	{
		$PY = &static::$PY;
		$PY['MESSAGE'] = (isset($PY['MESSAGE'])) ? $PY['MESSAGE'] : []; # message list
		$system = $this;
		define("PY_HOOK_INITIALIZED", "py-initialized");
		HookProvider::getInstance()->register(PY_HOOK_INITIALIZED);
		foreach ($PY['LOADING'] AS $module) {
			$mod = isset($PY['MODULES'][$module]) ? $PY['MODULES'][$module] : [];
			if ( !is_dir($path = PY_ROOT . DIR_SEP . $module))
				continue;
			
			if (is_file($initFile = $path . DIR_SEP . "initialize.php")) {
				ob_start();
				require $initFile;
				$ret = ob_get_clean();
				if ( !empty($ret))
					$PY['MESSAGE'][] = $ret;
			}
		}
		HookProvider::getInstance()->call(PY_HOOK_INITIALIZED);
	}
	
	#######################################
	# SERVICE PROVIDER
	
	#######################################
	# STRUCTURE
	
	function readStructure ($return = false)
	{
		if ( !$return)
			$PY = &static::$PY;
		
		$PY['MODULES'] = (isset($PY['MODULES'])) ? $PY['MODULES'] : []; # module list
		$PY['LOADING'] = (isset($PY['LOADING'])) ? $PY['LOADING'] : []; # loading pipeline
		$class_types = array(
		    'CLASS',
		    'INTERFACE',
		    'MODEL',
		    'TRAIT'
		);
		foreach ($class_types AS $type) {
			$PY["{$type}_PATH"] = (isset($PY["{$type}_PATH"])) ? $PY["{$type}_PATH"] : []; # class paths
			$PY[$type] = (isset($PY[$type])) ? $PY[$type] : []; # class list
		}
		$PY['TEMPLATE_PATH'] = (isset($PY['TEMPLATE_PATH'])) ? $PY['TEMPLATE_PATH'] : []; # core templates
		$PY['TEMPLATES'] = (isset($PY['TEMPLATES'])) ? $PY['TEMPLATES'] : []; # template list
		
		$load = array_fill(0, static::$TYPE_COUNT + 1, []);
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
				$load[$type][$mod] = $mod == static::SYSTEM ? [] :
				    [static::SYSTEM];
				if (isset($conf['require'])) {
					if (is_array($conf['require'])) {
						$load[$type][$mod] = array_merge($load[$type][$mod], $conf['require']);
					} elseif (is_string($conf['require'])) {
						array_push($load[$type][$mod], $conf['require']);
					}
				}
				
				// read registered classes
				foreach ($class_types AS $type) {
					if (isset($conf[$type]) && is_array($conf[$type]))
						$PY[$type] = array_merge($PY[$type], $conf[$type]);
				}
				
				// GET DIRECTORYS
				foreach ($class_types AS $type) {
					$dir = isset($conf[$cnf_name = (strtolower($type) . "_dir")]) ? $conf[$cnf_name] : constant("static::DIR_{$type}");
					if (is_dir(PY_ROOT . DIR_SEP . $mod . DIR_SEP . $dir))
						array_push($PY["{$type}_PATH"], $mod . DIR_SEP . $dir);
				}
				$template_dir = isset($conf['template_dir']) ? $conf['TEMPLATE_DIR'] : static::DIR_TEMPLATE;
				if (is_dir(PY_ROOT . DIR_SEP . $mod . DIR_SEP . $template_dir))
					array_push($PY['TEMPLATE_PATH'], $mod . DIR_SEP . $template_dir);
				
			}
		}
		
		$extensions = explode(",", spl_autoload_extensions());
		foreach ($class_types AS $type) {
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
		
		for ($p = 0; $p < static::$TYPE_COUNT; $p++ ) {
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

}
