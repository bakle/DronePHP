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
}