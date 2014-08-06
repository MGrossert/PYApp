<?php	
/** 
 * PY Database Statement
 *
 * @package PYApp
 * @author Martin Grossert <martin.grossert@gmail.com>
 * @version 0.1.0
 * @copyright Copyright (c) 2013, Martin Grossert
 * @license GNU GENERAL PUBLIC LICENSE
 */

namespace PY;

class dbStatement {
	
	#######################################
	# INTERNAL VARS
    protected $db = null;
    protected $stmt = null;
    protected $sql = "";
    protected $query = [
		'db'	=> false
	];

	#######################################
	# MAGIC METHODES
	
    public function __construct() {
		$p = func_get_args();
		if (count($p) == 1 && is_array($p[0])) $p = $p[0]; 
		foreach($p AS $key => $value) {
			switch(true) {
			default:
			break; case (is_a($value, "PY\database")):
				$this->db = $value;
			break; case (is_a($value, "PDOStatement")):
				$this->stmt = $value;
				$this->query = $this->parse($this->sql = $this->stmt->queryString);
				
			break; case(is_string($value)):
				$this->query = $this->parse($this->sql = $value);
			}
		}
		if ($this->db === null)
			$this->db = database::getInstance();
    }
	
	function __call ($name, $arguments) {
		switch (true) {
		default:
		
		# provide PDO functions
		break; case(method_exists($this->stmt, $name)):
			return call_user_func_array(array($this->stmt, $name), $arguments);
		}
	}
	
	static function __callStatic ($name, $arguments) {
		switch (true) {
		default:
		
		# provide PDO functions
		break; case(method_exists('PDOStatement', $name)):
			return call_user_func_array(array('PDOStatement', $name), $arguments);
		}
		
	}
	
	
	public function __toString() {
		print __CLASS__ . "__toString()\n";
		return '';
	}
	
	public function __invoke($obj) {
		print __CLASS__ . "__invoke(";
		var_dump($obj);
		print ")\n";
	}
	
    public static function __set_state($obj) {
		print __CLASS__ . "__set_state(";
		var_dump($obj);
		print ")\n";
        return $obj;
    }
	
    public function __set($name, $value) {
		print __CLASS__ . "__set($name, ";
		var_dump($value);
		print ")\n";
	} 

    public function __get($name) {
		print __CLASS__ . "__get($name)\n";
		return '';
	}

	public function __isset($name) {
		print __CLASS__ . "__isset($name)\n";
		return false;
	}

    /**  As of PHP 5.1.0  */
	public function __unset($name) {
		print __CLASS__ . "__unset($name)\n";
	}
	
	#######################################
	# METHODES
	
	function parse ($query = '') {
		if ($query=='') return [];
		# parse query to structure
		
		
		
		
	}
	
	function db ($dbname = '') {return $this->database($dbname);}
	function database ($dbname = '') {
		$this->query['db'] = $dbname;
		return $this;
	}
	
	function table ($table = false) {
		if (strpos($table, ".")!== false) {
			$arr = explode(".", $table);
			$this->database($arr[0]);
			$table = $arr[1];
		}
		$this->query['table'] = $table;
		return $this;
	}
	
	function exist () {
		static $stmt = null;
		
		# lookup Table
		if ($this->query['table']!='') {
			$name = implode(".", array_filter([
				$this->query['db']
			,	$this->query['table']
			]));
			if ($stmt === null) $stmt = $this->db->prepare("SHOW TABLES LIKE ':n'");
			$stmt->execute([':n' => $name]);
			return ($stmt->rowCount() > 0);
			
		# or database
		} elseif ($this->query['table']!='') {
			$name = $this->query['db'];
			if ($stmt === null) $stmt = $this->db->prepare("SHOW DATABASES LIKE ':n'");
			$stmt->execute([':n' => $name]);
			return ($stmt->rowCount() > 0);
			
		}
	}
	
	function fields($arr) {
		if (is_array($arr))
			$this->query['fields'] = $arr;
		return $this;
	}
	
	function index($arr) {
		if (is_array($arr))
			$this->query['index'] = $arr;
		return $this;
	}
	
	function create() {
		return $this;
	}
	
}