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

			$components = array(
				"attributes" => array(
					"fname" => array(
						"required" => true,
						"minlength" => 3,
						"maxlength" => 10,
						#"alnumWhiteSpace" => true,
						"label" => "First name"
					),
					"lname" => array(
						"required" => true,
						"minlength" => 3,
						"maxlength" => 5,
						#"alnumWhiteSpace" => true,
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
						"type" => "email",
						"required" => true,
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
				)
			);

			$form = new Drone_Dom_Element_Form($components);
			$form->fill($_POST);

			try {
				$validator = new Drone_Validator_FormValidator($form);
				$validator->validate();

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