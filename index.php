<?php
/**
 * PYApp
 *
 * @package PYApp
 * @author Martin Grossert <martin.grossert@gmail.com>
 * @version 0.1.0
 * @copyright Copyright (c) 2013, Martin Grossert
 * @license http://www.opensource.org/licenses/mit-license.php
 */

if (!defined("PY_START")) define("PY_START", true);
else die (); // already started

require_once("./config.php");						// base conf
require_once("lib/function.php");					// global functions

require_once("system/classes/system.php");			// system class

$system = \PY\System::getInstance();
$system->initalizeFrontend();

