<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

abstract class Drone_Db_Entity
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $connectionIdentifier = "default";

    /**
     * Returns the tableName property
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Returns the connectionIdentifier property
     *
     * @return string
     */
    public function getConnectionIdentifier()
    {
        return $this->connectionIdentifier;
    }

    /**
     * Sets all entity properties passed in the array
     *
     * @param string $tableName
     *
     * @return null
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Sets the connectionIdentifier property
     *
     * @param string $connectionIdentifier
     *
     * @return null
     */
    public function setConnectionIdentifier($connectionIdentifier)
    {
        $this->connectionIdentifier = $connectionIdentifier;
    }

    /**
     * Sets entity properties
     *
     * @param array $data
     *
     * @return null
     */
    public function exchangeArray($data)
    {
        $class = get_class($this);

        foreach ($data as $prop => $value)
        {
            if (property_exists($this, $prop))
                $this->$prop = $value;
            else
                throw new Exception("The property '$prop' does not exists in the class '$class'");
        }
    }
}