<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

class Drone_Db_Driver_Oracle extends Drone_Db_Driver_AbstractDriver implements Drone_Db_Driver_DriverInterface
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
     * @throws RuntimeException
     */
    public function __construct($options)
    {
        if (!array_key_exists("dbchar", $options))
            $options["dbchar"] = "AL32UTF8";

        parent::__construct($options);

        $auto_connect = array_key_exists('auto_connect', $options) ? $options["auto_connect"] : true;

        if ($auto_connect)
            $this->connect();

        $this->errorProvider->errorProvider = new Drone_Error_ErrorCollector();
    }

    /**
     * Connects  to database
     *
     * @throws RuntimeException
     *
     * @return boolean
     */
    public function connect()
    {
        if (!extension_loaded('oci8'))
            throw new RuntimeException("The Oci8 extension is not loaded");

        $connection_string = (is_null($this->dbhost) || empty($this->dbhost)) ? $this->dbname : $this->dbhost ."/". $this->dbname;
        $this->dbconn = @oci_connect($this->dbuser,  $this->dbpass, $connection_string, $this->dbchar);

        if ($this->dbconn === false)
        {
            $error = oci_error();
            $this->errorProvider->error($error["code"], $error["message"]);

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

        $this->result = $stid = oci_parse($this->dbconn, $sql);

        # Bound variables
        if (count($params))
        {
            $param_keys   = array_keys($params);
            $param_values = array_values($params);

            for ($i = 0; $i < count($params); $i++)
            {
                oci_bind_by_name($stid, $param_keys[$i], $param_values[$i], -1);
            }
        }

        $r = ($this->transac_mode) ? @oci_execute($stid, OCI_NO_AUTO_COMMIT) : @oci_execute($stid,  OCI_COMMIT_ON_SUCCESS);

        if (!$r)
        {
            $this->errorProvider->error($error["code"], $error["message"]);
            return false;
        }

        # This should be before of getArrayResult() because oci_fetch() is incremental.
        $this->rowsAffected = oci_num_rows($stid);

        $rows = $this->getArrayResult();

        $this->numRows = count($rows);
        $this->numFields = oci_num_fields($stid);

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
        return oci_commit($this->dbconn);
    }

    /**
     * Rollback definition
     *
     * @return boolean
     */
    public function rollback()
    {
        return oci_rollback($this->dbconn);
    }

    /**
     * Closes the connection
     *
     * @return boolean
     */
    public function disconnect()
    {
        if ($this->dbconn)
            return oci_close($this->dbconn);

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
            while ( ($row = @oci_fetch_array($this->result, OCI_BOTH + OCI_RETURN_NULLS)) !== false )
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
            oci_close($this->dbconn);
    }
}
