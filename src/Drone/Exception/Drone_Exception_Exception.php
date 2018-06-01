<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

/**
 * Exception class
 *
 * This is a standard exception that implements Drone_Exception_Storage as a provider.
 * Developers can use this exception to separate controller exceptions in the business logic.
 */
class Drone_Exception_Exception extends Exception
{
    /**
     * Storable class
     *
     * @var Drone_Exception_Storable
     */
    protected $storableProvider;

    /**
     * Constructor
     *
     * @param string         $message
     * @param integer        $code
     * @param Exception|null $previous
     *
     * @return null
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->storableProvider = new Drone_Exception_Storable();
    }
}