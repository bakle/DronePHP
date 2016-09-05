<?php

class App_Model_MysqlUserTable extends Drone_Db_TableGateway
{
	private $tableGateway;
	private $entity;

	public function __construct(Drone_Db_Entity $entity, Drone_Db_TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
		$this->entity = $entity;
	}

	public function fetch()
	{
		$result = $this->tableGateway->select();
		return $result;
	}
}