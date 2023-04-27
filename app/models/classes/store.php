<?php
class Store
{
  private $_db      = null,
    $_address = null,
    $_campus  = null,
    $_student = null,
    $_class   = null,
    $_subject = null;

  private $_data        = null,
    $_table       = null,
    $_error       = null,
    $_errorStatus = false;

  public function __construct($storeID = null)
  {
    $this->_db = DB::getInstance();
    if (is_null($storeID) === false) {
      $this->_table = (object)[
        'name'          => 'stores',
        'fields'        => '`name`, `email`, `contact`, `cover_pic`'
      ];
      $this->_address = new Address();
      $tmpStore = $this->find($storeID);
      if (is_null($tmpStore) === false) {
        // $this->_campus = new Campus($tmpStore->id);
        // $this->_student = new Student();
        // $this->_class = new Klass();
        // $this->_subject = new Subject();
        // $tmpStore->campuses = $this->_campus->getAll();
        $this->setData($tmpStore);
      }
    }
  }

  public function find($storeID = null, $type = null)
  {
    if (is_null($storeID) === false) {
      switch (strtoupper($type)) {
        case 'ACTIVE':
          $type = 1;
          break;
        default:
          $type = 1;
          break;
      }
      $findStore = $this->_db->get($this->_table->fields, $this->_table->name, '`store_id` = ? AND `store_status` = ?', array($storeID, $type));
      if ($findStore->errorStatus() === false and $findStore->dataCount() == 1) {
        $tmpStore = $findStore->getFirstResult();
        return (object)[
          'id'        => $storeID,
          'name'      => $tmpStore->name,
          'email'     => $tmpStore->email,
          'contact'   => $tmpStore->contact,
          'coverPic'  => 'app' . DS . 'assets' . DS . 'images' . DS . 'store' . DS . 'cover' . DS . $tmpStore->cover_pic,
          'address'   => $this->_address->getStoreAddress($storeID),
          'campuses'  => null
        ];
      } else {
        return null;
      }
    } else {
      return null;
    }
  }

  private function getAddress($schoolID = null)
  {
    $dem = 'Address not found.';
    if (is_null($schoolID) === false) {
      //
    } else {
      $this->setError($dem);
      return false;
    }
  }

  private function setData($store = null)
  {
    if (is_null($store) === false and is_object($store) === true) {
      $this->_data = (object)$store;
    } else {
      $this->_data = null;
    }
  }

  public function getStockCheck()
  {
    return false;
  }

  public function getStudents()
  {
    $dem = 'Students not found.';
    $campuses = $this->getData('campuses');
    if (is_array($campuses) === true and count($campuses) > 0) {
      $tmpStudents = array();
      foreach ($campuses as $cam) {
        $tmpCampusStudents = $this->_student->getAll($cam->id);
        if (is_null($tmpCampusStudents) === false) {
          $tmpStudents = array_merge($tmpStudents, $tmpCampusStudents);
        }
      }
      if (count($tmpStudents) > 0) {
        return $tmpStudents;
      } else {
        $this->setError($dem);
        return null;
      }
    } else {
      $this->setError($dem);
      return null;
    }
  }

  public function getClasses()
  {
    $dem = 'Classes not found.';
    $classes = $this->_class->getAll();
    if (is_null($classes) === false and is_array($classes) === true and count($classes) > 0) {
      $schoolClasses = array();
      foreach ($classes as $cl) {
        if (isset($cl->id) === true) {
          array_push($schoolClasses, (object)[
            'id'        => $cl->id,
            'name'      => $cl->name,
            'subjects'  => $this->_subject->getAll($cl->id, $this->getData('id'))
          ]);
        }
      }
    } else {
      $this->setError($this->_school->getError());
      return null;
    }
  }

  public function getData($key = null)
  {
    if (is_null($key) === false and isset($this->_data->$key) === true) {
      return $this->_data->$key;
    } else {
      return null;
    }
  }

  public function update($data = null, $coverPic = null)
  {
  }

  // campuses
  public function findCampus($campusID = null)
  {
    $dem = 'Campus not found.';
    if ($this->_campus->find($campusID) === true) {
      return true;
    } else {
      $this->setError($dem);
      return false;
    }
  }

  // error handeling and reporting
  protected function setError($error = null)
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

  protected function setErrorStatus($errorStatus = true)
  {
    $this->_errorStatus = $errorStatus;
  }

  protected function errorStatus()
  {
    return $this->_errorStatus;
  }
}
