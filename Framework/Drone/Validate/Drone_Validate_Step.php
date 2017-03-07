<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/fermius/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

class Drone_Validate_Step extends Zend_Validate_Abstract
{
    const INVALID = 'typeInvalid';
    const NOT_STEP = 'stepInvalid';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "Invalid value given. Scalar expected",
        self::NOT_STEP => "The input is not a valid step"
    );

    /**
     * @var mixed
     */
    protected $baseValue = 0;

    /**
     * @var mixed
     */
    protected $step = 1;

    /**
     * Set default options for this instance
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } elseif (!is_array($options)) {
            $options = func_get_args();
            $temp['baseValue'] = array_shift($options);
            if (!empty($options)) {
                $temp['step'] = array_shift($options);
            }
            $options = $temp;
        }
        if (isset($options['baseValue'])) {
            $this->setBaseValue($options['baseValue']);
        }
        if (isset($options['step'])) {
            $this->setStep($options['step']);
        }
    }

    /**
     * Sets the base value from which the step should be computed
     *
     * @param mixed $baseValue
     * @return Step
     */
    public function setBaseValue($baseValue)
    {
        $this->baseValue = $baseValue;
        return $this;
    }

    /**
     * Returns the base value from which the step should be computed
     *
     * @return string
     */
    public function getBaseValue()
    {
        return $this->baseValue;
    }

    /**
     * Sets the step value
     *
     * @param mixed $step
     * @return Step
     */
    public function setStep($step)
    {
        $this->step = (float) $step;
        return $this;
    }

    /**
     * Returns the step value
     *
     * @return string
     */
    public function getStep()
    {
        return $this->step;
    }

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
        if (!is_numeric($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        $this->_setValue($value);

        $fmod = $this->fmod($value - $this->baseValue, $this->step);
        if ($fmod !== 0.0 && $fmod !== $this->step) {
            $this->_error(self::NOT_STEP);
            return false;
        }

        return true;
    }

    /**
     * replaces the internal fmod function which give wrong results on many cases
     *
     * @param float $x
     * @param float $y
     * @return float
     */
    protected function fmod($x, $y)
    {
        if ($y == 0.0) {
            return 1.0;
        }
        //find the maximum precision from both input params to give accurate results
        $xFloatSegment = substr($x, strpos($x, '.') + 1) ? substr($x, strpos($x, '.') + 1) : '';
        $yFloatSegment = substr($y, strpos($y, '.') + 1) ? substr($y, strpos($y, '.') + 1) : '';
        $precision = strlen($xFloatSegment) + strlen($yFloatSegment);
        return round($x - $y * floor($x / $y), $precision);
    }
}