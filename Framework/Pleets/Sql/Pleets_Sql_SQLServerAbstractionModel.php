<?php

abstract class Pleets_Sql_SQLServerAbstractionModel
{
	private $conn;		# SQLServer connection

	public function __construct()
	{
		$dbsettings = include(dirname(__FILE__) . "/../../../config/database.sqlserver.config.php");

		$this->connect = new Pleets_Sql_SQLServer(
			$dbsettings["database"]["host"],
			$dbsettings["database"]["user"],
			$dbsettings["database"]["password"],
			$dbsettings["database"]["dbname"]
		);
	}

	/* Getters */
	public function getConn() { return $this->conn; }
}