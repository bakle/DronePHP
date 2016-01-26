<?php

class App_Model_MySQLModelExample extends Pleets_Sql_MySQLAbstractionModel
{
    public function consulta()
    {
        $sql = "SELECT * FROM mysql.user";
        $result = $this->connect->query($sql);
        return $this->connect->toArray();
    }
}