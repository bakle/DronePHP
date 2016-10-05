<?php

/*
 *	App Autoloader
 */

require_once("Drone/FileSystem/Drone_FileSystem_ShellInterface.php");
require_once("Drone/FileSystem/Drone_FileSystem_Shell.php");

function FrameworkLoader($name)
{

    $nms = explode("_", $name);
    $parsed_nms = "";

    for ($i = 0; $i < count($nms) - 1; $i++)
    {
        $parsed_nms .= "/" . $nms[$i];
    }

	$class = dirname(__FILE__) . $parsed_nms . "/" . $name . ".php";

	if (file_exists($class))
		include $class;
}

spl_autoload_register("FrameworkLoader");