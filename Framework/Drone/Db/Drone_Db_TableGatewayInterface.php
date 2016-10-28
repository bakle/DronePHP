<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

interface Drone_Db_TableGatewayInterface
{
   public function select();
   public function insert($data);
   public function update($set, $where);
   public function delete($where);
}