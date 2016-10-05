<?php

/*
 *	Zend Autoloader
 */

function Zf1Loader($name)
{
    $nms = explode("_", $name);
    $parsed_nms = "";

    for ($i = 0; $i < count($nms) - 1; $i++)
    {
        $parsed_nms .= "/" . $nms[$i];
    }

    $vendor_nms = strtolower(array_shift($nms));
    $package = array_shift($nms);

    $file = count($nms) ? array_pop($nms) : $package;

	$class = dirname(dirname(__FILE__)) ."/vendor/zf1/". $vendor_nms . "-" . strtolower($package) ."/library". $parsed_nms . "/" . $file . ".php";

	if (file_exists($class))
		include $class;
}

spl_autoload_register("Zf1Loader");