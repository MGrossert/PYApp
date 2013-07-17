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
if (!defined('DIR_SEP')) define('DIR_SEP', DIRECTORY_SEPARATOR);

require_once('config.php');															# base conf
require_once('system'.DIR_SEP.'function.php');										# core functions

# Base Classes
require_once('system'.DIR_SEP.'classes'.DIR_SEP.'singleton.php');					# singleton trait
require_once('system'.DIR_SEP.'classes'.DIR_SEP.'PY'.DIR_SEP.'base.php');			# objectBase class
require_once('system'.DIR_SEP.'classes'.DIR_SEP.'PY'.DIR_SEP.'system.php');			# system class

# initalize core
if (PY_MODE == 'BE') {
	require_once('system'.DIR_SEP.'classes'.DIR_SEP.'PY'.DIR_SEP.'backend.php');	# backend class
	$frontend = PY\backend::getInstance();	
} else {
	require_once('system'.DIR_SEP.'classes'.DIR_SEP.'PY'.DIR_SEP.'frontend.php');	# frontend class
	$frontend = PY\frontend::getInstance();	
}
$system = PY\system::getInstance();	

