<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

interface Drone_Db_TableGateway_TableGatewayInterface
{
   public function select();
   public function insert(Array $data);
   public function update(Array $set, Array $where);
   public function delete(Array $where);
}