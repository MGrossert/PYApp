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
print "<pre>";
if (!defined("PY_START")) define("PY_START", true);
else die (); # already started

# initalize System
define("PY_MODE", "FE");
require_once("py/initalize.php");

// $template = new PY\template('frontend\\default');
// $py_user = py_user::getInstance();
// $db = database::getInstance();


print "</pre>";