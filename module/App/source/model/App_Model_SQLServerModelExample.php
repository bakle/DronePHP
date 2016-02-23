<?php

class App_Model_SQLServerModelExample extends Pleets_Sql_AbstractionModel
{
    public function consulta()
    {
        $sql = "SELECT * FROM SYS.TABLES";
        $result = $this->getDb()->query($sql);
        return $this->getDb()->toArray(array('encode_utf8' => true));
    }
}