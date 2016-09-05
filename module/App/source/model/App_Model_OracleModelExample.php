<?php

class App_Model_OracleModelExample extends Drone_Sql_AbstractionModel
{
    public function consulta()
    {
        $sql = "SELECT * FROM ALL_TABLES WHERE OWNER NOT IN ('SYS', 'SYSTEM', 'OUTLN', 'CTXSYS', 'XDB', 'MDSYS', 'HR', 'APEX_040000')";
        $result = $this->getDb()->query($sql);
        return $this->getDb()->getArrayResult();
    }
}