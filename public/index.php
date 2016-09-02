<?php

chdir(dirname(dirname(__FILE__)));

// Set localtime zone
date_default_timezone_set("America/Bogota");

// Memory limit
ini_set("memory_limit","256M");

// Directories to load
$directories = array("library");

// General library loader
function LibraryLoader($name)
{
	global $directories;

	foreach ($directories as $dir)
	{
		$class =  dirname(dirname(__FILE__)) . "/$dir/". str_replace('\\', '/', $name) . ".php";

		if (file_exists($class))
		{
			include $class;
			break;
		}
	}
}

// Load libraries
spl_autoload_register("LibraryLoader");

// Run application
require_once("Framework/autoload.php");
$mvc = new Drone_Mvc_Application(include "config/application.config.php");
$mvc->run();