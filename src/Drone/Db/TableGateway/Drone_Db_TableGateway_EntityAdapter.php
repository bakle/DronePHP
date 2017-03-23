<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

class Drone_Db_TableGateway_EntityAdapter
{
    /**
     * @var Drone_Db_TableGateway $tableGateway
     */
    private $tableGateway;

    /**
     * Constructor
     *
     * @param Drone_Db_TableGateway_TableGateway $tableGateway
     */
    public function __construct(Drone_Db_TableGateway_TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Returns the tableGateway
     *
     * @return Drone_Db_TableGateway
     */
    public function getTableGateway()
    {
        return $this->tableGateway;
    }

    /**
     * Returns a rowset with entity instances
     *
     * @param array $where
     *
     * @return Drone_Db_Entity[]
     */
    public function select($where)
    {
        $result = $this->tableGateway->select($where);

        if (!count($result))
            return $result;

        $array_result = array();

        foreach ($result as $row)
        {
            $filtered_array = array();

            foreach ($row as $key => $value)
            {
                if (is_string($key))
                    $filtered_array[$key] = $value;
            }

            $user_entity = get_class($this->tableGateway->getEntity());

            $entity = new $user_entity();
            $entity->exchangeArray($filtered_array);

            $array_result[] = $entity;
        }

        return $array_result;
    }

    /**
     * Creates a row from an entity or array
     *
     * @param Drone_Db_Entity|array $entity
     *
     * @throws Exception
     * @return boolean
     */
    public function insert($entity)
    {
        if ($entity instanceof Drone_Db_Entity)
            $entity = get_object_vars($entity);
        else if (!is_array($entity))
            throw new Exception("Invalid type given. Drone_Db_Entity or Array expected");

        $result = $this->tableGateway->insert($entity);

        return $result;
    }

    /**
     * Updates an entity
     *
     * @param Drone_Db_Entity|array $entity
     * @param array $where
     *
     * @throws Exception
     * @return boolean
     */
    public function update($entity, $where)
    {
        if ($entity instanceof Drone_Db_Entity)
            $entity = get_object_vars($entity);
        else if (!is_array($entity))
            throw new Exception("Invalid type given. Drone_Db_Entity or Array expected");

        $result = $this->tableGateway->update($entity, $where);

        return $result;
    }

    /**
     * Deletes an entity
     *
     * @param Drone_Db_Entity|array $entity
     *
     * @throws Exception
     * @return boolean
     */
    public function delete($entity)
    {
        if ($entity instanceof Drone_Db_Entity)
            $entity = get_object_vars($entity);
        else if (!is_array($entity))
            throw new Exception("Invalid type given. Drone_Db_Entity or Array expected");

        $result = $this->tableGateway->delete($entity);

        return $result;
    }
}