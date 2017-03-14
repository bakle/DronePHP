<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

abstract class Drone_Db_TableGateway_AbstractTableGateway
{
    /**
     * Driver connection
     *
     * @var DriverAdapter
     */
    private static $driver;

    /**
     * Returns the DriverAdapter
     *
     * @return DriverAdapter
     */
    public static function getDriver()
    {
        return self::$driver;
    }

    /**
     * Constructor
     *
     * @param string  $connection_identifier
     * @param boolean $auto_connect
     */
    public function __construct($connection_identifier = "default", $auto_connect = true)
    {
        if (!isset(self::$driver))
            self::$driver = new Drone_Db_Driver_DriverAdapter($connection_identifier, $auto_connect);
    }
}