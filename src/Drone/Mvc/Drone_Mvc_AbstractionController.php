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
 * AbstractionController class
 *
 * This class manges the interaction between models and views
 */
abstract class Drone_Mvc_AbstractionController
{
    /**
     * Current module
     *
     * @var string
     */
    private $module;

    /**
     * Current method
     *
     * @var string
     */
    private $method = null;

    /**
     * Layout name
     *
     * @var string
     */
    private $layout = "default";

    /**
     * Terminal mode
     *
     * @var boolean
     */
    private $terminal = false;

    /**
     * Defines starting execution
     *
     * When this parameter is true, the constructor executes the method of the specified controller
     * The only way to stop init execution is throw the method stopInitExecution() inside a module class
     *
     * @var boolean
     */
    private $initExecution = true;

    /**
     * Base path
     *
     * @var string
     */
    private $basePath;

    /**
     * Returns the current module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Returns the current method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Returns the mode of visualization
     *
     * @return boolean
     */
    public function getTerminal()
    {
        return $this->terminal;
    }

    /**
     * Returns the current layout
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Returns the base path
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Returns the class name
     *
     * @return string
     */
    public static function getClassName()
    {
        return __CLASS__;
    }

    /**
     * Returns $_POST contents
     *
     * @return array
     */
    public function getPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST))
            $_POST = json_decode(file_get_contents('php://input'), true);

        return (array) $_POST;
    }

    /**
     * Returns json contents
     *
     * @throws LogicException
     *
     * @return array
     */
    public function getJson()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'JSON')
            throw new LogicException("Request method is not JSON");

        $input =  file_get_contents('php://input');
        $array = explode("&", $input);

        $result = array();

        foreach ($array as $value)
        {
            $io = explode("=", $value);
            $result[$io[0]] = $io[1];
        }

        return $result;
    }

    /**
     * Sets the terminal mode
     *
     * @param boolean $terminal
     *
     * @return null
     */
    public function setTerminal($terminal = true)
    {
        $this->terminal = $terminal;
    }

    /**
     * Sets layout name
     *
     * @param string $layout
     *
     * @return null
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Sets the method attribute
     *
     * @param string $method
     *
     * @return null
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Constructor
     *
     * @param string $module
     * @param string $method
     * @param string $basePath
     *
     * @throws PageNotFoundException
     */
    public function __construct($module, $method, $basePath)
    {
        $this->basePath = $basePath;
        $this->parseRequestParameters($_GET);

        /* Module class:
         * Each module must have a class called Module in her namesapce. This class
         * is initilized here, and contains several configurations and methods for
         * controllers.
         */
        $fqn = $module . "_Module";

        $this->module = new $fqn($module, $this);

        # detects method change inside Module.php
        if (!is_null($this->getMethod()))
            $method = $this->getMethod();

        if (!is_null($method) && $this->initExecution)
        {
            if (method_exists($this, $method))
            {
                $class = __CLASS__;

                $reflection = new ReflectionMethod($this, $method);

                if (!$reflection->isPublic())
                    throw new Drone_Mvc_PageNotFoundException("The method '$method' is not public in the control class '$class'");

                $this->method = $method;

                // Get the return value of the method (parameters sent to the view)
                $this->params = $this->$method();

                if (!is_null($this->getMethod()))
                {
                    $layoutManager = new Drone_LayoutManager_Layout();
                    $layoutManager->fromController($this);
                }
            }
            else {
                $class = dirname(__FILE__);
                throw new Drone_Mvc_PageNotFoundException("The method '$method' doesn't exists in the control class '$class'");
            }
        }
    }

    /**
     * Stops the execution of the specified method inside of __construct()
     *
     * @return null
     */
    public function stopInitExecution()
    {
        $this->initExecution = false;
    }

    /**
     * Checks if the current request is XmlHttpRequest (AJAX)
     *
     * @return boolean
     */
    public function isXmlHttpRequest()
    {
        # non standard (HTTP_X_REQUESTED_WITH is not a part of PHP)
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']))
            return true;
        return false;
    }

    /**
     * Checks if the current request is POST
     *
     * @return boolean
     */
    public function isPost()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST")
            return true;
        return false;
    }

    /**
     * Checks if the current request is GET
     *
     * @return boolean
     */
    public function isGet()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET")
            return true;
        return false;
    }

    /**
     * Parses requests parameters
     *
     * Searches for URI formed as follows /var1/value1/var2/value2
     *
     * @return null
     */
    private function parseRequestParameters($get)
    {
        if (array_key_exists('params', $_GET))
        {
            $params = explode("/", $_GET["params"]);

            $vars = $values = array();

            $i = 1;
            foreach ($params as $item)
            {
                if ($i % 2 != 0)
                    $vars[] = $item;
                else
                    $values[] = $item;
                $i++;
            }

            for ($i = 0; $i < count($vars); $i++)
            {
                if (array_key_exists($i, $values))
                    $_GET[$vars[$i]] = $values[$i];
                else
                    $_GET[$vars[$i]] = '';
            }

            unset($_GET["params"]);
        }
    }
}