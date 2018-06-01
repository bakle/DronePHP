<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

/**
 * TableGateway class
 *
 * This class is a query builder for CRUD (create, read, update, delete)
 */
class Drone_Db_TableGateway_TableGateway
extends Drone_Db_TableGateway_AbstractTableGateway
implements Drone_Db_TableGateway_TableGatewayInterface
{
    /**
     * Entity instance
     *
     * @var Drone_Db_Entity
     */
    private $entity;

    /**
     * Returns the entity
     *
     * @return Drone_Db_Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Constructor
     *
     * @param Drone_Db_Entity $entity
     * @param boolean         $auto_connect
     */
    public function __construct(Drone_Db_Entity $entity, $auto_connect = true)
    {
        parent::__construct($entity->getConnectionIdentifier(), $auto_connect);
        $this->entity = $entity;
    }

    /**
     * Select statement
     *
     * @param array $where
     *
     * @return array With all results
     */
    public function select(Array $where = array())
    {
        $bind_values = array();

        $driver = $this->getDriver()->getDriverName();

        if (count($where))
        {
            $parsed_where = array();

            $k = 0;

            foreach ($where as $key => $value)
            {
                $k++;

                if (is_null($value))
                    $parsed_where[] = "$key IS NULL";
                elseif ($value instanceof Drone_Db_SQLFunction)
                    $parsed_where[] = "$key = " . $value->getStatement();
                elseif (is_array($value))
                {
                    $parsed_in = array();

                    foreach ($value as $in_value)
                    {
                        switch ($driver)
                        {
                            case 'Oci8':
                                $parsed_in[] = ":$k";
                                $bind_values[":$k"] = $in_value;
                                break;

                            case 'Mysqli' || 'Sqlsrv':
                                $parsed_in[] = "?";
                                $bind_values[] = $in_value;
                                break;
                        }

                        $k++;
                    }

                    $parsed_where[] = "$key IN (" . implode(", ", $parsed_in) . ")";
                }
                else
                {
                    switch ($driver)
                    {
                        case 'Oci8':
                            $parsed_where[] = "$key = :$k";
                            $bind_values[":$k"] = $value;
                            break;

                        case 'Mysqli' || 'Sqlsrv':
                            $parsed_where[] = "$key = ?";
                            $bind_values[] = $value;
                            break;
                    }
                }
            }

            $where = "WHERE \r\n\t" . implode(" AND\r\n\t", $parsed_where);
        }
        else
            $where = "";

        $table = $this->entity->getTableName();

        $sql = "SELECT * \r\nFROM {$table}\r\n$where";

        $result = (count($bind_values)) ? $this->getDriver()->getDb()->execute($sql, $bind_values) : $this->getDriver()->getDb()->execute($sql);

        return $this->getDriver()->getDb()->getArrayResult();
    }

    /**
     * Insert statement
     *
     * @param array $data
     *
     * @throws LogicException
     *
     * @return boolean
     */
    public function insert($data)
    {
        if (!count($data))
            throw new LogicException("Missing values for INSERT statement!");

        $bind_values = array();

        $driver = $this->getDriver()->getDriverName();

        $k = 0;

        $null_keys = [];

        foreach ($data as $key => $value)
        {
            $k++;

            # insert NULL values cause problems when BEFORE INSERT triggers are
            # defined to assigns values over fields. For SQLServer is better not
            # pass NULL values
            if ($driver == 'Sqlsrv' && is_null($value))
            {
                $null_keys[] = $key;
                continue;
            }

            if (is_null($value))
                $value = "NULL";
            elseif ($value instanceof Drone_Db_SQLFunction)
                $value = $value->getStatement();
            else {

               switch ($driver)
                {
                    case 'Oci8':
                        $bind_values[":$k"] = $value;
                        $value = ":$k";
                        break;

                    case 'Mysqli' || 'Sqlsrv':
                        $bind_values[] = $value;
                        $value = "?";
                        break;
                }
            }

            $data[$key] = $value;
        }

        foreach ($null_keys as $key)
        {
            unset($data[$key]);
        }

        $cols = implode(",\r\n\t", array_keys($data));
        $vals = implode(",\r\n\t", array_values($data));

        $table = $this->entity->getTableName();

        $sql = "INSERT INTO {$table} \r\n(\r\n\t$cols\r\n) \r\nVALUES \r\n(\r\n\t$vals\r\n)";

        return $this->getDriver()->getDb()->execute($sql, $bind_values);
    }

    /**
     * Update statement
     *
     * @param array $set
     * @param array $where
     *
     * @throws LogicException
     * @throws SecurityException
     *
     * @return boolean
     */
    public function update(Array $set,Array $where)
    {
        $parsed_set = array();

        if (!count($set))
            throw new LogicException("Missing SET arguments!");

        if (!count($set))
            throw new Drone_Exception_SecurityException("You cannot update rows without WHERE clause!");

        $bind_values = array();

        $driver = $this->getDriver()->getDriverName();

        $k = 0;

        foreach ($set as $key => $value)
        {
            $k++;

            if (is_null($value))
                $parsed_set[] = "$key = NULL";
            elseif ($value instanceof Drone_Db_SQLFunction)
                $parsed_set[] = "$key = " . $value->getStatement();
            elseif (is_array($value))
            {
                $parsed_in = array();

                foreach ($value as $in_value)
                {
                    switch ($driver)
                    {
                        case 'Oci8':

                            # [POSSIBLE BUG] - To Future revision (What about non-string values ?)
                            if (is_string($in_value))
                                $parsed_in[] = ":$k";

                            $bind_values[":$k"] = $in_value;
                            break;

                        case 'Mysqli' || 'Sqlsrv':
                            $parsed_in[] = "?";
                            $bind_values[] = $in_value;
                            break;
                    }

                    $k++;
                }

                $parsed_set[] = "$key IN (" . implode(", ", $parsed_in) . ")";
            }
            else
            {
                switch ($driver)
                {
                    case 'Oci8':
                        $parsed_set[] = "$key = :$k";
                        $bind_values[":$k"] = $value;
                        break;

                    case 'Mysqli' || 'Sqlsrv':
                        $parsed_set[] = "$key = ?";
                        $bind_values[] = $value;
                        break;
                }
            }
        }

        $parsed_set_array = $parsed_set;
        $parsed_set = implode(",\r\n\t", $parsed_set);


        $parsed_where = array();

        foreach ($where as $key => $value)
        {
            $k++;

            if (is_null($value))
                $parsed_where[] = "$key IS NULL";
            elseif ($value instanceof Drone_Db_SQLFunction)
                $parsed_where[] = "$key = " . $value->getStatement();
            elseif (is_array($value))
            {
                $parsed_in = array();

                foreach ($value as $in_value)
                {
                    switch ($driver)
                    {
                        case 'Oci8':
                            $parsed_in[] = ":$k";
                            $bind_values[":$k"] = $in_value;
                            break;

                        case 'Mysqli' || 'Sqlsrv':
                            $parsed_in[] = "?";
                            $bind_values[] = $in_value;
                            break;
                    }

                    $k++;
                }

                $parsed_where[] = "$key IN (" . implode(", ", $parsed_in) . ")";
            }
            else
            {
                switch ($driver)
                {
                    case 'Oci8':
                        $parsed_where[] = "$key = :$k";
                        $bind_values[":$k"] = $value;
                        break;

                    case 'Mysqli' || 'Sqlsrv':
                        $parsed_where[] = "$key = ?";
                        $bind_values[] = $value;
                        break;
                }
            }
        }

        $parsed_where = implode(" AND\r\n\t", $parsed_where);

        $table = $this->entity->getTableName();

        $sql = "UPDATE {$table} \r\nSET \r\n\t$parsed_set \r\nWHERE \r\n\t$parsed_where";

        return $this->getDriver()->getDb()->execute($sql, $bind_values);
    }

    /**
     * Delete statement
     *
     * @param array $where
     *
     * @throws SecurityException
     *
     * @return boolean
     */
    public function delete(Array $where)
    {
        if (count($where))
        {
            $parsed_where = array();

            $bind_values = array();

            $driver = $this->getDriver()->getDriverName();

            $k = 0;

            foreach ($where as $key => $value)
            {
                $k++;

                if (is_null($value))
                    $parsed_where[] = "$key IS NULL";
                elseif ($value instanceof Drone_Db_SQLFunction)
                    $parsed_where[] = "$key = " . $value->getStatement();
                elseif (is_array($value))
                {
                    $parsed_in = array();

                    foreach ($value as $in_value)
                    {
                        switch ($driver)
                        {
                            case 'Oci8':
                                $parsed_in[] = ":$k";
                                $bind_values[":$k"] = $in_value;
                                break;

                            case 'Mysqli' || 'Sqlsrv':
                                $parsed_in[] = "?";
                                $bind_values[] = $in_value;
                                break;
                        }

                        $k++;
                    }

                    $parsed_where[] = "$key IN (" . implode(", ", $parsed_in) . ")";
                }
                else
                {
                    switch ($driver)
                    {
                        case 'Oci8':
                            $parsed_where[] = "$key = :$k";
                            $bind_values[":$k"] = $value;
                            break;

                        case 'Mysqli' || 'Sqlsrv':
                            $parsed_where[] = "$key = ?";
                            $bind_values[] = $value;
                            break;
                    }
                }
            }

            $where = "\r\nWHERE \r\n\t" . implode(" AND\r\n\t", $parsed_where);
        }
        else
            throw new Drone_Exception_SecurityException("You cannot delete rows without WHERE clause!. Use TRUNCATE statement instead.");

        $table = $this->entity->getTableName();

        $sql = "DELETE FROM {$table} $where";

        return $this->getDriver()->getDb()->execute($sql, $bind_values);
    }
}