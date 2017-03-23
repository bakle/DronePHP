<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

interface Drone_Db_TableGateway_TableGatewayInterface
{
   public function select();
   public function insert($data);
   public function update($set, $where);
   public function delete($where);
}