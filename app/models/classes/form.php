<?php

class Form
{
	private $_db 					= null,
		$_ID 					= null,
		$_inputs 			= null,
		$_data 				= null,
		$_method 			= null,
		$_errorStatus = false,
		$_errors 			= array();

	private static 	$_allowedImgs = array(
			'jpg', 'jpeg', 'png', 'webp', 'gif', 'exif', 'tiff', 'bmp', 'pnm', 'svg', 'cgm'
		),
		$_allowedAudioTypes = array(
			'mp3', 'avi', 'wav', 'mp4', 'm4a'
		);

	public function __construct($name = null, $method = null)
	{
		if ($name) {
			$this->_ID 			= uniqid();
			$this->_method 	= ($method) ? $method : $_POST;
			$this->setData($name);
		}
	}

	public static function submitted($method = 'POST')
	{
		return (isset($_POST) && !empty($_POST)) ? true : false;
	}

	public function getInputs()
	{
		if (is_null($this->_inputs) === false and is_array($this->_inputs) === true and count($this->_inputs) > 0) {
			return (object)$this->_inputs;
		} else {
			return null;
		}
	}

	private function setData($name = null)
	{
		if ($name and $this->_ID) {
			switch ($name) {

				case 'registerStore':
					$this->_data = array(
						'storeName'			=> array(
							'Name',
							'required'	=> true,
							'min'				=> 3,
							'max'				=> 100
						),
						'ownerEmail'		=> array(
							'Name',
							'required'	=> true,
							'min'				=> 3,
							'max'				=> 320
						),
						'ownerPassword'	=> array(
							'Name',
							'required'	=> true,
							'min'				=> 8,
							'max'				=> 32
						)
					);
					break;

				case 'adminLogin':
					$this->_data = array(
						'email'					=> array(
							'Email',
							'required'			=> true,
							'max'						=> 320
						),
						'password'			=> array(
							'Password',
							'required'			=> true,
							'min'						=> 8,
							'max'						=> 32
						)
					);
					break;

				case 'updateOwnerProfile':
					$this->_data = array(
						'firstName'				=> array(
							'First Name',
							'required'	=> true,
							'min'				=> 3,
							'max'				=> 15
						),
						'surname'				=> array(
							'Surname',
							'required'	=> true,
							'min'				=> 3,
							'max'				=> 15
						),
						'email'						=> array(
							'Email',
							'required'	=> true,
							'min'				=> 3,
							'max'				=> 320
						),
						'contact'				=> array(
							'Contact',
							'required'	=> true,
							'min'				=> 3,
							'max'				=> 15
						),
						'cnic'          => array(
							'CNIC',
							'required'  => false
						)
					);
					break;

				case 'changeOwnerPassword':
					$this->_data = array(
						'oldPassword'					=> array(
							'Old Password',
							'required'			=> true,
							'min'						=> 8,
							'max'						=> 32
						),
						'newPassword'					=> array(
							'New Password',
							'required'			=> true,
							'min'						=> 8,
							'max'						=> 32,
							'match'					=> 'newPasswordConfirm'
						),
						'newPasswordConfirm'	=> array(
							'Confirm Password',
							'required'			=> true,
							'min'						=> 8,
							'max'						=> 32
						)
					);
					break;

					// inventory
				case 'addInventoryItem':
					$this->_data = array(
						'itemName'			=> array(
							'Item Name',
							'required'				=> true,
							'min'							=> 2,
							'max'							=> 50
						),
						'itemCategory'	=> array(
							'Item Category',
							'required'				=> true,
							'min'							=> 2,
							'max'							=> 50
						),
						'itemBrand'			=> array(
							'Item Brand',
							'required'				=> true,
							'min'							=> 2,
							'max'							=> 50
						),
						'itemGeneral'		=> array(
							'General/Salt',
							'required'				=> false,
							'max'							=> 100
						),
						'itemUnits'		=> array(
							'Units',
							'required'				=> true,
							'min'							=> 1,
						),
					);
					break;

					// sale counter
				case 'addSaleCounter':
					$this->_data = array(
						'counterTitle'			=> array(
							'Counter Title',
							'required'						=> true,
							'min'									=> 2,
							'max'									=> 50
						),
						'scfirstName'				=> array(
							'First Name',
							'required'						=> true,
							'min'									=> 2,
							'max'									=> 25
						),
						'sclastName'				=> array(
							'Last Name',
							'required'						=> true,
							'min'									=> 2,
							'max'									=> 25
						),
						'scEmail'						=> array(
							'Email',
							'required'						=> true,
							'min'									=> 2,
							'max'									=> 320
						),
						'scPassword'				=> array(
							'Password',
							'required'						=> true,
							'min'									=> 8,
							'max'									=> 32
						),
					);
					break;

				case 'addExpense':
					$this->_data = array(
						'expenseType'			=> array(
							'Expense Type',
							'required'				=> true,
							'min'							=> 2,
							'max'							=> 50
						),
						'expenseAmount'		=> array(
							'Expense Amount',
							'required'				=> true,
							'min'							=> 1
						),
						'expenseNote'			=> array(
							'Expense Note',
							'max'							=> 500
						)
					);
					break;

				default:
					$this->_data = null;
					break;
			}
		} else {
			$this->_data = null;
		}
	}

	public function validate()
	{
		if ($this->_ID and is_array($this->_method) and (is_array($this->_data) and count($this->_data) > 0)) {
			$this->_inputs = array();
			foreach ($this->_data as $input => $rules) {
				$this->_inputs[$input] = Input::getPost($input);
				if (count($rules) == 0) {
					$this->setErrorStatus(true);
					$this->addError('Oops.. An unknown error occurred!');
					break;
				}
				$field = $rules[0];
				unset($rules[0]);
				foreach ($rules as $rule => $ruleValue) {
					switch ($rule) {
						case 'required':
							if ($ruleValue === true) {
								if (empty(Input::getPost($input)) === true and strlen(Input::getPost($input)) == 0) {
									$this->addError("{$field} should not be empty.");
								}
							}
							break;

						case 'fileRequired':
							if ($ruleValue === true and Input::fileExists($input) === false) {
								$this->addError("{$field} must be selected to upload.");
							}
							break;

						case 'allowedTypes':
							if (Input::fileExists($input) === true) {
								$file = Input::getFile($input);
								$ext 	= explode('.', $file['name']);
								$ext 	= end($ext);
								if (in_array($ext, $ruleValue) === false) {
									$this->addError("{$field} is not of a valid type.");
								}
							}
							break;

						case 'min':
							if (strlen(Input::getPost($input)) < $ruleValue) {
								$this->addError("{$field} should contain at least {$ruleValue} characters.");
							}
							break;

						case 'max':
							if (strlen(Input::getPost($input)) > $ruleValue) {
								$this->addError("{$field} could contain at most {$ruleValue} characters.");
							}
							break;

						case 'match':
							if (Input::getPost($input) != Input::getPost($ruleValue)) {
								$this->addError("{$field} does not match.");
							}
							break;

						case 'pattern':
							if ($ruleValue == 'alpha') {
								//
							} elseif ($ruleValue == 'alphaNumeric') {
								//
							} elseif ($ruleValue == 'naturalNumber') {
								if (Input::getPost($input) <= 0) {
									$this->addError("{$field} can not contain zeor or negative value.");
								}
							} elseif ($ruleValue == 'wholeNumber') {
								if (Input::getPost($input) < 0) {
									$this->addError("{$field} can not contain a negative value.");
								}
							} else {
								$this->addError("Oops... An unknown error occurred!");
							}
							break;

						case 'unique':
							if (is_array($ruleValue) and count($ruleValue) == 2) {
								$this->_db	= DB::getInstance();
								$table 			= $ruleValue['table'];
								$verify 		= $this->_db->get('*', $table, "{$ruleValue['field']} = ?", array(Input::getPost($input)));
								if (!$verify->errorStatus()) {
									if ($verify->dataCount() != 0) {
										$this->addError("{$field} already exists.");
									}
								} else {
									$this->addError('Oops... An unknown error occurred!');
								}
							} else {
								$this->addError('Oops... An unknown error occurred!');
							}
							break;

						case 'verify':
							if (is_array($ruleValue) and count($ruleValue) == 2) {
								$this->_db	= DB::getInstance();
								$table 			= $ruleValue['table'];
								$field 			= $ruleValue['field'];
								$verify 		= $this->_db->get('*', $table, "{$field} = ?", array(Input::getPost($input)));
								if (!$verify->errorStatus()) {
									if ($verify->dataCount() != 1) {
										$this->addError("Invalid {$field}.");
									}
								} else {
									$this->addError('Oops... An unknown error occurred!');
								}
							} else {
								$this->addError('Oops... An unknown error occurred!');
							}
							break;

						case 'checked':
							if ($ruleValue === true) {
								if (!isset($this->_method[$input])) {
									$this->addError("{$field} is not checked!");
								}
							}
							break;

						default:
							$this->addError('Oops... An unknown error occurred!');
							break;
					}
				}
			}
		} else {
			$this->setErrorStatus(true);
			$this->addError('Data validation failed!');
			return false;
		}
	}

	public static function validateImage($imgFile = null)
	{
		if (is_null($imgFile) === false and isset($imgFile['name']) === true) {
			$imgExt = strtolower(pathinfo($imgFile['name'], PATHINFO_EXTENSION));
			if (in_array($imgExt, self::$_allowedImgs) === true) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public static function validateAudio($audioFile = null)
	{
		if (is_null($audioFile) === false and isset($audioFile['name']) === true) {
			$audioExt = strtolower(pathinfo($audioFile['name'], PATHINFO_EXTENSION));
			if (in_array($audioExt, self::$_allowedAudioTypes) === true) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	private function addError($error = null)
	{
		if ($error) {
			if ($this->errorStatus() === false) {
				$this->setErrorStatus(true);
			}
			array_push($this->_errors, $error);
		}
	}

	public function getErrors()
	{
		return $this->_errors;
	}

	private function setErrorStatus($errorStatus)
	{
		if ($errorStatus === true or $errorStatus === false) {
			$this->_errorStatus = $errorStatus;
		} else {
			$this->_errorStatus = 'Oops... An unknown error occurred!';
		}
	}

	public function errorStatus()
	{
		return $this->_errorStatus;
	}

	public static function reset($type = 'post')
	{
		Input::reset($type);
	}
}
