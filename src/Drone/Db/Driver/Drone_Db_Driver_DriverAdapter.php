<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

class Drone_Db_Driver_DriverAdapter
{
    /**
     * Driver identifier
     *
     * @var string
     */
    private $driverName;

    /**
     * Connection resource
     *
     * @var resource|object
     */
    private $db;

    /**
     * All supported drivers
     *
     * @var array
     */
    private $availableDrivers;

    /**
     * Returns the current driver identifier
     *
     * @return string
     */
    public function getDriverName()
    {
        return $this->driverName;
    }

    /**
     * Returns the connection resource or object
     *
     * @return resource|object
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
     * @param string|array  $connection_identifier
     * @param boolean       $auto_connect
     *
     * @throws Exception
     * @throws RuntimeException
     */
    public function __construct($connection_identifier = "default", $auto_connect = true)
    {
        # driver => className
        $this->availableDrivers = array(
            "Oci8"   => "Drone_Db_Driver_Oracle",
            "Mysqli" => "Drone_Db_Driver_MySQL",
            "Sqlsrv" => "Drone_Db_Driver_SQLServer",
        );

        if (gettype($connection_identifier) == 'array')
            $connection_array = $connection_identifier;
        else
        {
            # Take connection parameters from configuration file
            if (!file_exists("config/database.config.php"))
                throw new \RuntimeException("config/data.base.config.php is missing!");

            $dbsettings = include("config/database.config.php");
            $connection_array = $dbsettings[$connection_identifier];
        }

        $drv = $connection_array["driver"];
        $connection_array["auto_connect"] = $auto_connect;

        if (array_key_exists($drv, $this->availableDrivers))
        {
            $driver = $this->getAvailableDrivers();

            $this->driverName = $drv;
            $this->db = new $driver[$drv]($connection_array);
        }
        else
            throw new RuntimeException("The Database driver does not exists");
    }
}