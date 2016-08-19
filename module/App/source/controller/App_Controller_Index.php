<?php

class App_Controller_Index extends Pleets_Mvc_AbstractionController
{
	public function index()
	{
		return array();
	}

	public function inicio()
	{
		$data = array();
		$data["process"] = "success";

		$modelo = new App_Model_MySQLModelExample();
		//$modelo = new App_Model_SQLServerModelExample();
		//$modelo = new App_Model_OracleModelExample();

		try {

			$rows = $modelo->myQuery();
			$data["data"] = $rows;

		} catch (\Exception $e) {

			$data["message"] = $e->getMessage();
			$data["process"] = "error";

			return $data;
		}

		return $data;
	}
}