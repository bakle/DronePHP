<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

abstract class Drone_Mvc_AbstractionModel
{
    /**
     * @var mixed
     */
	private $entityManager;

    /**
     * Returns the entity manager
     *
     * @return mixed
     */
	public function getEntityManager()
	{
		return $this->entityManager;
	}

    /**
     * Constructor
     */
	public function __construct()
    {
		$this->entityManager = include("bootstrap.php");
    }
}