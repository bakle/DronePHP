<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

class Drone_Exception_Exception extends \Exception
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
        $this->errorProvider->errorProvider = new Drone_Error_ErrorCollector();
    }

    /**
     * Stores the exception
     *
+     * By default exceptions are stores in the specific JSON file << $this->outputFile >>
+     *
+     * @return string|boolean
     */
    public function store()
    {
        # simple way to generate a unique id
        $id = time() . uniqid();

        # creates a new array with exceptions or gets the current collector
        $data = array();

+        if (file_exists($this->outputFile))
+        {
+            $string = file_get_contents($this->outputFile);
+
+            if (!empty($string))
+            {
+                $data   = json_decode($string, true);
+
+                # json_encode can be return TRUE, FALSE or NULL (http://php.net/manual/en/function.json-decode.php)
+                if (is_null($data) || $data === false)
+                {
+                    $this->errorProvider->error(Drone_Error_Errno::JSON_DECODE_ERROR, $this->outputFile);
+                    return false;
+                }
+            }
+        }

        $data[$id] = array(
+            "message" => $this->getMessage(),
+            "object"  => serialize($this)
        );

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