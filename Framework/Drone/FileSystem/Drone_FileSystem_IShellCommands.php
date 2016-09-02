<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/Drone
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

interface Drone_FileSystem_IShellCommands
{
   public function pwd();
   public function ls($path);
   public function cd($path);
   public function touch($file);
   public function rm($file);
   public function cp($source, $dest);
   public function mv($oldfile, $newfile);
   public function mkdir($dir, $dest);
   public function rmdir($dir);
}
