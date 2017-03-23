<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

abstract class Drone_Mvc_AbstractionModule
{
	/**
	 * @var string
	 */
	protected $moduleName;

	/**
	 * Constructor
	 *
	 * @param string                		  $moduleName
	 * @param Drone_Mvc_AbstractionController $controller
	 */
	public function __construct($moduleName, Drone_Mvc_AbstractionController $controller)
	{
		$this->moduleName = $moduleName;
		$this->init($controller);
	}

	/**
	 * Absract method to be executed before each controller in each module
	 *
	 * @param Drone_Mvc_AbstractionController
	 */
	public abstract function init(Drone_Mvc_AbstractionController $controller);

	/**
	 * Returns the moduleName attribute
	 *
	 * @return string
	 */
	public function getModuleName()
	{
		return $this->moduleName;
	}

	/**
	 * Returns an array with application settings
	 *
	 * @return array
	 */
	public function getConfig()
	{
		return include 'module/' . $this->getModuleName() . '/config/module.config.php';
	}

	/**
	 * Creates an autoloader for module classes
	 *
	 * @param string $name
	 *
	 * @return null
	 */
	public static function loader($name)
	{
		$class = $name;
		$nm    = explode('_', $name);

		$module     = array_shift($nm);
		$path 		= array_shift($nm);
		$className  = array_shift($nm);

		$class = "module/" . $module . "/source/" . $path ."/". $name . ".php";

		if (file_exists($class))
			include $class;
	}
}