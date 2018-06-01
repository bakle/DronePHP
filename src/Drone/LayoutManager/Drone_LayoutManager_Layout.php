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
class Drone_LayoutManager_Layout
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
        $view = 'module/' . $controller->getModule()->getModuleName() .'/source/view/'. basename(str_replace('\\','/',get_class($controller))) . '/' . $controller->getMethod() . '.phtml';

        $this->setParams($controller->getParams());
        $this->basePath = $controller->getBasePath();
        $this->controller = $controller;
        $this->view = $view;

        if ($controller->getTerminal())
        {
            if (file_exists($view))
                include $view;
        }
        else
        {
            $config = $controller->getModule()->getConfig();
            include $config["view_manager"]["template_map"][$controller->getLayout()];
        }

        $this->parameterProvider = new Drone_Util_Parameterizable();
    }

    /**
     * Loads a view from a template file
     *
     * @throws PageNotFoundException
     *
     * @param Drone\Mvc\AbstractionModule $module
     * @param string $template
     * @param array $params
     */
    public function fromTemplate($module, $template, $params = array())
    {
        $this->setParams($params);

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
            throw new Drone_Mvc_PageNotFoundException("The 'view' template " . $this->view . " does not exists");

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