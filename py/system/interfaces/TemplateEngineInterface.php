<?php

interface TemplateEngineInterface {
	/**
	 * set a template value
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	function assign($name, $value);

	/**
	 * get a template value
	 *
	 * @param string $name
	 */
	function get($name);

	/**
	 * Set a other Template view.
	 *
	 * @param string $view
	 */
	function setView($view);

	/**
	 * Returns the Template Output
	 *
	 * @return string
	 */
	function getOutput();
}
