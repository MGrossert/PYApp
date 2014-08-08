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
require('function.php');	# core functions

# initalize core
include('system'.DIR_SEP.'classes'.DIR_SEP.'System.php');	# system class
use PY\System;
$system = System::getInstance();
$system->initialize();
