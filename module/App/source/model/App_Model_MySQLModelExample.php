<?php

class App_Model_MySQLModelExample extends Pleets_Sql_AbstractionModel
{
    public function myQuery()
    {
        $sql = "SELECT host, user, password FROM mysql.user";
        $result = $this->getDb()->query($sql);
        return $this->getDb()->getArrayResult();
    }
}