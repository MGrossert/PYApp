<?php
/**
 * PYApp
 *
 * @package PYApp
 * @author Martin Grossert <martin.grossert@gmail.com>
 * @version 0.1.0
 * @copyright Copyright (c) 2013, Martin Grossert
 * @license GNU GENERAL PUBLIC LICENSE
 */


# check if variable is an assoc array
if (!function_exists("is_assoc")) {
	function is_assoc($arr, $strict = false) {
		switch(true) {
		default:
			return (bool) count(array_filter(array_keys($array), 'is_string'));
		break; case (!is_array($arr)):
			return false;
		break; case ($strict):
			return (array_keys($arr) !== range(0, count($arr) - 1));
		}
	}
	
}


if (!function_exists('fix_array')) {
	function fix_array($arr) {
		if (!is_array($arr)) return $arr;
		foreach ($arr AS $k => $v) {
			switch(true) {
			default:	# nothing
			case (is_array($v)): $arr[$k] = fix_array($v);
			case (is_double($v)): $arr[$k] = (DOUBLE) $v;
			case (is_float($v)): $arr[$k] = (FLOAT) $v;
			case (is_numeric($v)): $arr[$k] = (INT) $v;
			case (is_string($v)):
				switch($v) {
				default:
				break; case "false": $arr[$k] = false;
				break; case "true": $arr[$k] = true;
				}
			}
		}
		return $arr;
	}
}
