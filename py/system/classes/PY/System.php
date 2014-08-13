<?php

namespace PY {

include_once(PY_ROOT . DIR_SEP . 'system' . DIR_SEP . 'interfaces' . DIR_SEP . 'SystemInterface.php'); # System Interface
include_once(PY_ROOT . DIR_SEP . 'system' . DIR_SEP . 'traits' . DIR_SEP . 'Singleton.php'); # singleton trait
use \SystemInterface;
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
class System implements \SystemInterface
{
	# System is a Singleton
	use Singleton {
		getInstance as protected __getInstance;
		resetInstance as protected;
	}
	
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
	private $PY = [];
	private $TYPE_COUNT = null;
	private $CLASS_TYPES = array(
	    'CLASS',
	    'INTERFACE',
	    'MODEL',
	    'TRAIT'
	);
	private $OUTPUT_TYPES = array(
	    "php",
	    "html",
	    "html5",
	    "json",
	);
	private $autoload = null;
	private $service = null;
	private $initialized = false;
	
	###################################
	#
	
	/**
	 * returns the system object
	 *
	 * @return \SystemInterface
	 */
	
	static function getInstance ()
	{
		return static::__getInstance();
	}
	
	###################################
	# AUTOLOAD
	
	function __autoload ($class)
	{
		$PY = &$this->PY;
		$extensions = explode(",", spl_autoload_extensions());
		
		if (class_exists($class, false) || interface_exists($class, false))
			return true;
		
		$classes = $paths = [];
		foreach ($this->CLASS_TYPES as $idx => $type) {
			$classes = array_merge($classes, $PY[$type]);
			$paths = array_merge($paths, $PY[$type . '_PATH']);
		}
		
		$classes = array_unique($classes);
		if (isset($classes[$class])) {
			$file = PY_ROOT . DIR_SEP . $classes[$class];
			require($file);
			if (class_exists($class, false) || interface_exists($class, false))
				return true;
		}
		
		$filename = (DIR_SEP != "\\") ? str_replace("\\", DIR_SEP, $class) : $class;
		$paths = array_unique($paths);
		foreach ($paths AS $path) {
			if (substr($path, -1) == DIR_SEP)
				$path = substr($path, 0, -1);
			foreach ($extensions AS $ext) {
				# if there is a namespace dir
				if (is_file($file = PY_ROOT . DIR_SEP . $path . DIR_SEP . $filename . $ext)) {
					require($file);
					if (class_exists($class, false) || interface_exists($class, false))
						return true;
					# without a namespace dir
				} elseif (is_file($file = PY_ROOT . DIR_SEP . $path . DIR_SEP . basename($filename) . $ext)) {
					require($file);
					if (class_exists($class, false) || interface_exists($class, false))
						return true;
				}
			}
		}
	}
	
	#######################################
	# MAGIC METHODS
	
	function __set_state ($export)
	{
		return $this->PY;
	}
	
	function __toString ()
	{
		return var_export($this->PY, true);
	}
	
	# de/construct class
	
	function __destruct ()
	{
		
		spl_autoload_unregister($this->autoload);
		
	}
	
	protected function __initialize ()
	{
		$PY = &$this->PY;
		
		$reflection = new \ReflectionClass(get_class());
		$this->TYPE_COUNT = count(preg_grep_keys("/^TYPE_/i", $reflection->getConstants()));
		
		# try to load internal structure cache
		# TODO: later | may be one of the last
		
		# read structure, if not cached
		if ( !isset($PY['MODULES'])) {
			$this->readStructure();
		}
		
		# register autoloader
		$this->autoload = array(
		    $this,
		    '__autoload'
		);
		spl_autoload_register($this->autoload, true, true);
		
		# setup service registry
		$this->service = new ServiceRegistry();
		
		# setup hooks
		$this->service()->prepare("hook", '\HookRegistryInterface', new HookRegistry());
		
		# setup initialized hook
		define("PY_HOOK_INITIALIZED", "py-initialized");
		$this->service()->get('hook')->prepare(PY_HOOK_INITIALIZED);
	}
	
	function initialize ()
	{
		if ($this->initialized)
			return false;
		
		# TODO: capsule ?
		$PY = &$this->PY;
		$this->PY['MESSAGE'] = (isset($PY['MESSAGE'])) ? $PY['MESSAGE'] : []; # message list
		
		# initialize all modules
		$system = System::getInstance();
		foreach ($PY['LOADING'] AS $module) {
			$mod = isset($PY['MODULES'][$module]) ? $PY['MODULES'][$module] : [];
			if ( !is_dir($path = PY_ROOT . DIR_SEP . $module))
				continue;
			
			if (is_file($initFile = $path . DIR_SEP . "initialize.php")) {
// 				ob_start();
				require $initFile;
// 				$ret = ob_get_clean();
				if ( !empty($ret))
					$PY['MESSAGE'][] = $ret;
			}
		}
		$system->service()->get('hook')->call(PY_HOOK_INITIALIZED);
		
		$this->initialized = true;
		return true;
	}
	
	#######################################
	# SERVICE PROVIDER
	
	function service ()
	{
		return $this->service;
	}
	
	#######################################
	# TEMPLATE PROVIDER

	function getTemplate ($view)
	{
		if (empty($view)) {
			return false;
		}
		
		if ( !isset($this->PY['TEMPLATES'][$view])) {
			return false;
		}
		
		$path = $this->PY['TEMPLATES'][$view];
		foreach ($this->OUTPUT_TYPES as $ext) {
			if (is_file(PY_ROOT . DIR_SEP . $path . "." . $ext)) {
				return PY_ROOT . DIR_SEP . $path . "." . $ext;
			}
		}
		return false;
	}
	
	#######################################
	# STRUCTURE
	
	function getStructure ()
	{
		if (empty($this->PY))
			$this->readStructure;
		
		return $PY;
	}
	
	function parseStructure ()
	{
		return $this->readStructure(true);
	}
	
	private function readStructure ($return = false)
	{
		if ( !$return)
			$PY = &$this->PY;
		
		$PY['MODULES'] = (isset($PY['MODULES'])) ? $PY['MODULES'] : []; # module list
		$PY['LOADING'] = (isset($PY['LOADING'])) ? $PY['LOADING'] : []; # loading pipeline
		foreach ($this->CLASS_TYPES AS $type) {
			$PY["{$type}_PATH"] = (isset($PY["{$type}_PATH"])) ? $PY["{$type}_PATH"] : []; # class paths
			$PY[$type] = (isset($PY[$type])) ? $PY[$type] : []; # class list
		}
		$PY['TEMPLATE_PATH'] = (isset($PY['TEMPLATE_PATH'])) ? $PY['TEMPLATE_PATH'] : []; # core templates
		$PY['TEMPLATES'] = (isset($PY['TEMPLATES'])) ? $PY['TEMPLATES'] : []; # template list
		
		$load = array_fill(0, $this->TYPE_COUNT + 1, []);
		$extensions = explode(",", spl_autoload_extensions());
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
				
				// read manuell registered classes
				foreach ($this->CLASS_TYPES AS $type) {
					if (isset($conf[$type]) && is_array($conf[$type]))
						$PY[$type] = array_merge($PY[$type], $conf[$type]);
				}
				
				// GET CLASS DIRECTORYS & CLASSES
				foreach ($this->CLASS_TYPES AS $type) {
					$dir = isset($conf[$cnf_name = (strtolower($type) . "_dir")]) ? $conf[$cnf_name] : constant("static::DIR_{$type}");
					if (is_dir(PY_ROOT . DIR_SEP . $mod . DIR_SEP . $dir)) {
						array_push($PY["{$type}_PATH"], $mod . DIR_SEP . $dir);
						if (substr($path = $mod . DIR_SEP . $dir, -1) == DIR_SEP)
							$path = substr($path, 0, -1);
						foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(PY_ROOT . DIR_SEP . $path, \FilesystemIterator::SKIP_DOTS)) as $filePath => $fileObj) {
							if (array_search("." . pathinfo($filePath, PATHINFO_EXTENSION), $extensions) !== false) {
								$file = str_replace(PY_ROOT . DIR_SEP . $path . DIR_SEP, "", $filePath);
								if ( !isset($PY[$type][$base = substr($file, 0, strpos($file, "."))])) {
									$PY[$type][$base] = $path . DIR_SEP . $file;
								}
							}
						}
					}
				}
				
				// GET TEMPLATE DIRECTORYS & TEMPLATES
				$template_dir = isset($conf['template_dir']) ? $conf['TEMPLATE_DIR'] : static::DIR_TEMPLATE;
				if (is_dir(PY_ROOT . DIR_SEP . $mod . DIR_SEP . $template_dir)) {
					array_push($PY['TEMPLATE_PATH'], $mod . DIR_SEP . $template_dir);
					if (substr($path = $mod . DIR_SEP . $template_dir, -1) == DIR_SEP)
						$path = substr($path, 0, -1);
					foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(PY_ROOT . DIR_SEP . $path, \FilesystemIterator::SKIP_DOTS)) as $filePath => $fileObj) {
						if (array_search(pathinfo($filePath, PATHINFO_EXTENSION), $this->OUTPUT_TYPES) !== false && basename($filePath) != "index.php") {
							$file = str_replace(PY_ROOT . DIR_SEP . $path . DIR_SEP, "", $filePath);
							$file = str_replace(DIR_SEP, "-", $file);
							if ( !isset($PY["TEMPLATES"][$base = substr($file, 0, strpos($file, "."))])) {
								$PY["TEMPLATES"][$base] = $path . DIR_SEP . $base;
							}
						}
					}
				}
			}
		}
		
		# PREPARE LOADING SORT
		for ($p = 0; $p < $this->TYPE_COUNT; $p++ ) {
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
		return true;
	}
	
}

}
