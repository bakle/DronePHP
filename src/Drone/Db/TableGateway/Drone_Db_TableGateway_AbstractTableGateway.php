<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

abstract class Drone_Db_TableGateway_AbstractTableGateway
{
    /**
     * Driver collector
     *
     * @var DriverAdapter[]
     */
    private static $drivers;

    /**
     * Current driver identifier
     *
     * @var string
     */
    private $currentDriverIdentifier;

    /**
     * Returns all registered drivers
     *
     * @return DriverAdapter[]
     */
    public static function getDrivers()
    {
        return self::$drivers;
    }

    /**
     * Returns the current driver identifier
     *
     * @return string
     */
    public function getCurrentDriverIdentifier()
    {
        return $this->currentDriverIdentifier;
    }

    /**
     * Returns the current DriverAdapter
     *
     * @return DriverAdapter
     */
    public function getDriver()
    {
        return self::$drivers[$this->currentDriverIdentifier];
    }

    /**
     * Constructor
     *
     * @param string  $connection_identifier
     * @param boolean $auto_connect
     */
    public function __construct($connection_identifier = "default", $auto_connect = true)
    {
        $this->currentDriverIdentifier = $connection_identifier;

        if (!isset(self::$drivers[$connection_identifier]))
            self::$drivers[$connection_identifier] = new Drone_Db_Driver_DriverAdapter($connection_identifier, $auto_connect);
    }
}