<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

class Drone_Mvc_Application
{
    /**
     * @var array
     */
	private $modules;

    /**
     * Returns the router instance
     *
     * @var Drone_Mvc_Router
     */
	private $router;

    /**
     * @var boolean
     */
	private $devMode;

    /**
	 * @return Drone_Mvc_Router
     */
	public function getRouter()
	{
		return $this->router;
	}

    /**
     * Prepares the app environment
     *
	 * @return null
     */
	public function prepare()
	{
		# Start sessions
		if (!isset($_SESSION))
			session_start();
	}

    /**
     * Checks app.config structure
     *
     * @param array $required_tree
     * @param array $parameters
     *
	 * @return null
     */
	public function verifyRequiredParameters(Array $required_tree, Array $parameters)
	{
		foreach ($required_tree as $key => $value)
		{
			$req_keys = array_keys($parameters);

			if (!in_array($key, $req_keys))
				throw new Exception("The key '$key' must be in the configuration!", 1);

			if (is_array($value))
				$this->verifyRequiredParameters($value, $parameters[$key]);
		}
	}

    /**
     * Constructor
     *
     * @param array $init_parameters
     */
	public function __construct($init_parameters)
	{
		$this->prepare();

		$this->verifyRequiredParameters(
			array(
				"modules" 		=> array(
					"{key}"		=> "{value}"
				),
				"router"		=> array(
					"routes"	=> array(
			            'defaults' => array(
			                'module' 		=> '{value}',
			                'controller'	=> '{value}',
			                'view' 			=> '{value}'
			            )
					)
				),
				"environment"	=> array(
					"base_path" => "{value}",
					"dev_mode"	=> "{value}"
				)
			),
		$init_parameters);

		$this->devMode = $init_parameters["environment"]["dev_mode"];
		$this->modules = $init_parameters["modules"];

		/*
		 *	DEV MODE:
		 *	Set Development or production environment
		 */

		if ($this->devMode)
		{
			ini_set('display_errors', 1);

			// See errors
			// error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

			// PHP 5.4
			// error_reporting(E_ALL);

			// Best way to view all possible errors
			error_reporting(-1);
		}
		else {
			ini_set('display_errors', 0);
			error_reporting(-1);
		}

		$this->loadModules($this->modules, $init_parameters["router"]["routes"]["defaults"]["module"]);

		$this->router = new Drone_Mvc_Router($init_parameters["router"]["routes"]);
		$this->router->setBasePath($init_parameters["environment"]["base_path"]);

		# load routes from modules
		foreach ($this->modules as $module)
		{
			if (file_exists("module/$module/config/module.config.php"))
			{
				$module_config_file = require "module/$module/config/module.config.php";
				$this->getRouter()->addRoute($module_config_file["router"]["routes"]);
			}
		}
	}

    /**
     * Loads user classes in each module
     *
     * @param array $modules
     * @param array $module
     *
	 * @return null
     */
	private function loadModules($modules, $module)
	{
		$fileSystem = new Drone_FileSystem_Shell();

		if ($modules)
		{
			$mod = array_key_exists('module', $_GET) ? $_GET["module"] : $module;

			foreach ($modules as $module)
			{
				// First include the Module class
				if (file_exists("module/".$module."/Module.php"))
					include("module/".$module."/Module.php");

				spl_autoload_register($module . "_Module::loader");
			}
		}
		else
			throw new Exception("The application must have at least one module");
	}

    /**
     * Runs the application
     *
	 * @return null
     */
	public function run()
	{
		$module = isset($_GET["module"]) ? $_GET["module"] : null;
		$controller = isset($_GET["controller"]) ? $_GET["controller"] : null;
		$view = isset($_GET["view"]) ? $_GET["view"] : null;

		$this->router->setIdentifiers($module, $controller, $view);
		$this->router->run();
	}
}