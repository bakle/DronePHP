<?php

class App_Model_SQLServerModelExample extends Pleets_Sql_SQLServerAbstractionModel
{
    public function consulta()
    {
        $sql = "SELECT * FROM SYS.TABLES";
        $result = $this->connect->query($sql);
        return $this->connect->toArray(array('encode_utf8' => true));
    }
}