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
 * SQLFunction class
 *
 * This class could be used to build specific querys that requires
 * specific database functions that data mapper does not support
 */
class Drone_Db_SQLFunction
{
    /**
     * The SQL function name
     *
     * @var string
     */
    private $function;

    /**
     * The arguments for the SQL function
     *
     * @var string
     */
    private $arguments;

    /**
     * Returns the SQL function
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Returns the arguments for the SQL function
     *
     * @return string
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Constructor
     *
     * @param string $function
     * @param array $args
     *
     * @return null
     */
    public function __construct($function, Array $args)
    {
        $this->function  = $function;
        $this->arguments = $args;
    }

    /**
     * Returns the SQL statment
     *
     * @return string
     */
    public function getStatement()
    {
        $arguments = $this->arguments;

        foreach ($arguments as $key => $arg)
        {
            if (is_string($arg))
                $arguments[$key] = "'$arg'";
        }

        $arguments = implode(", ", array_values($arguments));

        return $this->function . '(' . $arguments . ')';
    }
}