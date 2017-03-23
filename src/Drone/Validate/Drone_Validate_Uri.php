<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

class Drone_Validate_Uri extends Zend_Validate_Abstract
{
    const INVALID = 'uriInvalid';
    const NOT_URI = 'notUri';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "Invalid type given. String expected",
        self::NOT_URI => "The input does not appear to be a valid Uri",
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is less than max option
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        if (!is_string($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        if (!Zend_Uri::check($value)) {
            $this->_error(self::NOT_URI);
            return false;
        }

        return true;
    }
}
