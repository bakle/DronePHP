<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

class Drone_Validator_FormValidator
{
    /**
     * @var boolean
     */
	private $valid;

    /**
     * @var array
     */
	private $messages = array();

     /**
     * @var Drone_Dom_Element_Form
     */
	private $formHandler;

    /**
     * Get all failure messages
     *
     * @return array
     */
	public function getMessages()
	{
		return $this->messages;
	}

    /**
     * Get valid attribute after validation
     *
     * @return array
     */
	public function isValid()
	{
		return $this->valid;
	}

    /**
     * Set valid atribute after each validation
     *
     * @return array
     */
	public function setValid($valid)
	{
		return $this->valid && $valid;
	}

    /**
     * Constructor
     *
     * @param array $rules
     */
	public function __construct(Drone_Dom_Element_Form $formHandler)
	{
		$this->formHandler = $formHandler;
	}

    /**
     * Check all form rules
     *
     * @return null
     */
	public function validate()
	{
		$attribs = $this->formHandler->getAttributes();

		foreach ($attribs as $key => $attributes)
		{
			if (!array_key_exists($key, $attribs))
				throw new Exception("The field '$key' does not exists!");

			$label = (array_key_exists('label', array_keys($attributes))) ? $attributes["label"] : $key;

			$all_attribs = [];

			foreach ($attributes as $attr)
			{
				$all_attribs[$attr->getName()] = $attr->getValue();
			}

			foreach ($attributes as $name => $attr)
			{
				$name = $attr->getName();
				$value = $attr->getValue();

				$form_value = $this->formHandler->getAttribute($label, "value")->getValue();

				switch ($name)
				{
					case 'required':

						$validator = new Zend_Validate_NotEmpty();
						break;

					case 'minlength':

						$validator = new Zend_Validate_GreaterThan(['min' => $value, 'inclusive' => true]);
						$form_value = strlen($form_value);
						break;

					case 'maxlength':

						$validator = new Zend_Validate_LessThan(['max' => $value, 'inclusive' => true]);
						$form_value = strlen((string) $form_value);
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

						if (array_key_exists('type', $all_attribs) && in_array($all_attribs['type'], ['number', 'range']))
							$validator = new Zend_Validate_GreaterThan(['min' => $value, 'inclusive' => true]);
						else
							throw new Exception("The input type must be 'range' or 'number'");

						break;

					case 'max':

						if (array_key_exists('type', $all_attribs) && in_array($all_attribs['type'], ['number', 'range']))
							$validator = new Zend_Validate_LessThan(['max' => $value, 'inclusive' => true]);
						else
							throw new Exception("The input type must be 'range' or 'number'");

						break;

					case 'step':

						$baseValue = (array_key_exists('min', $all_attribs)) ? $all_attribs['min'] : 0;

						if (array_key_exists('type', $all_attribs) && in_array($all_attribs['type'], ['range']))
							$validator = new Drone_Validate_Step(['baseValue' => $baseValue, 'step' => $value]);
						else
							throw new Exception("The input type must be 'range'");

						break;
				}

				if (in_array($name, ['required', 'digits', 'minlength', 'maxlength', 'type', 'min', 'max', 'date', 'step']))
				{
					$valid = $validator->isValid($form_value);
					$this->setValid($valid);

					if (!$valid)
					{
						if (!in_array($key, array_keys($this->messages)))
							$this->messages[$key] = array();

						$this->messages[$key] = array_merge($this->messages[$key], $validator->getMessages());
					}
				}

			}
		}
	}
}