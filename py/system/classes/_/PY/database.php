<?php	
/** 
 * PY Database
 *
 * @package PYApp
 * @author Martin Grossert <martin.grossert@gmail.com>
 * @version 0.1.0
 * @copyright Copyright (c) 2013, Martin Grossert
 * @license GNU GENERAL PUBLIC LICENSE
 */

namespace PY;

/*
	# WAYS TO CONNECT:
	#############################
	
	# get default
	database::getInstance();
	# with array
	database::connect(['type' => 'xxsql', 'host' => '127.0.0.1', 'dbname' => 'database', 'port' => 9999, 'user' => 'root' , 'password' => 'password', 'options' => []]);
	database::connect(['type' => 'mysql', 'host' => '127.0.0.1', 'dbname' => 'database', 'port' => 3306, 'charset' => 'UTF-8', 'user' => 'root' , 'password' => 'password', 'options' => []]);
	database::connect(['type' => 'sqlite', 'file' => '/path/to/file', 'user' => 'owner' , 'password' => 'password', 'options' => []]);
	database::connect(['type' => 'sqlite', 'file' => ':memory:']);
	# with string
	database::getInstance($dsn, 'user' , 'password', []);
	# with parameters
	database::connect('mysql', '127.0.0.1','user', 'password', '127.0.0.1', 'database', []);
	
*/

	
class database {
	# use \Singleton;		=> semi singelton
	
	#######################################
	# STATIC VARS
    protected static $instance = null;
	protected static $instances = [];
	
	# INTERNAL VARS
    protected $pdo = null;

	#######################################
	# INITALIZE / CREATE
	
    final private function __construct($dsn=null, $user=false, $password=false, $options=[]) {
        static::__initialize($dsn, $user, $password, $options);
    }
	
	# protect functions 
    final private function __wakeup() {}
    final private function __sleep() {}
    final private function __clone() {}
	
    final public static function &getInstance($dsn=null, $user=false, $password=false, $options=[]) {
		$instance = null;
		
		if (is_null($dsn)) {
			# check default instance
			$instance = static::$instance;

		} else {
			# check connection array
			if (!isset(static::$instances[$dsn])) 
				$instance = static::$instances[$dsn] = new static($dsn, $user, $password, $options);
			if (null === static::$instance) 
				static::$instance = $instance;
		}
		return $instance;
    }

	public static function connect($db='mysql', $user='root', $password=false, $host='127.0.0.1', $dbname=false, $options = []) {
		if (!is_array($db)) {
			$type = $db;
			$db = [
				'host'		=> $host
			,	'dbname'	=> $dbname
			];
		} else {
			$type = $db['type'];unset($db['type']);
			if (isset($db['user'])) {$user = $db['user'];unset($db['user']);}
			if (isset($db['password'])) {$password = $db['password'];unset($db['password']);}
			if (isset($db['options'])) {$options = $db['options'];unset($db['options']);}
		}
		
		switch ($type) {
			default:
			break; case 'mysql':
				$db['charset'] = isset($db['charset'])?$db['charset']:'UTF8';
		}
		$db = array_filter($db);
		array_walk($db, function(&$v,$k) { $v = "$k=$v"; });
		$dsn = "$type:".implode(";", $db );
		return static::getInstance($dsn, $user, $password, $options);
	}

    protected function __initialize($dsn=null, $user=false, $password=false, $options=[]) {
		if (is_null($dsn)) return false;
		if (!is_array($options)) $options = [];
		
		switch(false) {
		default:
			$this->pdo = new \PDO($dsn, $user, $password, $options);
		break; case ($password):
			$this->pdo = new \PDO($dsn, $user);
		break; case ($user):
			$this->pdo = new \PDO($dsn);
		}
	}

	#######################################
	# METHODS
	
	function __call ($name, $arguments) {
		# provide PDO functions
		if (method_exists($this->pdo, $name)) {
			$ret = call_user_func_array(array($this->pdo, $name), $arguments);
			if (is_a($ret, "PDOStatement")) $ret = new dbStatement($ret);
			return $ret;
		} 
		
		# provide statement functions .... the start of the query
		$STMT = new dbStatement();
		if (method_exists($STMT, $name)) {
			return call_user_func_array(array($STMT, $name), $arguments);
		}
		unset($STMT);
		
	}
	static function __callStatic ($name, $arguments) {
		# provide PDO functions
		if (method_exists('PDO', $name)) {
			$ret = call_user_func_array(array('PDO', $name), $arguments);
			if (is_a($ret, "PDOStatement")) $ret = new dbStatement($ret);
			return $ret;
		}
	}
	
}