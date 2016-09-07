<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

abstract class Drone_Db_Entity
{
    /**
     * @var string
     */
    private $tableName;

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
     * Sets all entity properties passed in the array
     *
     * @param string $tableName
     *
     * @param null
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
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
        foreach ($data as $prop => $value)
        {
            if (property_exists($this, $prop))
                $this->$prop = $value;
            else
                throw new Exception("The property '$prop' does not exists in the class!");
        }
    }
}