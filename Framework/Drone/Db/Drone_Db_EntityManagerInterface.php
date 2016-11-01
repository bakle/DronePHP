<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

interface Drone_Db_EntityManagerInterface
{
   public function select($where);
   public function insert($model);
   public function update($model, $where);
   public function delete($model);
}