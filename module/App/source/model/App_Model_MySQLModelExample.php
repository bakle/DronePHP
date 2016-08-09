<?php

class App_Model_MySQLModelExample extends Pleets_Sql_AbstractionModel
{
    public function consulta()
    {
        $sql = "SELECT * FROM mysql.user";
        $result = $this->getDb()->query($sql);
        return $this->getDb()->getArrayResult();
    }
}