<?php

class App_Controller_Db extends AbstractionController
{
	public function mysql()
	{
		$data = array();
		$data["process"] = "success";

		$model = new App_Model_MySQLModelExample();

		try {

			$rows = $model->myQuery();
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

		$model = new App_Model_OracleModelExample();

		try {

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

		$model = new App_Model_SQLServerModelExample();

		try {

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