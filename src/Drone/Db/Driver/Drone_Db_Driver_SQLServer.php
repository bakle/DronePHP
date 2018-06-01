<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

class Drone_Db_Driver_SQLServer extends Drone_Db_Driver_AbstractDriver implements Drone_Db_Driver_DriverInterface
{
    /**
     * Error collector
     *
     * @var Drone_Error_ErrorCollector
     */
    protected $errorProvider;

    /**
     * Constructor for Oracle driver
     *
     * @param array $options
     *
     * @throws Exception
     */
    public function __construct($options)
    {
        if (!array_key_exists("dbchar", $options))
            $options["dbchar"] = "UTF-8";

        parent::__construct($options);

        $auto_connect = array_key_exists('auto_connect', $options) ? $options["auto_connect"] : true;

        if ($auto_connect)
            $this->connect();

        $this->errorProvider->errorProvider = new Drone_Error_ErrorCollector();
    }

    /**
     * Connects to database
     *
     * @throws RuntimeException
     *
     * @return boolean
     */
    public function connect()
    {
        if (!extension_loaded('sqlsrv'))
            throw new RuntimeException("The Sqlsrv extension is not loaded");

        $db_info = array("Database" => $this->dbname, "UID" => $this->dbuser, "PWD" => $this->dbpass, "CharacterSet" => $this->dbchar);
        $this->dbconn = sqlsrv_connect($this->dbhost, $db_info);

        if ($this->dbconn === false)
        {
            $errors = sqlsrv_errors();

            foreach ($errors as $error)
            {
                $this->errorProvider->error($error["code"], $error["message"]);
            }

            return false;
        }

        return true;
    }

    /**
     * Excecutes a statement
     *
     * @return boolean
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
            $this->result = sqlsrv_prepare($this->dbconn, $sql, $params);
            $r = sqlsrv_execute($this->result);
        }
        else
            $r = $this->result = sqlsrv_query($this->dbconn, $sql, $params, array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

        if (!$r)
        {
            $errors = sqlsrv_errors();

            foreach ($errors as $error)
            {
                $this->errorProvider->error($error["code"], $error["message"]);
            }

            return false;
        }

        $this->getArrayResult();

        $this->numRows = sqlsrv_num_rows($this->result);
        $this->numFields = sqlsrv_num_fields($this->result);
        $this->rowsAffected = sqlsrv_rows_affected($this->result);

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
        return sqlsrv_commit($this->dbconn);
    }

    /**
     * Rollback definition
     *
     * @return boolean
     */
    public function rollback()
    {
        return sqlsrv_rollback($this->dbconn);
    }

    /**
     * Defines start point of a transaction
     *
     * @return boolean
     */
    public function beginTransaction()
    {
        if (sqlsrv_begin_transaction($this->dbconn) === false)
        {
            $errors = sqlsrv_errors();

            foreach ($errors as $error)
            {
                $this->errorProvider->error($error["code"], $error["message"]);
            }

            return false;
        }

        return parent::beginTransaction();
    }

    /**
     * Closes the connection
     *
     * @return boolean
     */
    public function disconnect()
    {
        if ($this->dbconn)
            return sqlsrv_close($this->dbconn);

        return true;
    }

    /**
     * Returns an array with the rows fetched
     *
     * @throws LogicException
     *
     * @return array
     */
    protected function toArray()
    {
        $data = array();

        if ($this->result)
        {
            while ($row = sqlsrv_fetch_array($this->result))
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

    public function __destruct()
    {
        if ($this->dbconn)
            sqlsrv_close($this->dbconn);
    }
}