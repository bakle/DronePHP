<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

abstract class Drone_Patch_Uri extends Zend_Uri
{
    /**
     * Create a new Zend_Uri object for a URI.  If building a new URI, then $uri should contain
     * only the scheme (http, ftp, etc).  Otherwise, supply $uri with the complete URI.
     *
     * @param  string $uri       The URI form which a Zend_Uri instance is created
     * @param  string $className The name of the class to use in order to manipulate URI
     * @throws Zend_Uri_Exception When an empty string was supplied for the scheme
     * @throws Zend_Uri_Exception When an illegal scheme is supplied
     * @throws Zend_Uri_Exception When the scheme is not supported
     * @throws Zend_Uri_Exception When $className doesn't exist or doesn't implements Zend_Uri
     * @return Zend_Uri
     * @link   http://www.faqs.org/rfcs/rfc2396.html
     */
    public static function factory($uri = 'http', $className = null)
    {
        // Separate the scheme from the scheme-specific parts
        $uri            = explode(':', $uri, 2);
        $scheme         = strtolower($uri[0]);
        $schemeSpecific = isset($uri[1]) === true ? $uri[1] : '';

        if (strlen($scheme) === 0) {
            // require_once 'Zend/Uri/Exception.php';
            throw new Zend_Uri_Exception('An empty string was supplied for the scheme');
        }

        // Security check: $scheme is used to load a class file, so only alphanumerics are allowed.
        if (ctype_alnum($scheme) === false) {
            // require_once 'Zend/Uri/Exception.php';
            throw new Zend_Uri_Exception('Illegal scheme supplied, only alphanumeric characters are permitted');
        }

        if ($className === null) {
            /**
             * Create a new Zend_Uri object for the $uri. If a subclass of Zend_Uri exists for the
             * scheme, return an instance of that class. Otherwise, a Zend_Uri_Exception is thrown.
             */
            switch ($scheme) {
                case 'http':
                    // Break intentionally omitted
                case 'https':
                    $className = 'Zend_Uri_Http';
                    break;

                case 'mailto':
                    // TODO
                default:
                    // require_once 'Zend/Uri/Exception.php';
                    throw new Zend_Uri_Exception("Scheme \"$scheme\" is not supported");
                    break;
            }
        }

        // require_once 'Zend/Loader.php';
        try {
            # [PATCH] Checks if class exists!
            if (!class_exists($className))
                Zend_Loader::loadClass($className);
        } catch (Exception $e) {
            // require_once 'Zend/Uri/Exception.php';
            throw new Zend_Uri_Exception("\"$className\" not found");
        }

        $schemeHandler = new $className($scheme, $schemeSpecific);

        if (! $schemeHandler instanceof Zend_Uri) {
            // require_once 'Zend/Uri/Exception.php';
            throw new Zend_Uri_Exception("\"$className\" is not an instance of Zend_Uri");
        }

        return $schemeHandler;
    }
}