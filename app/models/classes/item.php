<?php
  class Item extends custom_error {
    private $_db          = null,
            $_table       = null;

    public function __construct(){
      $this->_db = DB::getInstance();
      $this->_table = (object)[
        'name'    => 'items',
        'fields'  => '`item_id`, `name`'
      ];
    }

    public function find($itemID = null){
      $dem = 'Failed to find item.';
      if(is_null($itemID) === false){
        $item = $this->_db->get($this->_table->fields, $this->_table->name, '`item_id` = ?', array($itemID));
        if($item->errorStatus() === false){
          if($item->dataCount() == 1){
            // item found
            $tmpItem = $item->getFirstResult();
            return (object)[
              'id'    => $tmpItem->item_id,
              'name'  => $tmpItem->name
            ];
          } else {
            // item not found
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
      $dem = 'Failed to find item.';
      $response = (object)[
        'status'    => null, // ture -> processed, false -> processing error
        'type'      => null,
        'data'      => null
      ];
      if(is_null($name) === false){
        $item = $this->_db->get($this->_table->fields, $this->_table->name, '`name` = ?', array($name));
        if($item->errorStatus() === false){
          $response->status = true;
          $response->data = array();
          if($item->dataCount() == 1){
            // item found
            $tmpItem = $item->getFirstResult();
            $response->data = array();
            array_push($response->data, (object)[
              'id'    => $tmpItem->item_id,
              'name'  => $tmpItem->name
            ]);
          } else {
            // item not found
          }
          return $response;
        } else {
          // query processing error
          $response->status = false;
          $this->setError($dem);
          return $response;
        }
      } else {
        $response->status = false;
        $this->setError($dem);
        return $response;
      }
    }
  }
?>