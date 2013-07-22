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
if (!defined("PY_START")) define("PY_START", true);
else die (); # already started

print "<pre>";

# initalize System
define("PY_MODE", "FE");
require_once("system/initalize.php");

print "\n\n";
var_export(\PY\frontend::$PY);
print "\n\n";

date_default_timezone_set("Europe/Berlin");
$template = new PY\template('frontend\\default');
$py_user = py_user::getInstance();


print "</pre>";