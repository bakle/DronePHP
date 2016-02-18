<?php

abstract class Pleets_Sql_OracleAbstractionModel
{
	private $db;		# Oracle connection

	public function __construct($abstract_connection_string = "default")
	{
		$dbsettings = include(dirname(__FILE__) . "/../../../config/database.oracle.config.php");

		$this->db = new Pleets_Sql_Oracle(
			$dbsettings[$abstract_connection_string]["host"],
			$dbsettings[$abstract_connection_string]["user"],
			$dbsettings[$abstract_connection_string]["password"],
			$dbsettings[$abstract_connection_string]["dbname"]
		);
	}

	/* Getters */
	public function getDb() { return $this->db; }
}