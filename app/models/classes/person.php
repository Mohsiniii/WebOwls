<?php
abstract class Person extends custom_error
{
	// internal objects
	private $_table	= null;

	// external objects
	protected $_db 					= null,
		$_address 		= null,
		$_data  			= null;

	public function __construct($personID = null)
	{
		$this->_table = (object)[
			'name'				=> 'persons',
			'fields'			=> '`first_name`, `middle_name`, `surname`, `cnic`'
		];
		$this->_data = (object)[
			'private'			=> (object)[
				'personID'			=> null,
				'userID'				=> null,
				'privateID'			=> null,
				'token'					=> null,
				'password'			=> null
			],
			'public'			=> (object)[
				'name'					=> null,
				'email'					=> null,
				'contact'				=> null,
				'cnic'					=> null,
				'address'				=> null
			]
		];
		$this->_db 			= DB::getInstance();
		$this->_address = new Address();
	}

	abstract protected function _setPersonData($data): void;
	abstract protected function _getPrivateData($key): string;
	abstract public function getData($key);

	protected function _findP($personID = null, $type = null)
	{
		switch (strtoupper($type)) {
			case 'ACTIVE':
				$type = 1;
				break;
			default:
				$type = 1;
				break;
		}
		$findPerson = $this->_db->get($this->_table->fields, $this->_table->name, '`person_id` = ?', array($personID));
		if ($findPerson->errorStatus() === false and $findPerson->dataCount() == 1) {
			$tmpPerson = $findPerson->getFirstResult();
			return (object)[
				'id'        => $personID,
				'name'      => (object)[
					'first'       => $tmpPerson->first_name,
					'middle'      => $tmpPerson->middle_name,
					'surname'     => $tmpPerson->surname
				],
				'cnic'      => $tmpPerson->cnic
			];
		} else {
			return null;
		}
	}

	private function _create($data = null)
	{
		if ($this->__parseCreatePersonData($data) === true) {
			$personID = Hash::unique();
			$createPerson = $this->_db->insert($this->_table->name, array(
				'person_id'   => $personID,
				'first_name'  => $data->firstName,
				'surname'     => $data->surname,
				'cnic'        => $data->cnic
			));
			if ($createPerson->errorStatus() === false) {
				return (object)[
					'status'    => true,
					'type'			=> 1, // creating person
					'id'        => $personID
				];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	protected function _updateP($data = null)
	{
		$dem = 'Failed to update person details.';
		if (is_null($this->_getPrivateData('personID')) === true) {
			// create person
			$person = $this->_create($data);
			if ($person !== false and is_object($person) === true) {
				$person = (object)$person;
				if (isset($person->status) === true and $person->status === true and isset($person->type) === true and isset($person->id) === true) {
					return $person;
				} else {
					return false;
				}
			} else {
				$this->setError($dem);
				return false;
			}
		} else {
			// update person
			$updatePerson = $this->_db->update($this->_table->name, '`first_name` = ?, `surname` = ?', '`person_id` = ?', array($data->firstName, $data->surname, $this->_getPrivateData('personID')));
			if ($updatePerson->errorStatus() === false) {
				$updatedPerson = $this->_findP($this->_getPrivateData('personID'));
				if (is_null($updatePerson) === false) {
					$this->_setPersonData($updatedPerson);
				}
				return true;
			} else {
				$this->setError($dem);
				return false;
			}
		}
	}

	protected function _isCNICUnique($CNIC = null)
	{
		$dem = 'Failed to check cnic availability.';
		if (is_null($CNIC) === false) {
			$checkCNIC = $this->_db->get('`cnic`', '`persons`', '`cnic` = ?', array($CNIC));
			if ($checkCNIC->errorStatus() === false) {
				if ($checkCNIC->dataCount() == 0) {
					return true;
				} else {
					$this->setError('This cnic is already in use.');
					return false;
				}
			} else {
				$this->setError($dem);
				return false;
			}
		} else {
			return false;
		}
	}

	// data parsers
	protected function __parseCreatePersonData($data = null)
	{
		if (is_null($data) === false and is_object($data) === true) {
			$data = (object)$data;
			if (isset($data->firstName) === true and isset($data->surname) === true) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
