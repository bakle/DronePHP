<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/Drone
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

abstract class Drone_Sql_AbstractionModel
{
    private $driver;
    private $db;
    private $availableDrivers;

    public function __construct($abstract_connection_string = "default", $auto_connect = true)
    {
		$dbsettings = include(dirname(__FILE__) . "/../../../config/database.config.php");

        # driver => className
        $this->availableDrivers = array(
            "Oci8"          => "Drone_Sql_Oracle",
            "Mysqli"        => "Drone_Sql_Mysql",
            "Sqlsrv"        => "Drone_Sql_SQLServer",
            // Drivers for future implementation
            //"Pdo_Mysql"     => "",
            //"Pgsql"         => "",
            //"Pdo_Sqlite"    => "",
            //"Pdo_Sqlite"    => "",
            //"Pdo_Pgsql"     => "",
        );

        $drv = $dbsettings[$abstract_connection_string]["driver"];

        if (array_key_exists($drv, $this->availableDrivers))
        {
            $driver = $this->getAvailableDrivers();

            $this->db = new $driver[$drv](
                $dbsettings[$abstract_connection_string]["host"],
                $dbsettings[$abstract_connection_string]["user"],
                $dbsettings[$abstract_connection_string]["password"],
                $dbsettings[$abstract_connection_string]["dbname"],
                $auto_connect,
                array_key_exists('charset', $dbsettings[$abstract_connection_string]) ? $dbsettings[$abstract_connection_string]["charset"] : ""
            );
        }
        else
            throw new Exception("The Database driver does not exists");
	}

    /* Getters */

    public function getDriver() { return $this->driver; }
    public function getDb() { return $this->db; }
    public function getAvailableDrivers() { return $this->availableDrivers; }
}