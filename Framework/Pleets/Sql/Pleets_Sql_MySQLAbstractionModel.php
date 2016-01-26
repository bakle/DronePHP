<?php

abstract class Pleets_Sql_MySQLAbstractionModel
{
	private $dbconn;		# MySQL connection

	public function __construct()
	{
		$dbsettings = include(dirname(__FILE__) . "/../../../config/database.mysql.config.php");

		$this->connect = new Pleets_Sql_Mysql(
			$dbsettings["database"]["host"],
			$dbsettings["database"]["user"],
			$dbsettings["database"]["password"],
			$dbsettings["database"]["dbname"]
		);
	}

	/* Getters */
	public function getConn() { return $this->dbconn; }
}