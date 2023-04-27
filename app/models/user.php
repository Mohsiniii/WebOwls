<?php
class User extends Person
{
	// internal objects
	protected $_userType 		= null,
		$_userId 			= null,
		$_isLoggedIn 	= false;
	// external object
	protected $_role 				= null,
		$_campus 			= null;

	public function __construct($type = null)
	{
		parent::__construct();
		$this->_role = new Role();
		if (is_null($type) === false) {
			$this->_userType = $type;
			$checkUser = $this->checkLogin();
			if ($checkUser !== false and isset($checkUser->status) === true and $checkUser->status === true) {
				// user is logged in
				if ($this->_verifyLoginUser() === true) {
					$tmpUserSession = Session::get(Config::get('session/name'));
					$tmpUser = $this->_find($tmpUserSession->id);
					if (is_null($tmpUser) === false and is_object($tmpUser) === true) {
						// user found successfully, (also need to verify the role)
						$tmpUser = (object)$tmpUser;
						$this->_setUserData($tmpUser, 'ALL');
						$person = $this->_findP($tmpUser->person);
						if (is_null($person) === false) {
							$this->_setPersonData($person);
						} else {
							// failed to find the logged in person
							// $this->logout();
							// Redirect::to('home');
						}
						$this->setLogin(true);
					} else {
						// failed to find the logged in user
						$this->logout();
						Redirect::to('home');
					}
				}
			}
		}
	}

	protected function _setUserData($data = null, $type = null)
	{
		if (is_null($data) === false) {
			switch (strtoupper($type)) {
				case 'PUBLIC':
					$this->_setPublicData($data);
					break;
				case 'PRIVATE':
					$this->_setPrivateData($data);
					break;
				case 'ALL':
					$this->_setPublicData($data);
					$this->_setPrivateData($data);
					break;
				default:
					//
					break;
			}
		}
	}
	private function _setPublicData($data = null)
	{
		if (is_null($data) === false) {
			if (isset($data->email) === true) {
				$this->_data->public->email = $data->email;
			}
			if (isset($data->contact) === true) {
				$this->_data->public->contact = $data->contact;
			}
		}
	}
	private function _setPrivateData($data = null)
	{
		if (is_null($data) === false) {
			if (isset($data->id) === true) {
				$this->_data->private->userID = $data->id;
			}
			if (isset($data->privateID) === true) {
				$this->_data->private->privateID = $data->privateID;
			}
			if (isset($data->person) === true) {
				$this->_data->private->personID = $data->person;
			}
			if (isset($data->token) === true) {
				$this->_data->private->token = $data->token;
			}
			if (isset($data->password) === true) {
				$this->_data->private->password = $data->password;
			}
		}
	}

	// strt abstract methods
	protected function _setPersonData($data = null): void
	{
		if (is_null($data) === false) {
			if (isset($data->name) === true) {
				$this->_data->public->name = $data->name;
			}
			if (isset($data->cnic) === true) {
				$this->_data->public->cnic = $data->cnic;
			}
		}
	}
	protected function _getPrivateData($key = null): string
	{
		if (is_null($key) === false) {
			return (isset($this->_data->private->$key) === true) ? $this->_data->private->$key : null;
		}
	}
	public function getData($key = null)
	{
		if ($this->isLoggedIn() === true and is_null($key) === false) {
			return (isset($this->_data->public->$key) === true) ? $this->_data->public->$key : null;
		}
	}
	// end abstract methods

	protected function _find($userID = null, $type = null)
	{
		$dem = 'User not found.';
		if (is_null($userID) === false) {
			switch (strtoupper($type)) {
				case 'ACTIVE':
					$type = 1;
					break;
				default:
					$type = 1;
					break;
			}
			$findUser = $this->_db->get('`user_id`, `person_id`, `email`, `contact`, `token`, `password`, `role_id` as `role`', 'users', '`user_id` = ? AND `account_status` = ?', array($userID, $type));
			if ($findUser->errorStatus() === false and $findUser->dataCount() == 1) {
				$tmpUser = $findUser->getFirstResult();
				return (object)[
					'id'				=> $tmpUser->user_id,
					'person'		=> $tmpUser->person_id,
					'email'			=> $tmpUser->email,
					'contact'		=> $tmpUser->contact,
					'token'			=> $tmpUser->token,
					'password'	=> $tmpUser->password,
					'role'			=> $this->_role->find($tmpUser->role)
				];
			} else {
				$this->setError($dem);
				return null;
			}
		} else {
			$this->setError($dem);
			return null;
		}
	}

	protected function find($userID = null, $type = null)
	{
		$dem = 'User not found.';
		if (is_null($userID) === false) {
			switch (strtoupper($type)) {
				case 'ACTIVE':
					$type = 1;
					break;
				default:
					$type = 1;
					break;
			}
			$findUser = $this->_db->get('`user_id`, `person_id`, `email`, `contact`', 'users', '`user_id` = ? AND `account_status` = ?', array($userID, $type));
			if ($findUser->errorStatus() === false and $findUser->dataCount() == 1) {
				$tmpUser = $findUser->getFirstResult();
				return (object)[
					'id'				=> $tmpUser->user_id,
					'person'		=> $tmpUser->person_id,
					'name'			=> null,
					'email'			=> $tmpUser->email,
					'contact'		=> $tmpUser->contact,
					'role'			=> null
				];
			} else {
				$this->setError($dem);
				return null;
			}
		} else {
			$this->setError($dem);
			return null;
		}
	}

	protected function findByEmail($email = null, $type = null)
	{
		$dem = 'User not found.';
		$tmpResponse = (object)[
			'status'		=> false,
			'data'			=> null,
			'message'		=> (object)[
				'success'			=> null,
				'error'				=> null
			]
		];
		if (is_null($email) === false) {
			switch (strtoupper($type)) {
				case 'ACTIVE':
					$type = 1;
					break;
				default:
					$type = 1;
					break;
			}
			$findUser = $this->_db->get('`user_id`, `email`, `token`, `password`, `role_id` as `role`', 'users', '`email` = ? AND `account_status` = ?', array($email, $type));
			if ($findUser->errorStatus() === false and $findUser->dataCount() == 1) {
				$tmpUser = $findUser->getFirstResult();
				$tmpResponse->status = true;
				$tmpResponse->data = (object)[
					'id'				=> $tmpUser->user_id,
					'email'			=> $tmpUser->email,
					'role'			=> $this->_role->find($tmpUser->role),
					'token'			=> $tmpUser->token,
					'password'	=> $tmpUser->password
				];
				return $tmpResponse;
			} else {
				$tmpResponse->message->error = $dem;
				return $tmpResponse;
			}
		} else {
			$tmpResponse->message->error = $dem;
			return null;
		}
	}

	protected function _isUsernameAvailable($username = null)
	{
		$dem = 'Failed to check username availability.';
		if (is_null($username) === false) {
			$checkUsername = $this->_db->get('`username`', '`users`', '`username` = ?', array($username));
			if ($checkUsername->errorStatus() === false) {
				if ($checkUsername->dataCount() == 0) {
					return true;
				} else {
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

	protected function _isEmailUnique($email = null)
	{
		$dem = 'Failed to check email availability.';
		if (is_null($email) === false) {
			$checkEmail = $this->_db->get('`email`', '`users`', '`email` = ?', array($email));
			if ($checkEmail->errorStatus() === false) {
				if ($checkEmail->dataCount() == 0) {
					return true;
				} else {
					$this->setError('This email is already in use.');
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

	public function login($email = null, $password = null)
	{
		$dem = 'Failed to login.';
		if (is_null($email) === false and is_null($password) === false) {
			$tmpUser = $this->findByEmail($email);
			if (is_object($tmpUser) === true and isset($tmpUser->status) === true and $tmpUser->status === true) {
				// user found, i.e. email is correct
				if ($tmpUser->data->password == Hash::make($password . $tmpUser->data->token)) {
					// password matched
					if ($this->_putUserSession($tmpUser->data->role->title, $tmpUser->data->id) === true) {
						return true;
					} else {
						$this->setError($dem);
						return false;
					}
				} else {
					// password didn't match
					$this->setError($dem . ' Password is incorrect.');
					return false;
				}
			} else {
				// some error occurred
				if (isset($tmpUser->message->error) === true and is_null($tmpUser->message->error) === false) {
					// error message exists
					$this->setError($tmpUser->message->error);
					return false;
				} else {
					// error message doesn't exist
					$this->setError($dem);
					return false;
				}
			}
		} else {
			$this->setError($dem);
			return null;
		}
	}

	public function logout()
	{
		if (Session::exists(Config::get('session/name')) === true) {
			Session::delete(Config::get('session/name'));
			return true;
		} else {
			session_destroy();
			return true;
		}
		return false;
	}

	protected function _putUserSession($type = null, $userID = null)
	{
		$defaultErrorMessage = 'Failed to login.';
		if (is_null($type) === false and is_null($userID) === false) {
			if (Session::exists(Config::get('session/name')) === true) {
				// session already exists with this name
				$tmpUserSession = Session::get(Config::get('session/name'));
				if ($tmpUserSession->type != $type or $tmpUserSession->id != $userID) {
					// user is different
					if (Session::put(Config::get('session/name'), (object)[
						'id'        => $userID,
						'type'      => $type
					]) === true) {
						return true;
					} else {
						$this->setError($defaultErrorMessage);
						return false;
					}
				}
			} else {
				if (Session::put(Config::get('session/name'), (object)[
					'id'        => $userID,
					'type'      => $type
				]) === true) {
					return true;
				} else {
					$this->setError($defaultErrorMessage);
					return false;
				}
			}
		} else {
			$this->setError($defaultErrorMessage);
			return false;
		}
	}

	protected function setLogin($loginStatus = false)
	{
		if (is_bool($loginStatus) === true) {
			$this->_isLoggedIn = $loginStatus;
		} else {
			$this->_isLoggedIn = false;
		}
	}

	public function isLoggedIn()
	{
		return (is_bool($this->_isLoggedIn) === true) ? $this->_isLoggedIn : false;
	}

	private function _verifyLoginUser()
	{
		$checkUser = $this->checkLogin();
		if (isset($checkUser->status) === true and $checkUser->status === true and isset($checkUser->user) === true) {
			if (strtoupper($checkUser->user) == $this->_userType) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function checkLogin()
	{
		$tmpUserSession = Session::get(Config::get('session/name'));
		if (is_null($tmpUserSession) === false and isset($tmpUserSession->type) === true and isset($tmpUserSession->id) === true) {
			if (is_null($tmpUserSession->type) === false and is_null($tmpUserSession->id) === false) {
				return (object)[
					'status'		=> true,
					'user'			=> $tmpUserSession->type
				];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	protected function _updateAddress($userID = null, $address = null)
	{
		$dem = 'Failed to update address.';
		if (is_null($userID) === false and $this->__parseAddressData($address) === true) {
			$country = $address->country;
			$state = $address->state;
			$province = $address->province;
			$city = $address->city;
			$street = $address->street;
			$searchAddress = $this->_address->search($country, $state, $province, $city, $street);
			if ($searchAddress !== false) {
				$tmpAddressID = null;
				if ($searchAddress === true) {
					// address not found, create it
					$createAddress = $this->_address->create($country, $state, $province, $city, $street);
					if ($createAddress === false) {
						$this->setError($dem);
						return false;
					} else {
						$tmpAddressID = $createAddress;
					}
				} else {
					// similer address found, attach it with this campus
					$tmpAddressID = $searchAddress;
				}
				if (is_null($tmpAddressID) === false) {
					// address id is set
					$tmpAddress = $this->_address->find($tmpAddressID);
					if (is_null($tmpAddress) === false) {
						// valid address entity, attach it
						$newUserAddressID = Hash::unique();
						$updateAddress = $this->_db->insert('addresses_users', array(
							'ua_id'         => $newUserAddressID,
							'user_id'		    => $userID,
							'address_id'    => $tmpAddressID,
							'ua_status'     => 1
						));
						if ($updateAddress->errorStatus() === false) {
							// address changed, remove previous addresses
							$removePreviousAddresses = $this->_db->update('addresses_users', '`ua_status` = ?', '`user_id` = ? AND `ua_id` != ?', array(-1, $userID, $newUserAddressID));
							if ($removePreviousAddresses->errorStatus() === false) {
								return true;
							} else {
								$this->setError($dem . ' Please immediately contact with admin.');
								return false;
							}
						} else {
							$this->setError($dem);
							return false;
						}
					} else {
						$this->setError($dem);
						return false;
					}
				} else {
					$this->setError($dem);
					return false;
				}
			} else {
				$this->setError($dem);
				return false;
			}
		} else {
			$this->setError($dem);
			return false;
		}
	}

	// data parsers
	protected function __parseAddressData($data = null)
	{
		if (is_null($data) === false and is_object($data) === true) {
			$data = (object)$data;
			if (isset($data->country) === true and isset($data->state) === true and isset($data->province) === true and isset($data->city) === true and isset($data->street) === true) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
