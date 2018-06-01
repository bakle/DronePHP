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
 * AbstractDriver Class
 *
 * This class defines standard behavior for database drivers
 */
abstract class Drone_Db_Driver_AbstractDriver
{
    /**
     * @var string
     */
    protected $dbhost;

    /**
     * @var string
     */
    protected $dbuser;

    /**
     * @var string
     */
    protected $dbpass;

    /**
     * @var string
     */
    protected $dbname;

    /**
     * @var string
     */
    protected $dbchar;

    /**
     * @var integer
     */
    protected $dbport;

    /**
     * Connection identifier
     *
     * @var resource|boolean
     */
    protected $dbconn;

    /**
     * Rows returned on execute() method
     *
     * @var integer
     */
    protected $numRows;

    /**
     * Fields returned on execute() method
     *
     * @var integer
     */
    protected $numFields;

    /**
     * Rows affected returned on execute() method
     *
     * @var integer
     */
    protected $rowsAffected;

    /**
     * Statement handle
     *
     * @var resource|boolean
     */
    protected $result;

    /**
     * Data stored in select statements
     *
     * @var array
     */
    protected $arrayResult;

    /**
     * Defines if consecutive querys are part of a transaction
     *
     * @var boolean
     */
    protected $transac_mode = false;

    /**
     * @var boolean
     */
    protected $transac_result = null;

    /**
     * Error collector
     *
     * @var Drone_Error_ErrorCollector
     */
    protected $errorProvider;

    /**
     * Returns the dbhost attribute
     *
     * @return string
     */
    public function getDbhost()
    {
        return $this->dbhost;
    }

    /**
     * Returns the dbuser attribute
     *
     * @return string
     */
    public function getDbuser()
    {
        return $this->dbuser;
    }

    /**
     * Returns the dbname attribute
     *
     * @return string
     */
    public function getDbname()
    {
        return $this->dbname;
    }

    /**
     * Returns the dbchar attribute
     *
     * @return string
     */
    public function getDbchar()
    {
        return $this->dbchar;
    }

    /**
     * Returns the dbport attribute
     *
     * @return string
     */
    public function getDbport()
    {
        return $this->dbport;
    }

    /**
     * Returns the numRows attribute
     *
     * @return integer
     */
    public function getNumRows()
    {
        return $this->numRows;
    }

    /**
     * Returns the numFields attribute
     *
     * @return integer
     */
    public function getNumFields()
    {
        return $this->numFields;
    }

    /**
     * Returns the rowsAffected attribute
     *
     * @return integer
     */
    public function getRowsAffected()
    {
        return $this->rowsAffected;
    }

    /**
     * Returns an array with all results of the last execute statement
     *
     * @throws LogicException if toArray() is executed before execute()
     *
     * @return array
     */
    public function getArrayResult()
    {
        if ($this->arrayResult)
            return $this->arrayResult;

        return $this->toArray();
    }

    /**
     * Sets dbhost attribute
     *
     * @param string $value
     *
     * @return null
     */
    public function setDbhost($value)
    {
        return $this->dbhost = $value;
    }

    /**
     * Sets dbuser attribute
     *
     * @param string $value
     *
     * @return null
     */
    public function setDbuser($value)
    {
        return $this->dbuser = $value;
    }

    /**
     * Sets dbpass attribute
     *
     * @param string $value
     *
     * @return null
     */
    public function setDbpass($value)
    {
        return $this->dbpass = $value;
    }

    /**
     * Sets dbname attribute
     *
     * @param string $value
     *
     * @return null
     */
    public function setDbname($value)
    {
        return $this->dbname = $value;
    }

    /**
     * Sets dbchar attribute
     *
     * @param string
     *
     * @return null
     */
    public function setDbchar($value)
    {
        return $this->dbchar = $value;
    }

    /**
     * Sets dbport attribute
     *
     * @param integer $value
     *
     * @return null
     */
    public function setDbport($value)
    {
        return $this->dbport = $value;
    }

    /**
     * Driver Constructor
     *
     * All modifiable attributes (i.e. with setter method) can be passed as key
     *
     * @throws RuntimeException
     *
     * @param array $options
     */
    public function __construct($options)
    {
        foreach ($options as $option => $value)
        {
            if (property_exists(__CLASS__, strtolower($option)) && method_exists($this, 'set'.$option))
                $this->{'set'.$option}($value);
        }

        $this->errorProvider->errorProvider = new Drone_Error_ErrorCollector();
   }

    /**
     * Returns true if there is a stablished connection
     *
     * @return boolean
     */
    public function isConnected()
    {
        return (is_resource($this->dbconn) || is_object($this->dbconn));
    }

    /**
     * Abstract connect
     *
     * @throws RuntimeException
     *
     * @return resource|object
     */
    public function connect() {}

    /**
     * Abstract execute
     *
     * @param string $sql
     * @param array $params to bind
     *
     * @throws RuntimeException
     *
     * @return resource|object
     */
    public abstract function execute($sql, Array $params = array());

    /**
     * Reconnects to the database
     *
     * @throws RuntimeException
     * @throws LogicException
     *
     * @return resource|object
     */
    public function reconnect()
    {
        if (!$this->isConnected())
            throw new LogicException("Connection was not established, however it's trying to reconnect");

        $this->disconnect();
        return $this->connect();
    }

    /**
     * Abstract commit
     *
     * @return boolean
     */
    public function commit() {}

    /**
     * Abstract rollback
     *
     * @return boolean
     */
    public function rollback() {}

    /**
     * Abstract disconnect
     *
     * @throws LogicException
     *
     * @return boolean
     */
    public function disconnect()
    {
        if (!$this->isConnected())
            throw new LogicException("Connection was not established, however it's trying to disconnect");
    }

    /**
     * Defines start point of a transaction
     *
     * @throws RuntimeException
     * @throws LogicException if transaction was already started
     *
     * @return null
     */
    public function beginTransaction()
    {
        if (!$this->isConnected())
            $this->connect();

        if ($this->transac_mode)
            throw new LogicException(self::DB_TRANSACTION_STARTED);

        $this->transac_mode = true;
    }

    /**
     * Defines end point of a transaction
     *
     * @throws RuntimeException
     * @throws LogicException if transaction has not been started or it's empty
     *
     * @return null
     */
    public function endTransaction()
    {
        if (!$this->transac_mode)
            throw new LogicException(self::DB_TRANSACTION_NOT_STARTED);

        if (is_null($this->transac_result))
            throw new \LogicException(self::DB_TRANSACTION_EMPTY);

        if ($this->transac_result)
            $this->commit();
        else
            $this->rollback();

        $this->result = $this->transac_result;

        $this->transac_result = null;
        $this->transac_mode = false;
    }

    /**
     * Abstract result set
     *
     * By default all Drivers must be implement toArray() function.
     * The toArray() method must take the latest result from an execute statement
     * and convert it to an array. To get this array getArrayResult() has been implemented.
     *
     * @throws LogicException if execute() was not executed before this.
     *
     * @return array
     */
    protected abstract function toArray();

    /**
     * Excecutes multiple statements as transaction
     *
     * @param array $querys
     *
     * @throws RuntimeException
     * @throws LogicException
     *
     * @return null
     */
    public function transaction(Array $querys)
    {
        $this->beginTransaction();

        foreach ($querys as $sql)
        {
            $this->execute($sql);
        }

        $this->endTransaction();
    }
}