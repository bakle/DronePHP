<?php

class App_Controller_Form extends Drone_Mvc_AbstractionController
{
	public function validator()
	{
		# data to view
		$data = array();

		if ($this->isPost())
		{
			$data["success"] = true;

			$rules = array(
				"fname" => array(
					"required" => true,
					"minlength" => 3,
					"maxlength" => 10,
					/*"alnumWhiteSpace" => true,*/
					"label" => "First name"
				),
				"lname" => array(
					"required" => true,
					"minlength" => 3,
					"maxlength" => 10,
					/*"alnumWhiteSpace" => true,*/
					"label" => "Last name"
				),
				"height" => array(
					"type" => "range",
 					"required" => true,
					"min" => 0.5,
					"max" => 2.5,
					"step" => 0.1,
					"label" => "Height"
 				),
				"email" => array(
					"required" => true,
					"type" => "email",
					"label" => "Email"
				),
				"date" => array(
					"required" => true,
					"type" => "date",
					"label" => "Date"
				),
				"url" => array(
					"required" => true,
					"type" => "url",
					"label" => "Website"
 				)
			);

			try {
				$validator = new Drone_Form_Validator_QuickValidator($rules);
				$validator->validateWith($_POST);

				if (!$validator->isValid())
				{
					$data["success"] = false;
					$data["messages"] = $validator->getMessages();
				}

			}
			catch (Exception $e)
			{
				$data["success"] = false;
				$data["message"] = $e->getMessage();
				return $data;
			}
		}

		return $data;
	}
}