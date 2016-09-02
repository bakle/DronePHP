<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/Drone
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

abstract class Drone_Mvc_AbstractionModel
{
	private $entityManager;

	public function __construct()
	{
		$this->entityManager = include("bootstrap.php");
	}

	/* Getters */
	public function getEntityManager() { return $this->entityManager; }

	public function __destruct()
	{
		// $this->getEntityManager()->flush();
	}
}