<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

/**
 * DriverInterface Interface
 *
 * This interface defines the four basic operations for persistent storage (CRUD)
 */
interface Drone_Db_TableGateway_TableGatewayInterface
{
    /**
     * Select statement
     *
     * @param array $where
     */
	public function select(Array $where);

    /**
     * Insert statement
     *
     * @param array $data
     */
	public function insert(Array $data);

    /**
     * Update statement
     *
     * @param array $set
     * @param array $where
     */
	public function update(Array $set, Array $where);

    /**
     * Delete statement
     *
     * @param array $where
     */
	public function delete(Array $where);
}