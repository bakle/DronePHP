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
 * MySQL class
 *
 * This is a database driver class to connect to MySQL
 */
class Drone_Db_Driver_MySQL extends Drone_Db_Driver_AbstractDriver implements Drone_Db_Driver_DriverInterface
{
    /**
     * Constructor for MySql driver
     *
     * @param array $options
     *
     * @throws RuntimeException if connect() found an error
     */
    public function __construct($options)
    {
        if (!array_key_exists("dbchar", $options))
            $options["dbchar"] = "utf8";

        parent::__construct($options);

        $auto_connect = array_key_exists('auto_connect', $options) ? $options["auto_connect"] : true;

        if ($auto_connect)
        {
            $this->dbconn = @new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);

            if ($this->dbconn->connect_errno)
                $this->connect();
        }
    }

    /**
     * Connects to database
     *
     * @throws RuntimeException
     *
     * @return mysqli
     */
    public function connect()
    {
        if (!extension_loaded('mysqli'))
            throw new RuntimeExceptionException("The Mysqli extension is not loaded");

        if (!is_null($this->dbport) && !empty($this->dbport))
            $this->dbconn = @new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname, $this->dbport);
        else
            $this->dbconn = @new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);

        if ($this->dbconn->connect_errno)
        {
            /*
             * Use ever mysqli_connect_errno() and mysqli_connect_error()
             * over $this->dbconn->errno and $this->dbconn->error to prevent
             * the warning message "Property access is not allowed yet".
             */
            throw new Drone_Exception_ConnectionException(mysqli_connect_error(), mysqli_connect_errno());
        }
        else
            $this->dbconn->set_charset($this->dbchar);

        return $this->dbconn;
    }

    /**
     * Excecutes a statement
     *
     * @param string $sql
     * @param array $params
     *
     * @throws RuntimeException
     *
     * @return resource
     */
    public function execute($sql, Array $params = array())
    {
        $this->numRows = 0;
        $this->numFields = 0;
        $this->rowsAffected = 0;

        $this->arrayResult = null;

        # Bound variables
        if (count($params))
        {
            $this->result = $stmt = @$this->dbconn->prepare($sql);

            if (!$stmt)
            {
                $this->error($this->dbconn->errno, $this->dbconn->error);
                throw new RuntimeException("Could not prepare statement");
            }

            $param_values = array_values($params);

            $n_params = count($param_values);
            $bind_values = array();
            $bind_types = "";

            for ($i = 0; $i < $n_params; $i++)
            {
                if (is_string($param_values[$i]))
                    $bind_types .= 's';
                else if(is_float($param_values[$i]))
                    $bind_types .= 'd';
                # [POSSIBLE BUG] - To Future revision (What about non-string and non-decimal types ?)
                else
                    $bind_types .= 's';

                $bind_values[] = '$param_values[' . $i . ']';
            }

            $values = implode(', ', $bind_values);
            eval('$stmt->bind_param(\'' . $bind_types . '\', ' . $values . ');');

            $r = $stmt->execute();

            if ($r)
            {
                if (is_object($stmt) && get_class($stmt) == 'mysqli_stmt')
                {
                    $res = $this->result->get_result();

                    /*
                     * if $res is false then there aren't results.
                     * It is useful to prevent rollback transactions on insert statements because
                     * insert statement do not free results.
                     */
                    if ($res)
                        $this->result = $res;
                }
            }
        }
        else
            $r = $this->result = @$this->dbconn->query($sql);

        if (!$r)
        {
            $this->errorProvider->error($this->dbconn->errno, $this->dbconn->error);
            throw new RuntimeException("Could not execute query");
        }

        if (is_object($this->result) && property_exists($this->result, 'num_rows'))
            $this->numRows = $this->result->num_rows;

        if (is_object($this->result) && property_exists($this->result, 'field_count'))
            $this->numFields = $this->result->field_count;

        if (is_object($this->result) && property_exists($this->result, 'affected_rows'))
            $this->rowsAffected = $this->result->affected_rows;

        if ($this->transac_mode)
            $this->transac_result = is_null($this->transac_result) ? $this->result: $this->transac_result && $this->result;

        return $this->result;
    }

    /**
     * Commit definition
     *
     * @return boolean
     */
    public function commit()
    {
        return $this->dbconn->commit();
    }

    /**
     * Rollback definition
     *
     * @return boolean
     */
    public function rollback()
    {
        return $this->dbconn->rollback();
    }

    /**
     * Begins a transaction in SQLServer
     *
     * @throws RuntimeException
     * @throws LogicException if transaction was already started
     *
     * @return null
     */
    public function beginTransaction()
    {
        parent::beginTransaction();
        $this->dbconn->autocommit(false);
    }

    /**
     * Closes the connection
     *
     * @return boolean
     */
    public function disconnect()
    {
        parent::disconnect();
        return $this->dbconn->close();
    }

    /**
     * Returns an array with the rows fetched
     *
     * @throws LogicException
     *
     * @return array
     */
    protected  function toArray()
    {
        $data = array();

        if ($this->result && !is_bool($this->result))
        {
            while ($row = $this->result->fetch_array(MYSQLI_BOTH))
            {
                $data[] = $row;
            }
        }
        else
            /*
             * "This kind of exception should lead directly to a fix in your code"
             * So much production tests tell us this error is throwed because developers
             * execute toArray() before execute().
             *
             * Ref: http://php.net/manual/en/class.logicexception.php
             */
            throw new LogicException('There are not data in the buffer!');

        $this->arrayResult = $data;

        return $data;
    }

    /**
     * By default __destruct() disconnects to database
     *
     * @return null
     */
    public function __destruct()
    {
        if ($this->dbconn !== false && !is_null($this->dbconn))
            @$this->dbconn->close();
    }
}