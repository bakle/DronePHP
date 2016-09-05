<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

abstract class Drone_Db_Entity
{
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