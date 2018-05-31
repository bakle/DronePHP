<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

class Drone_Error_ErrorCollector
{
    /**
     * Common errors
     *
     * @var array
     */
    private $messagesTemplates = array(
        13 => 'Failed to open stream: \'%file%\', Permission Denied!',
    );

    /**
     * Failure messages
     *
     * @var array
     */
    private $errors = array();

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
}