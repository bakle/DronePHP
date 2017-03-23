<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

class Drone_Db_Driver_DriverAdapter
{
    /**
     * Driver identifier
     *
     * @var string
     */
    private $driver;

    /**
     * Connection resource
     *
     * @var resource
     */
    private $db;

    /**
     * All supported drivers
     *
     * @var array
     */
    private $availableDrivers;

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
     * Returns the connection resource
     *
     * @return resource
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

    /**
     * Constructor
     *
     * @param string  $connection_identifier
     * @param boolean $auto_connect
     *
     * @throws Exception
     */
    public function __construct($connection_identifier = "default", $auto_connect = true)
    {
        # Take connection parameters from configuration file
      $dbsettings = include(dirname(__FILE__) . "/../../../../config/database.config.php");

        # driver => className
        $this->availableDrivers = array(
            "Oci8"   => "Drone_Db_Driver_Oracle",
            "Mysqli" => "Drone_Db_Driver_MySQL",
            "Sqlsrv" => "Drone_Db_Driver_SQLServer",
        );

        $drv = $dbsettings[$connection_identifier]["driver"];
        $dbsettings[$connection_identifier]["auto_connect"] = $auto_connect;

        if (array_key_exists($drv, $this->availableDrivers))
        {
            $driver = $this->getAvailableDrivers();

            $this->driver = $drv;
            $this->db = new $driver[$drv]($dbsettings[$connection_identifier]);
        }
        else
            throw new Exception("The Database driver does not exists");
    }
}