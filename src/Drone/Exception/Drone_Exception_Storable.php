<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

/**
 * Storable class
 *
 * This is a helper tait that provides essential methods to store Exceptions.
 * All Exceptions that use this class as a provider, will be stored with the store() method.
 */
class Drone_Exception_Storable extends Exception
{
    /**
     * Local file when exceptions will be stored
     *
     * @var string
     */
    protected $outputFile;

    /**
     * Error collector
     *
     * @var Drone_Error_ErrorCollector
     */
    protected $errorProvider;

    /**
     * Returns the outputFile attribute
     *
     * @return string
     */
    public function getOutputFile()
    {
        return $this->outputFile;
    }

    /**
     * Sets outputFile attribute
     *
     * @param string $value
     *
     * @return null
     */
    public function setOutputFile($value)
    {
        return $this->outputFile = $value;
    }

    /**
     * Constructor
     *
     * @param array $data
     *
     * @return null
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorProvider = new Drone_Error_ErrorCollector();
    }

    /**
     * Stores the exception
     *
     * By default exceptions are stored in a JSON file << $this->outputFile >>
     *
     * @return string|boolean
     */
    public function store()
    {
        $storage = new Drone_Exception_Storage($this->outputFile);

        $st = $storage->store($this);

        if (!$st)
        {
            $_errors = $st->getErrors();

            foreach ($_errors as $errno => $error)
            {
                $this->errorProvider->error($errno, $error);
            }
        }

        return $st;
    }
}