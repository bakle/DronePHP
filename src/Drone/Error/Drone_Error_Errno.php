<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

class Drone_Error_Errno
{
    /**
     * Common file errors
     *
     * @var integer
     */
	const FILE_PERMISSION_DENIED = 1;
	const FILE_NOT_FOUND         = 2;
	const FILE_EXISTS            = 3;
	const NOT_DIRECTORY          = 4;

    /**
     * Common JSON errors
     *
     * @var integer
     */
    const JSON_DECODE_ERROR = 10;
    const JSON_ENCODE_ERROR = 11;

    /**
     * Common database errors
     *
     * @var integer
     */
    const DB_TRANSACTION_STARTED     = 20;
    const DB_TRANSACTION_NOT_STARTED = 21;
    const DB_TRANSACTION_EMPTY       = 22;
}