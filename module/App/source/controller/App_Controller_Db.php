<?php

class App_Controller_Db extends Drone_Mvc_AbstractionController
{
	private $mysqlUserTable;

	public function getMysqlUserTable()
	{
		if (!is_null($this->mysqlUserTable))
			return $this->mysqlUserTable;

		$tableGateway = new Drone_Db_TableGateway();
		$tableGateway->bind("mysql.user");

		$entity = new App_Model_MysqlUser();
		$this->mysqlUserTable = new App_Model_MysqlUserTable($entity, $tableGateway);

		return $this->mysqlUserTable;
	}

	public function mysql()
	{
		$data = array();
		$data["process"] = "success";

		try {

			$model = new MySQLModelExample();

			# no entity
			# $rows = $model->myQuery();

			# entity
			$rows = $this->getMysqlUserTable()->fetch();

			$data["data"] = $rows;

		} catch (\Exception $e) {

			$data["message"] = $e->getMessage();
			$data["process"] = "error";

			return $data;
		}

		return $data;
	}

	public function oracle()
	{
		$data = array();
		$data["process"] = "success";

		try {

			$model = new App_Model_OracleModelExample();

			$rows = $model->myQuery();
			$data["data"] = $rows;

		} catch (\Exception $e) {

			$data["message"] = $e->getMessage();
			$data["process"] = "error";

			return $data;
		}

		return $data;
	}

	public function sqlserver()
	{
		$data = array();
		$data["process"] = "success";

		try {

			$model = new App_Model_SQLServerModelExample();

			$rows = $model->myQuery();
			$data["data"] = $rows;

		} catch (\Exception $e) {

			$data["message"] = $e->getMessage();
			$data["process"] = "error";

			return $data;
		}

		return $data;
	}
}