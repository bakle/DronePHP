<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

abstract class Drone_Db_AbstractTableGateway
{
    /**
     * Handle
     *
     * @var Driver
     */
    private static $db;

    /**
     * Constructor
     *
     * @param string  $abstract_connection_string
     * @param boolean $auto_connect
     *
     * @return null
     */
    public function __construct($abstract_connection_string = "default", $auto_connect = true)
    {
       $dbsettings = include(dirname(__FILE__) . "/../../../config/database.config.php");

        $drivers = array(
            "Oci8"          => "Drone_Sql_Oracle",
            "Mysqli"        => "Drone_Sql_MySQL",
            "Sqlsrv"        => "Drone_Sql_SQLServer",
        );

        $drv = $dbsettings[$abstract_connection_string]["driver"];

        if (!array_key_exists($drv, $drivers))
            throw new Exception("The Database driver '$drv' does not exists");

        if (array_key_exists($drv, $drivers) && !isset(self::$db))
            self::$db = new $drivers[$drv]($dbsettings[$abstract_connection_string]);
    }

    /**
     * Returns the handle instance
     *
     * @return Driver
     */
    public static function getDb()
    {
        return self::$db;
    }
}