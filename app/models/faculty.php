<?php
  class Faculty extends User {
    private $_table = null;
    private $_type 		= 'WO_SM_SCOOL_FACULTY';

    public function __construct(){
      parent::__construct($this->_type);
      $this->_table = (object)[
        'name'      => 'faculty',
        'fields'    => '`faculty_id`, `user_id`, `school_id`'
      ];
    }

    public function isFacultyMember($facultyID = null, $schoolID = null, $type = null){
      $dem = 'Faculty member not found.';
      if(is_null($facultyID) === false AND is_null($schoolID) === false){
        switch(strtoupper($type)){
          case 'ACTIVE':
            $type = 1;
          break;
          default:
            $type = 1;
          break;
        }
        $schoolFaculty = $this->_db->get($this->_table->fields, $this->_table->name, '`faculty_id` = ? AND `school_id` = ? AND `faculty_status` = ?', array($facultyID, $schoolID, $type));
        if($schoolFaculty->errorStatus() === false AND $schoolFaculty->dataCount() == 1){
          return true;
        } else {
          $this->setError($dem);
          return null;
        }
      } else {
        $this->setError($dem);
        return null;
      }
    }

    public function getAll($schoolID = null, $type = null){
      $dem = 'Faculty not found.';
      if(is_null($schoolID) === false){
        switch(strtoupper($type)){
          case 'ACTIVE':
            $type = 1;
          break;
          default:
            $type = 1;
          break;
        }
        $schoolFaculty = $this->_db->get($this->_table->fields, $this->_table->name, '`school_id` = ? AND `faculty_status` = ? ORDER BY `registration_date` ASC', array($schoolID, $type));
        if($schoolFaculty->errorStatus() === false AND $schoolFaculty->dataCount() > 0){
          $tmpFaculty = array();
          foreach($schoolFaculty->getResults() as $f){
            $tmpUser = $this->find($f->user_id);
            $tmpPerson = $this->_findP($tmpUser->person);
            // $this->_setUserData($tmpUser, 'ALL');
            // $this->_setUserData((object)['privateID' => $f->faculty_id], 'PRIVATE');
            // $this->_setPersonData($tmpPerson);
            array_push($tmpFaculty, (object)[
              'facultyID'     => $f->faculty_id,
              'name'          => $tmpPerson->name
            ]);
          }
          if(count($tmpFaculty) > 0){
            return $tmpFaculty;
          } else {
            $this->setError($dem);
            return null;
          }
        } else {
          $this->setError($dem);
          return null;
        }
      } else {
        $this->setError($dem);
        return null;
      }
    }
    
    public function updateAddress($facultyID = null, $address = null){
      $dem = 'Failed to update faculty address.';
      if(is_null($facultyID) === false AND $this->__parseAddressData($address) === true){
        $country = $address->country; $state = $address->state; $province = $address->province; $city = $address->city; $street = $address->street;
        $searchAddress = $this->_address->search($country, $state, $province, $city, $street);
        if($searchAddress !== false){
          $tmpAddressID = null;
          if($searchAddress === true){
            // address not found, create it
            $createAddress = $this->_address->create($country, $state, $province, $city, $street);
            if($createAddress === false){
              $this->setError($dem);
              return false;
            } else {
              $tmpAddressID = $createAddress;
            }
          } else {
            // similer address found, attach it with this campus
            $tmpAddressID = $searchAddress;
          }
          if(is_null($tmpAddressID) === false){
            // address id is set
            $tmpAddress = $this->_address->find($tmpAddressID);
            if(is_null($tmpAddress) === false){
              // valid address entity, attach it
              $newFacultyAddressID = Hash::unique();
              $updateAddress = $this->_db->insert('addresses_faculty', array(
                'fa_id'         => $newFacultyAddressID,
                'faculty_id'    => $facultyID,
                'address_id'    => $tmpAddressID,
                'fa_status'     => 1
              ));
              if($updateAddress->errorStatus() === false){
                // address changed, remove previous addresses
                $removePreviousAddresses = $this->_db->update('addresses_faculty', '`fa_status` = ?', '`faculty_id` = ? AND `fa_id` != ?', array(-1, $facultyID, $newFacultyAddressID));
                if($removePreviousAddresses->errorStatus() === false){
                  return true;
                } else {
                  $this->setError($dem.' Please immediately contact with admin.');
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
  }

?>