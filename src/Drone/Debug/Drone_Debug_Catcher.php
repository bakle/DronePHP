<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

class Drone_Debug_Catcher
{
    /**#@+
     * Transaction constants
     * @var string
     */
    const PERMISSION_DENIED = 'permissionDenied';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messagesTemplates = [
        self::PERMISSION_DENIED => 'Failed to open stream: %file%, Permission Denied!'
    ];

    /**
     * Output filename
     *
     * Catched events/vars stored as json format
     *
     * @var string
     */
    protected $output;

    /**
     * Failure messages
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Returns the output filename
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Returns an array with all failure messages
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Sets output attribute
     *
     * @param string
     *
     * @return null
     */
    public function setOutput($value)
    {
        return $this->output = $value;
    }

    /**
     * Adds an error
     *
     * @param string $code
     * @param string $message
     *
     * @return null
     */
    protected function error($code, $message = null)
    {
        if (!array_key_exists($code, $this->errors))
            $this->errors[$code] = (array_key_exists($code, $this->messagesTemplates))
                ?
                    is_null($message)
                        ? $this->messagesTemplates[$code]
                        : preg_replace('/%[a-zA-Z]*%/', $message, $this->messagesTemplates[$code])
                : $message;
    }

    /**
     * Returns the exception id stored
     *
     * @return string
     */
    public function storeException(Exception $e)
    {
        $id = time();

        $data = (file_exists($this->output)) ? json_decode(file_get_contents($this->output), true) : array();
        $data[$id] = array(
            "message" => $e->getMessage(),
            "object"  => serialize($e)
        );

        $hd = @fopen($this->output, "w");

        if (!$hd || !@fwrite($hd, json_encode($data)))
        {
            $this->error(self::PERMISSION_DENIED, $this->output);
            return false;
        }

        @fclose($hd);

        return $id;
    }
}