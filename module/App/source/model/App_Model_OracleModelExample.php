<?php

class App_Model_OracleModelExample extends Pleets_Sql_OracleAbstractionModel
{
    public function consulta()
    {
        $sql = "SELECT * FROM HELP";
        $result = $this->connect->query($sql);
        return $this->connect->toArray(array('encode_utf8' => true));
    }
}