<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

class Drone_Db_TableGateway extends Drone_Sql_AbstractionModel implements Drone_Db_TableGatewayInterface
{
    /**
     * Entity instance
     *
     * @var Drone_Db_Entity
     */
    private $entity;

    /**
     * Constructor
     *
     * @param string $entity
     *
     * @return null
     */
    public function __construct(Entity $entity)
    {
        parent::__construct("default", true);
        $this->entity = $entity;
    }

    /**
     * Select statement
     *
     * @param array $where
     *
     * @return array
     */
    public function select($where = array())
    {
        if (count($where))
        {
            $parsed_where = array();

            foreach ($where as $key => $value)
            {
                if (is_string($value))
                    $parsed_where[] = "$key = '$value'";
                else
                    $parsed_where[] = "$key = $value";
            }

            $where = "WHERE " . implode(" AND ", $parsed_where);
        }
        else
            $where = "";

        $table = $this->entity->getTableName();

        $sql = "SELECT *
                FROM {$table} $where";

        $result = $this->getDb()->query($sql);
        return $this->getDb()->getArrayResult();
    }

    /**
     * Insert statement
     *
     * @param array $where
     *
     * @return boolean
     */
    public function insert($data)
    {
        $cols = implode(", ", array_keys($row));
        $vals = array_values($row);

        $parsed_vals = array();

        foreach ($vals as $value)
        {
            $parsed_vals[] = (is_string($value)) ? "'$value'" : $value;
        }

        $vals = implode(", ", array_values($parsed_vals));

        $table = $this->entity->getTableName();

        $sql = "INSERT INTO {$table}
                ($cols) VALUES ($vals)";

        return $this->getDb()->query($sql);
    }

    /**
     * Update statement
     *
     * @param array $set
     * @param array $where
     *
     * @return boolean
     */
    public function update($set, $where)
    {
        $parsed_set = array();

        foreach ($set as $key => $value)
        {
            if (is_string($value))
                $value = "'$value'";

            $parsed_set[] = "$key = $value";
        }

        $parsed_set = implode(", ", $parsed_set);


        $parsed_where = array();

        foreach ($where as $key => $value)
        {
            if (is_string($value))
                $value = "$key = '$value'";

            $parsed_where[] = "$value";
        }

        $parsed_where = implode(" AND ", $parsed_where);

        $table = $this->entity->getTableName();

        $sql = "UPDATE {$table}
                SET $parsed_set
                WHERE $parsed_where";

        return $this->getDb()->query($sql);
    }

    /**
     * Delete statement
     *
     * @param array $where
     *
     * @return boolean
     */
    public function delete($where)
    {
        if (count($where))
        {
            $parsed_where = array();

            foreach ($where as $key => $value)
            {
                if (is_string($value))
                    $condition = "$key = '$value'";

                $parsed_where[] = "$key = $value";
            }

            $where = "WHERE " . implode(" AND ", $parsed_where);
        }
        else
            throw new Exception("You cannot delete rows without WHERE clause!");

        $sql = "DELETE
                FROM {$this->tableName} $where";

        return $this->getDb()->query($sql);
    }
}