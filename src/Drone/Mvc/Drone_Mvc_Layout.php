<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

/**
 * Layout class
 *
 * This class manages templates from views
 */
class Drone_Mvc_Layout
{
    /**
     * Controller instance
     *
     * @var AbstractionController
     */
    private $controller;

    /**
     * View path
     *
     * @var string
     */
    private $view;

    /**
     * Document title
     *
     * @var string
     */
    private $title;

    /**
     * Base path
     *
     * @var string
     */
    private $basePath;

    /**
     * Parametrizable class
     *
     * @var Drone_Util_Parameterizable
     */
    protected $parameterProvider;

    /**
     * Returns the instance of current controller
     *
     * @return AbstractionController
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Returns the view
     *
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Returns the document title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the document title
     *
     * @param string $title
     *
     * @return null
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Sets the view
     *
     * @param Drone_Mvc_AbstractionModule $module
     * @param string $view
     *
     * @return null
     */
    public function setView($module, $view)
    {
        $config = $module->getConfig();


        if (!array_key_exists($view, $config["view_manager"]["view_map"]) || !file_exists($config["view_manager"]["view_map"][$view]))
            throw new Drone_Mvc_Exception_ViewNotFoundException("The 'view' template " . $view . " does not exists");

        $this->view = $config["view_manager"]["view_map"][$view];
    }

    /**
     * Sets the base path
     *
     * @param string $basePath
     *
     * @return null
     */
    public function setBasePath($path)
    {
        $this->basePath = $path;
    }

    /**
     * Constructor
     *
     * @throws PageNotFoundException
     */
    public function __construct()
    {
        // nothing to do
    }

    /**
     * Loads a view from a controller
     *
     * @throws PageNotFoundException
     *
     * @param AbstractionController
     */
    public function fromController($controller)
    {
        // str_replace() is needed in linux systems
        $this->setParams($controller->getParams());
        $this->basePath = $controller->getBasePath();
        $this->controller = $controller;

        if ($controller->getShowView())
        {
            $view = 'module/'      . $controller->getModule()->getModuleName() .
                    '/source/view/'. basename(str_replace('\\','/',get_class($controller))) .
                    '/'            . $controller->getMethod() . '.phtml';

            $this->view = $view;
        }

        if ($controller->getTerminal())
        {
            if (file_exists($view))
                include $view;
        }
        else
        {
            if (!is_null($this->view) && !file_exists($this->view))
                throw new Drone_Mvc_Exception_ViewNotFoundException("The 'view' template " . $this->view . " does not exists");

            $config = $controller->getModule()->getConfig();

            if (!array_key_exists($controller->getLayout(), $config["view_manager"]["template_map"]))
                throw new Drone_Mvc_Exception_PageNotFoundException("The 'template' " . $template . " was not defined in module.config.php");

            $template = $config["view_manager"]["template_map"][$controller->getLayout()];

            if (!file_exists($template))
                throw new Drone_Mvc_Exception_PageNotFoundException("The 'template' " . $template . " does not exists");

            include $template;
        }

        $this->parameterProvider = new Drone_Util_Parameterizable();
    }

    /**
     * Loads a view from a template file
     *
     * @throws PageNotFoundException
     *
     * @param Drone_Mvc_AbstractionModule $module
     * @param string $template
     */
    public function fromTemplate($module, $template)
    {
        $config = $module->getConfig();
        include $config["view_manager"]["template_map"][$template];
    }

    /**
     * Includes the file view
     *
     * @return null
     */
    public function content()
    {
        if (!file_exists($this->view))
            throw new Drone_Mvc_Exception_ViewNotFoundException("The 'view' template " . $this->view . " does not exists");

        include $this->view;
    }

    /**
     * Returns the base path of the application
     *
     * @return string
     */
    public function basePath()
    {
        return $this->basePath;
    }
}