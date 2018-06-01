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
 * Storage class
 *
 * This is a helper class to store exceptions
 */
class Drone_Exception_Storage extends Exception
{
    /**
     * Output file
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
     * Constructor
     *
     * @param string $outputFile
     *
     * @return null
     */
    public function __construct($outputFile)
    {
        $this->outputFile = $outputFile;
        $this->errorProvider = new Drone_Error_ErrorCollector();
    }

    /**
     * Stores the exception serializing the object
     *
     * @param Exception $exception
     * @param string    $outputFile
     *
     * @return string|boolean
     */
    public function store(Exception $exception)
    {
        # simple way to generate a unique id
        $id = time() . uniqid();

        $data = array();

        if (file_exists($this->outputFile))
        {
            $string = file_get_contents($this->outputFile);

            if (!empty($string))
            {
                $data   = json_decode($string, true);

                # json_encode can return TRUE, FALSE or NULL (http://php.net/manual/en/function.json-decode.php)
                if (is_null($data) || $data === false)
                {
                    $this->errorProvider->error(Drone_Error_Errno::JSON_DECODE_ERROR, $this->outputFile);
                    return false;
                }
            }
        }

        $data[$id] = array(
            "message" => $exception->getMessage(),
            "object"  => serialize($exception)
        );

        if (!function_exists('mb_detect_encoding'))
            throw new \RuntimeException("mbstring library is not installed!");

        /*
         * Encodes to UTF8 all messages. It ensures JSON encoding.
         */
        try {

            if (!mb_detect_encoding($data[$id]["message"], 'UTF-8', true))
                $data[$id]["message"] = utf8_encode($data[$id]["message"]);

            $class  = get_class($exception);
            throw new $class($data[$id]["message"]);
        }
        catch (Exception $e)
        {
            $data[$id]["object"] = $e;
        }

        if (($encoded_data = json_encode($data)) === false)
        {
            $this->errorProvider->error(Drone_Error_Errno::JSON_ENCODE_ERROR, $this->outputFile);
            return false;
        }

        $hd = @fopen($this->outputFile, "w+");

        if (!$hd || !@fwrite($hd, $encoded_data))
        {
            $this->errorProvider->error(Drone_Error_Errno::FILE_PERMISSION_DENIED, $this->outputFile);
            return false;
        }

        @fclose($hd);

        return $id;
    }
}