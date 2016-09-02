<?php

/*
 *	App Autoloader
 */

include("Drone/FileSystem/Drone_FileSystem_IShellCommands.php");
include("Drone/FileSystem/Drone_FileSystem_Shell.php");

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

# load vendor classes
if (file_exists(dirname(__FILE__) . '/../vendor/autoload.php'))
	require_once(dirname(__FILE__) . '/../vendor/autoload.php');