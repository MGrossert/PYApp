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
 
if (!defined('PY_START')) die (); # exit, if not started
if (!defined('DIR_SEP'))	define('DIR_SEP', DIRECTORY_SEPARATOR);
if (!defined("PY_ROOT"))	define("PY_ROOT", dirname(__DIR__));


require_once('system'.DIR_SEP.'function.php');										# core functions

# Base Classes
require_once('system'.DIR_SEP.'classes'.DIR_SEP.'singleton.php');					# singleton trait
require_once('system'.DIR_SEP.'classes'.DIR_SEP.'PY'.DIR_SEP.'base.php');			# objectBase class
require_once('system'.DIR_SEP.'classes'.DIR_SEP.'PY'.DIR_SEP.'system.php');			# system class

# System Paths
$GLOBALS['PY']['CLASS_PATH'][] = 'system'.DIR_SEP.'classes';						# core classes
# autoload like classes or preload?
$GLOBALS['PY']['CLASS_PATH'][] = 'system'.DIR_SEP.'dco';							# data container
$GLOBALS['PY']['DCO_PATH'][] = 'system'.DIR_SEP.'dco';								# data container

# initalize core
if (PY_MODE == 'BE') {
	require_once('system'.DIR_SEP.'classes'.DIR_SEP.'PY'.DIR_SEP.'backend.php');	# backend class
	$frontend = PY\backend::getInstance();	
} else {
	require_once('system'.DIR_SEP.'classes'.DIR_SEP.'PY'.DIR_SEP.'frontend.php');	# frontend class
	$frontend = PY\frontend::getInstance();	
}
$system = PY\system::getInstance();	
