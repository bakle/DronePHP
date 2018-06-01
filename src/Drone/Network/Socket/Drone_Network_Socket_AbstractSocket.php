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
 * AbstractSocket class
 *
 * This class defines some standard functions to interact with sockets.
 * Client and Server behaviors must be implemented.
 */
class Drone_Network_Socket_AbstractSocket
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var integer
     */
    protected $port;

    /**
     * Socket resource
     *
     * @var resource|boolean
     */
    protected $socket;

    /**
     * Error collector
     *
     * @var Drone_Error_ErrorCollector
     */
    protected $errorProvider;

    /**
     * Returns the host attribute
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Returns the port attribute
     *
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Returns the socket attribute
     *
     * @return resource|boolean
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * Sets host attribute
     *
     * @param string $host
     *
     * @return null
     */
    public function setHost($host)
    {
        return $this->host = $host;
    }

    /**
     * Sets port attribute
     *
     * @param integer $port
     *
     * @return null
     */
    public function setPort($port)
    {
        return $this->port = $port;
    }

    /**
     * Driver Constructor (here socket will be created!)
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

        if (!($this->socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP)))
        {
            $errno = socket_last_error();
            $this->errorProvider->error($errno, socket_strerror($errno));

            throw new RuntimeException("Could not create the socket");
        }

        $this->errorProvider->errorProvider = new Drone_Error_ErrorCollector();
    }

    /**
     * Binds the socket
     *
     * @return bool
     */
    public function bind()
    {
        if (!($bind = @socket_bind($this->socket, $this->host, $this->port)))
        {
            $errno = socket_last_error();
            $this->errorProvider->error($errno, socket_strerror($errno));
            return false;
        }

        return $bind;
    }

    /**
     * Closes the socket
     *
     * @return bool
     */
    public function close()
    {
        return socket_close($this->socket);
    }
}