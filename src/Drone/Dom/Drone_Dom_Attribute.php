<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

class Drone_Dom_Attribute
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
   private $value;

    /**
     * Gets the name attribute
     *
     * @return array
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the value attribute
     *
     * @return array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the value attribute
     *
     * @param mixed $value
     *
     * @return null
     */
   public function setValue($value)
   {
       $this->value = $value;
   }

    /**
     * Constructor
     *
     * @param string $name
     * @param mixed  $value
     */
   public function __construct($name, $value = null)
   {
        $this->name = $name;
       $this->value = $value;
   }
}