<?php

class Drone_Form_Validator_QuickValidator
{
    /**
     * @var array
     */
	private $rules;

    /**
     * @var boolean
     */
	private $valid;

    /**
     * @var array
     */
	private $messages = array();

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
	public function __construct($rules)
	{
		$this->rules = $rules;
	}

	public function validateWith($arrayForm)
	{
		foreach ($this->rules as $key => $attributes)
		{
			if (!array_key_exists($key, $arrayForm))
				throw new Exception("The field '$key' does not exists!");

			$label = (array_key_exists('label', array_keys($attributes))) ? $attributes["label"] : $key;

			foreach ($attributes as $name => $value)
			{
				$form_value = $arrayForm[$key];

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

							/*case 'url':

								$validator = new Zend_Validate_Uri();
								break;*/
						}
 						break;

					case 'min':

						if (in_array('type', $attributes) && in_array($attributes['type'], ['number', 'range']))
							$validator = new Zend_Validate_GreaterThan(['min' => $value, 'inclusive' => true]);
						else
							throw new Exception("The input type must be 'range' or 'number'");

						break;

					case 'max':

						if (in_array('type', $attributes) && in_array($attributes['type'], ['number', 'range']))
							$validator = new Zend_Validate_LessThan(['max' => $value, 'inclusive' => true]);
						else
							throw new Exception("The input type must be 'range' or 'number'");

						break;

					/*case 'step':

						$baseValue = (in_array('min', $attributes)) ? $attributes['min'] : 0;

						if (in_array('type', $attributes) && $attributes['type'] == "range")
							$validator = new Zend_Validate_Step(['baseValue' => $baseValue, 'step' => $value]);
						else
							throw new Exception("The input type must be 'range'");

						break;*/
				}

				if (in_array($name, ['required', 'digits', 'minlength', 'maxlength', 'type', 'min', 'max', 'date', /*'step'*/]))
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