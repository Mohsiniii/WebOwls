<?php
  class Option extends custom_error {
    private $_db    = null,
            $_table = null;

    public function __construct(){
      $this->_db = DB::getInstance();
      $this->_table = (object)[
        'name'      => 'items_options',
        'fields'    => '`io_id`, `name`'
      ];
    }

    public function find($optionID = null){
      $response = (object)[
        'status'  => null,
        'data'    => null
      ];
      $response->status = false;
      if(is_null($optionID) === false){
        $find = $this->_db->get($this->_table->fields, $this->_table->name, '`io_id` = ?', array($optionID));
        if($find->errorStatus() === false AND $find->dataCount() == 1){
          $option = $find->getFirstResult();
          return (object)[
            'id'      => $option->io_id,
            'name'    => strtoupper($option->name)
          ];
        } else {
          return null;
        }
      } else {
        return null;
      }
    }

    public function findByName($name = null){
      $response = (object)[
        'status'  => null,
        'data'    => null
      ];
      $response->status = false;
      if(is_null($name) === false){
        $name = strtoupper($name);
        $find = $this->_db->get($this->_table->fields, $this->_table->name, '`name` = ?', array($name));
        if($find->errorStatus() === false){
          $response->status = true;
          if($find->dataCount() == 1){
            $option = $find->getFirstResult();
            array_push($response->data, (object)[
              'id'      => $option->io_id,
              'name'    => strtoupper($option->name)
            ]);
          }
        }
        return $response;
      } else {
        return $response;
      }
    }
  }
?>