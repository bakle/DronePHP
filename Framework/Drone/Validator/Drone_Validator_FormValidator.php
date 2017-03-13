<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

class Drone_Validator_FormValidator
{
    /**
     * The result of latest validation
     *
     * It's null before validate() execution
     *
     * @var boolean|null
     */
	private $valid;

    /**
     * Validation messages
     *
     * @var array
     */
	private $messages = array();

     /**
     * @var Drone_Dom_Element_Form
     */
	private $formHandler;

	/**
	 * @var Zend_Translate
	 */
	private $translator;

	/**
	 * @var array
	 */
	private $options;

    /**
     * Returns all failure messages
     *
     * @return array
     */
	public function getMessages()
	{
		return $this->messages;
	}

    /**
     * Returns valid attribute after validation
     *
     * @return boolean
     */
	public function isValid()
	{
		return $this->valid;
	}

    /**
     * Sets valid atribute after each validation
     *
     * @return null
     */
	public function setValid($valid)
	{
		$this->valid = (is_null($this->valid) ? true : $this->valid) && $valid;
	}

    /**
     * Gets an option
     *
     * @param string $option
     *
     * @return mixed
     */
	public function getOption($key, $name)
	{
		if (!array_key_exists($key, $this->options))
			throw new Exception("The option '$key' does not exists");

		return array_key_exists($name, $this->options[$key]) ? $this->options[$key][$name] : null;
	}

    /**
     * Constructor
     *
     * @param Drone_Dom_Element_Form $formHandler
     */
	public function __construct(Drone_Dom_Element_Form $formHandler, $options)
	{
		$this->formHandler = $formHandler;
		$this->options = (is_array($options)) ? $options : array();

		$config = include('config/application.config.php');
		$locale = $config["environment"]["locale"];

		$this->translator = new Zend_Translate(
		    array(
		        'adapter' => 'Zend_Translate_Adapter_Array',
		        'content' => "languages/$locale/Zend_Validate.php",
		        'locale'  => "$locale"
		    )
		);
	}

    /**
     * Checks all form rules
     *
     * @return null
     */
	public function validate()
	{
		$this->setValid(true);

		$attribs = $this->formHandler->getAttributes();

		foreach ($attribs as $key => $attributes)
		{
			if (!array_key_exists($key, $attribs))
				throw new Exception("The field '$key' does not exists!");

			$label = (array_key_exists('label', array_keys($this->options))) ? $attributes["label"] : $key;

			$all_attribs = array();

			foreach ($attributes as $attr)
			{
				$all_attribs[$attr->getName()] = $attr->getValue();
			}

			$required = array_key_exists('required', $all_attribs) ? $all_attribs["required"] : false;

			foreach ($attributes as $name => $attr)
			{
				$name = $attr->getName();
				$value = $attr->getValue();

				$attrib = $this->formHandler->getAttribute($label, "value");
				$form_value = (!is_null($attrib)) ? $attrib->getValue() : null;

				switch ($name)
				{
					case 'required':

						$validator = new Zend_Validate_NotEmpty();
						break;

					case 'minlength':

						$validator = new Zend_Validate_StringLength(array('min' => $value));
						break;

					case 'maxlength':

						$validator = new Zend_Validate_StringLength(array('max' => $value));
						break;

					case 'type':

						switch ($value)
						{
							case 'number':

								$validator = new Zend_Validate_Digits();
								break;

							case 'email':

								$validator = new Zend_Validate_EmailAddress();
								break;

							case 'date':

								$validator = new Zend_Validate_Date();
								break;

							case 'url':

								$validator = new Drone_Validate_Uri();
								break;
						}
 						break;

					case 'min':

						if (array_key_exists('type', $all_attribs) && in_array($all_attribs['type'], array('number', 'range')))
							$validator = new Drone_Validate_GreaterThan(array('min' => $value, 'inclusive' => true));
						else
							throw new Exception("The input type must be 'range' or 'number'");

						break;

					case 'max':

						if (array_key_exists('type', $all_attribs) && in_array($all_attribs['type'], array('number', 'range')))
							$validator = new Drone_Validate_LessThan(array('max' => $value, 'inclusive' => true));
						else
							throw new Exception("The input type must be 'range' or 'number'");

						break;

					case 'step':

						$baseValue = (array_key_exists('min', $all_attribs)) ? $all_attribs['min'] : 0;

						if (array_key_exists('type', $all_attribs) && in_array($all_attribs['type'], array('range')))
							$validator = new Drone_Validate_Step(array('baseValue' => $baseValue, 'step' => $value));
						else
							throw new Exception("The input type must be 'range'");

						break;
				}

				if (in_array($name, array('required', 'digits', 'minlength', 'maxlength', 'type', 'min', 'max', 'date', 'step')))
				{
					$validator->setTranslator($this->translator);
					$this->_validate($validator, $form_value, $key, $required);
				}
			}
		}

		foreach ($this->options as $key => $options)
		{
			if (isset($options["validators"]) and is_array($options["validators"]))
			{
				$attrib = $this->formHandler->getAttribute($key, "required");

				$isRequired = (!is_null($attrib)) ? $attrib = $attrib->getValue() : false;

				$validator = new Zend_Validate_NotEmpty();
				$validator->setTranslator($this->translator);
				$value = $this->formHandler->getAttribute($key, "value")->getValue();

				if ($isRequired || $validator->isValid($value))
				{
					foreach ($options["validators"] as $class => $params)
					{
						$className = 'Zend_Validate_' . $class;

						if (!class_exists($className))
							throw new Exception("The class '$className' does not exists");

						$validator = new $className($params);

						$form_value = $this->formHandler->getAttribute($key, "value")->getValue();

						$validator->setTranslator($this->translator);
						$this->_validate($validator, $form_value, $key, $required);
					}
				}
			}
		}
	}

	/**
     * Validate all field values iteratively
     *
     * Supports n-dimensional arrays (name='example[][]')
     *
     * @param Zend\Validator $validator
     * @param mixed 		 $form_value
     * @param integer	     $key
     * @param boolean	     $required
     *
     * @return null
     */
	private function _validate($validator, $form_value, $key, $required)
	{
		if (gettype($form_value) != 'array')
		{
			$val = $form_value;

			# Check if the value is required. If it is, check the other rules.
			$v = new Zend_Validate_NotEmpty();
			$v->setTranslator($this->translator);
			$notEmpty = $v->isValid($val);

			if (!$required && !$notEmpty)
				return null;

			$valid = $validator->isValid($val);
			$this->setValid($valid);

			if (!$valid)
			{
				if (!in_array($key, array_keys($this->messages)))
					$this->messages[$key] = array();

				$this->messages[$key] = array_merge($this->messages[$key], $validator->getMessages());
			}
		}
		else {
			foreach ($form_value as $val)
			{
				$this->_validate($validator, $val, $key);
			}
		}
	}
}