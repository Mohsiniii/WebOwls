<?php
class Address
{
  private $_db = null;

  private $_data        = null,
    $_table       = null,
    $_error       = null,
    $_errorStatus = false;

  public function __construct()
  {
    $this->_db = DB::getInstance();
    $this->_table = (object)[
      'name'      => 'addresses',
      'fields'    => '`address_id`, `country`, `state`, `province`, `city`, `street_primary`'
    ];
  }

  public function create($country = null, $state = null, $province = null, $city = null, $street = null)
  {
    // at least one entity should not be null
    $dem = 'Failed to create address.';
    if (is_null($country) === false or is_null($state) === false or is_null($province) === false or is_null($city) == null or is_null($street) === false) {
      $tmpAddressID = Hash::unique();
      $createAddress = $this->_db->insert($this->_table->name, array(
        'address_id'       => $tmpAddressID,
        'country'         => $country,
        'state'           => $state,
        'province'        => $province,
        'city'            => $city,
        'street_primary'  => $street
      ));
      if ($createAddress->errorStatus() === false) {
        // no error
        return $tmpAddressID;
      } else {
        // error
        $this->setError($dem);
        return false;
      }
    }
  }

  private function _create($country = null, $state = null, $province = null, $city = null, $street = null)
  {
    // at least one entity should not be null
    $dem = 'Failed to create address.';
    if (is_null($country) === false or is_null($state) === false or is_null($province) === false or is_null($city) == null or is_null($street) === false) {
      $tmpAddressID = Hash::unique();
      $createAddress = $this->_db->insert($this->_table->name, array(
        'address_id'       => $tmpAddressID,
        'country'         => $country,
        'state'           => $state,
        'province'        => $province,
        'city'            => $city,
        'street_primary'  => $street
      ));
      if ($createAddress->errorStatus() === false) {
        // no error
        return $tmpAddressID;
      } else {
        // error
        $this->setError($dem);
        return false;
      }
    }
  }

  public function find($addressID = null, $type = null)
  {
    if (is_null($addressID) === false) {
      switch (strtoupper($type)) {
        case 'ACTIVE':
          $type = 1;
          break;
        default:
          $type = 1;
          break;
      }
      $findAddress = $this->_db->get($this->_table->fields, $this->_table->name, '`address_id` = ?', array($addressID));
      if ($findAddress->errorStatus() === false and $findAddress->dataCount() == 1) {
        $tmpAddress = $findAddress->getFirstResult();
        return (object)[
          'country'   => $tmpAddress->country,
          'state'     => $tmpAddress->state,
          'province'  => $tmpAddress->province,
          'city'      => $tmpAddress->city,
          'street'    => $tmpAddress->street_primary,
        ];
      } else {
        return null;
      }
    } else {
      return null;
    }
  }

  public function search($country = null, $state = null, $province = null, $city = null, $street = null)
  {
    // at least one entity should not be null
    $dem = 'Failed to search address.';
    if (is_null($country) === false or is_null($state) === false or is_null($province) === false or is_null($city) == null or is_null($street) === false) {
      $findAddress = $this->_db->get('`address_id`', $this->_table->name, '`country` = ? AND `state` = ? AND `province` = ? AND `city` = ? AND `street_primary` = ?', array($country, $state, $province, $city, $street));
      if ($findAddress->errorStatus() === false) {
        // no error
        if ($findAddress->dataCount() == 1) {
          $tmpAddress = $findAddress->getFirstResult();
          return $tmpAddress->address_id;
        } else {
          return true;
        }
      } else {
        // error
        $this->setError($dem);
        return false;
      }
    }
  }

  // school
  public function getStoreAddress($storeID = null, $type = null)
  {
    $dem = 'Address not found.';
    if (is_null($storeID) === false) {
      switch (strtoupper($type)) {
        case 'ACTIVE':
          $type = 1;
          break;
        default:
          $type = 1;
          break;
      }
      $findStoreAddress = $this->_db->get('`address_id`', $this->_table->name . '_stores', '`store_id` = ? AND `sa_status` = ?', array($storeID, $type));
      if ($findStoreAddress->errorStatus() === false and $findStoreAddress->dataCount() == 1) {
        $tmpSchoolAddress = $findStoreAddress->getFirstResult();
        return $this->find($tmpSchoolAddress->address_id);
      } else {
        $this->setError($dem);
        return null;
      }
    } else {
      $this->setError($dem);
      return null;
    }
  }
  public function updateSchoolAddress($schoolID = null, $country = null, $state = null, $province = null, $city = null, $street = null)
  {
    $dem = 'Failed to update school address.';
    if (is_null($schoolID) === false) {
      $searchAddress = $this->search($country, $state, $province, $city, $street);
      if ($searchAddress !== false) {
        $tmpAddressID = null;
        if ($searchAddress === true) {
          // address not found, create it
          $createAddress = $this->_create($country, $state, $province, $city, $street);
          if ($createAddress === false) {
            $this->setError($dem);
            return false;
          } else {
            $tmpAddressID = $createAddress;
          }
        } else {
          // similer address found, attach it with this school
          $tmpAddressID = $searchAddress;
        }
        if (is_null($tmpAddressID) === false) {
          // address id is set
          $tmpAddress = $this->find($tmpAddressID);
          if (is_null($tmpAddress) === false) {
            // valid address entity, attach it
            $newSchoolAddressID = Hash::unique();
            $updateAddress = $this->_db->insert($this->_table->name . '_schools', array(
              'sa_id'         => $newSchoolAddressID,
              'school_id'     => $schoolID,
              'address_id'    => $tmpAddressID,
              'sa_status'     => 1
            ));
            if ($updateAddress->errorStatus() === false) {
              // address changed, remove previous addresses
              $removePreviousAddresses = $this->_db->update($this->_table->name . '_schools', '`sa_status` = ?', '`school_id` = ? AND `sa_id` != ?', array(-1, $schoolID, $newSchoolAddressID));
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

  // branch
  public function updateBranchAddress($branchID = null, $address = null)
  {
    $dem = 'Failed to update store branch address.';
    if (is_null($branchID) === false and $this->__parseAddressData($address) === true) {
      $country = $address->country;
      $state = $address->state;
      $province = $address->province;
      $city = $address->city;
      $street = $address->street;
      $searchAddress = $this->search($country, $state, $province, $city, $street);
      if ($searchAddress !== false) {
        $tmpAddressID = null;
        if ($searchAddress === true) {
          // address not found, create it
          $createAddress = $this->_create($country, $state, $province, $city, $street);
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
          $tmpAddress = $this->find($tmpAddressID);
          if (is_null($tmpAddress) === false) {
            // valid address entity, attach it
            $newBranchAddressID = Hash::unique();
            $updateAddress = $this->_db->insert($this->_table->name . '_branches', array(
              'ba_id'         => $newBranchAddressID,
              'branch_id'     => $branchID,
              'address_id'    => $tmpAddressID,
              'ba_status'     => 1
            ));
            if ($updateAddress->errorStatus() === false) {
              // address changed, remove previous addresses
              $removePreviousAddresses = $this->_db->update($this->_table->name . '_branches', '`ba_status` = ?', '`branch_id` = ? AND `ba_id` != ?', array(-1, $branchID, $newBranchAddressID));
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

  // faculty
  private function updateFacultyAddress($facultyID = null, $address = null)
  {
    $dem = 'Failed to update faculty address.';
    if (is_null($facultyID) === false and $this->__parseAddressData($address) === true) {
      $country = $address->country;
      $state = $address->state;
      $province = $address->province;
      $city = $address->city;
      $street = $address->street;
      $searchAddress = $this->search($country, $state, $province, $city, $street);
      if ($searchAddress !== false) {
        $tmpAddressID = null;
        if ($searchAddress === true) {
          // address not found, create it
          $createAddress = $this->_create($country, $state, $province, $city, $street);
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
          $tmpAddress = $this->find($tmpAddressID);
          if (is_null($tmpAddress) === false) {
            // valid address entity, attach it
            $newFacultyAddressID = Hash::unique();
            $updateAddress = $this->_db->insert($this->_table->name . '_faculty', array(
              'fa_id'         => $newFacultyAddressID,
              'faculty_id'    => $facultyID,
              'address_id'    => $tmpAddressID,
              'fa_status'     => 1
            ));
            if ($updateAddress->errorStatus() === false) {
              // address changed, remove previous addresses
              $removePreviousAddresses = $this->_db->update($this->_table->name . '_faculty', '`fa_status` = ?', '`faculty_id` = ? AND `fa_id` != ?', array(-1, $facultyID, $newFacultyAddressID));
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

  // parser
  private function __parseAddressData($data = null)
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

  // error handeling and reporting
  private function setError($error = null)
  {
    $this->setErrorStatus(true);
    if ($error) {
      $this->_error = $error;
    } else {
      $this->_error = 'Oops... An unknown error occurred!';
    }
  }

  public function getError()
  {
    if (!is_null($this->_error)) {
      return $this->_error;
    } else {
      return 'Oops... An unknown error occurred!';
    }
  }

  private function setErrorStatus($errorStatus = true)
  {
    $this->_errorStatus = $errorStatus;
  }

  private function errorStatus()
  {
    return $this->_errorStatus;
  }
}
