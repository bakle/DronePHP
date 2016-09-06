<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
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
	 * @param string $moduleName
	 */
	public function __construct($moduleName, $controller)
	{
		$this->moduleName = $moduleName;
		$this->init($controller);
	}

	public abstract function init($controller);

	/**
	 * Gets module name
	 *
	 * @return string
	 */
	public function getModuleName()
	{
		return $this->moduleName;
	}

	/**
	 * Gets configuration file
	 *
	 * @return array
	 */
	public function getConfig()
	{
		return include 'module/' . $this->getModuleName() . '/config/module.config.php';
	}
}