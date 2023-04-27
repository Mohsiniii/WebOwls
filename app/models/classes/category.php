<?php
  class Category extends custom_error {
    
    private $_db          = null,
            $_table       = null;

    public function __construct(){
      $this->_db = DB::getInstance();
      $this->_table = (object)[
        'name'    => 'categories',
        'fields'  => '`category_id`, `name`'
      ];
    }

    public function find($categoryID = null){
      $dem = 'Failed to find category.';
      if(is_null($categoryID) === false){
        $category = $this->_db->get($this->_table->fields, $this->_table->name, '`category_id` = ?', array($categoryID));
        if($category->errorStatus() === false){
          if($category->dataCount() == 1){
            // category found
            $tmpCategory = $category->getFirstResult();
            return (object)[
              'id'      => $tmpCategory->category_id,
              'name'    => strtoupper($tmpCategory->name)
            ];
          } else {
            // category not found
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
      $dem = 'Failed to find category.';
      $response = (object)[
        'status'      => null, // ture -> processed, false -> processing error
        'type'        => null,
        'data'      => null
      ];
      if(is_null($name) === false){
        $category = $this->_db->get($this->_table->fields, $this->_table->name, '`name` = ?', array($name));
        if($category->errorStatus() === false){
          $response->status = true;
          if($category->dataCount() == 1){
            // category found
            $tmpCategory = $category->getFirstResult();
            $response->data = array();
            array_push($response->data, (object)[
              'id'      => $tmpCategory->category_id,
              'name'    => strtoupper($tmpCategory->name)
            ]);
          } else {
            // category not found
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