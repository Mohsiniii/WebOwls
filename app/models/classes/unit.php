<?php
  class Unit extends custom_error {
    private $_db          = null,
            $_table       = null;

    public function __construct(){
      $this->_db = DB::getInstance();
      $this->_table = (object)[
        'name'    => 'units',
        'fields'  => '`unit_id`, `name`'
      ];
    }

    public function find($unitID = null){
      $dem = 'Failed to find unit.';
      if(is_null($unitID) === false){
        $unit = $this->_db->get($this->_table->fields, $this->_table->name, '`unit_id` = ?', array($unitID));
        if($unit->errorStatus() === false){
          if($unit->dataCount() == 1){
            // unit found
            $tmpUnit = $unit->getFirstResult();
            return (object)[
              'id'      => $tmpUnit->unit_id,
              'name'    => strtoupper($tmpUnit->name)
            ];
          } else {
            // unit not found
            $this->setError($dem);
            return null;
          }
        } else {
          // query processing error
          $this->setError($dem);
          return null;
        }
      } else {
        $this->setError($dem);
        return null;
      }
    }

    public function findByName($name = null){
      $dem = 'Failed to find unit.';
      if(is_null($name) === false){
        $unit = $this->_db->get($this->_table->fields, $this->_table->name, '`name` = ?', array($name));
        if($unit->errorStatus() === false){
          if($unit->dataCount() == 1){
            // unit found
            $tmpUnit = $unit->getFirstResult();
            return (object)[
              'id'      => $tmpUnit->unit_id,
              'name'    => strtoupper($tmpUnit->name)
            ];
          } else {
            // unit not found
            return null;
          }
        } else {
          // query processing error
          $this->setError($dem);
          return false;
        }
      } else {
        $this->setError($dem);
        return false;
      }
    }

    public function add($name = null){
      if(is_null($name) === false){
        $find = $this->findByName($name);
        if(is_null($find) === true){
          // not exists
          $add = $this->_db->insert($this->_table->name, array(
            'unit_id' => Hash::unique(),
            'name'    => strtoupper($name)
          ));
          if($add->errorStatus() === false){
            return true;
          } else {
            return false;
          }
        } else {
          if($find !== false AND is_null($find) === false){
            return true;
          } else {
            return false;
          }
        }
      } else {
        return false;
      }
    }
  }
?>