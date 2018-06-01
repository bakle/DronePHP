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
 * This interface could be used to define what methods should implement a driver class
 */
interface Drone_Db_Driver_DriverInterface
{
    /**
     * Connects to a database
     *
     * This method would use connect()
     */
    public function connect();

    /**
     * Reconnects to the database
     *
     * This method would use connect()
     */
    public function reconnect();

    /**
     * Executes a statement
     *
     *@param string $sql
     */
    public function execute($sql);

    /**
     * Does commit to current statements
     */
    public function commit();

    /**
     * Does rollback to current statements
     */
    public function rollback();

    /**
     * Begins a transaction
     */
    public function beginTransaction();

    /**
     * Closes a transaction
     */
    public function endTransaction();

    /**
     * Disconnects to database
     */
    public function disconnect();
}