<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/Drone
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

abstract class Drone_Mvc_AbstractionModule
{
	protected $moduleName;

	public function __construct($moduleName, $controller)
	{
		$this->moduleName = $moduleName;
		$this->init($controller);
	}

	public abstract function init($controller);

	public function getModuleName()
	{
		return $this->moduleName;
	}

	# Get system configuration
	public function getConfig()
	{
		return include 'module/' . $this->getModuleName() . '/config/module.config.php';
	}
}