<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

class Drone_Mvc_Router
{
    /**
     * @var array
     */
	private $routes;

    /**
     * The Identifiers builds the route
     *
     * @var array
     */
	private $identifiers;

    /**
     * Controller instance
     *
     * @var Drone\Mvc\AbstractionController
     */
	private $controller;

    /**
     * @var string
     */
	private $basePath;

    /**
     * Returns all routes built
     *
     * @return array
     */
	public function getRoutes()
	{
		return $this->routes;
	}

    /**
     * Returns all identifiers
     *
     * @return array
     */
	public function getIdentifiers()
	{
		return $this->identifiers;
	}

    /**
     * Returns the controller instance
     *
     * @return Drone\Mvc\AbstractionController
     */
	public function getController()
	{
		return $this->controller;
	}

    /**
     * Returns the base path of the application
     *
     * @return string
     */
    public function getBasePath()
	{
		return $this->basePath();
	}

    /**
     * Sets identifiers
     *
     * @param string $module
     * @param string $controller
     * @param string $view
     *
     * @return null
     */
	public function setIdentifiers($module, $controller, $view)
	{
		$this->identifiers = array(
			"module"		=> $module,
			"controller"	=> $controller,
			"view"			=> $view
		);
	}

    /**
     * Sets the basePath attribute
     *
     * @param string $basePath
     *
     * @return null
     */
	public function setBasePath($basePath)
	{
		$this->basePath = $basePath;
	}

    /**
     * Constructor
     *
     * @param  array $routes
     */
	public function __construct($routes)
	{
		$this->routes = $routes;
	}

    /**
     * Builds the current route and calls the controller
     *
     * @return  null
     */
	public function run()
	{
		/*
		 *	Route builder:
		 *	The route is constructed from the URL in the following order
		 *	www.example.com/module/controller/view
		 */

		$module = (is_null($this->identifiers["module"]) || empty($this->identifiers["module"]))
					? $this->routes["defaults"]["module"] : $this->identifiers["module"];

		$controller = (is_null($this->identifiers["controller"]) || empty($this->identifiers["controller"]))
					? $this->routes[$module]["controller"] : $this->identifiers["controller"];

		$view = (is_null($this->identifiers["view"]) || empty($this->identifiers["view"]))
					? $this->routes[$module]["view"] : $this->identifiers["view"];

        $fqn_controller = $module . "_Controller_" . $controller;

		if (class_exists($fqn_controller))
			$this->controller = new $fqn_controller($module, $view, $this->basePath);
		else
			throw new Exception("The control class '$fqn_controller' does not exists!", 1);
	}

    /**
     * Adds a new route to router
     *
     * @param Array $routes
     *
     * @return string
     */
	public function addRoute($route)
	{
		$this->routes = array_merge($this->routes, $route);
	}
}