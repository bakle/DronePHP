<?php

class Drone_Network_Http
{
	public static function getHost()
	{
		return $_SERVER["HTTP_HOST"];
	}

	public static function getServerPort()
	{
		return $_SERVER["SERVER_PORT"];
	}

	public static function getClientPort()
	{
		return $_SERVER["REMOTE_PORT"];
	}

	public static function getServerIP()
	{
		return $_SERVER["SERVER_ADDR"];
	}

    public static function getClientIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];

        return $_SERVER['REMOTE_ADDR'];
    }
}