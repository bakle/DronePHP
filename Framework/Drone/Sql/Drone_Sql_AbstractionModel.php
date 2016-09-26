<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

abstract class Drone_Sql_AbstractionModel
{
    /**
     * Driver identifier
     *
     * @var string
     */
    private $driver;

    /**
     * Handle
     *
     * @var object
     */
    private $db;

    /**
     * All supported drivers
     *
     * @var string
     */
    private $availableDrivers;

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

        # driver => className
        $this->availableDrivers = array(
            "Oci8"          => "Drone_Sql_Oracle",
            "Mysqli"        => "Drone_Sql_MySQL",
            "Sqlsrv"        => "Drone_Sql_SQLServer",
            // Drivers for future implementation
            //"Pdo_Mysql"     => "",
            //"Pgsql"         => "",
            //"Pdo_Sqlite"    => "",
            //"Pdo_Sqlite"    => "",
            //"Pdo_Pgsql"     => "",
        );

        $drv = $dbsettings[$abstract_connection_string]["driver"];
        $dbsettings[$abstract_connection_string]["auto_connect"] = $auto_connect;

        if (array_key_exists($drv, $this->availableDrivers))
        {
            $driver = $this->getAvailableDrivers();

            $this->db = new $driver[$drv]($dbsettings[$abstract_connection_string]);
        }
        else
            throw new Exception("The Database driver does not exists");
	}

    /**
     * Returns the current driver
     *
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Returns the current handle instance
     *
     * @return object
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Returns all supported drivers
     *
     * @return array
     */
    public function getAvailableDrivers()
    {
        return $this->availableDrivers;
    }
}