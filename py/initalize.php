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
define("START", microtime(true));
if (!defined('PY_START'))	die (); # exit, if not started
const DIR_SEP = DIRECTORY_SEPARATOR;
const PY_ROOT = __DIR__;
require_once('function.php');														# core functions

# Base Classes
require_once('system'.DIR_SEP.'traits'.DIR_SEP.'singleton.php');					# singleton trait
require_once('system'.DIR_SEP.'classes'.DIR_SEP.'system.php');						# system class

# initalize core
$system = system::getInstance();
$system->initialize(PY_MODE);
