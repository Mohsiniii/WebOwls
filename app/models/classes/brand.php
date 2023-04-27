<?php
  class Brand extends custom_error {
    
    private $_db          = null,
            $_table       = null;

    public function __construct(){
      $this->_db = DB::getInstance();
      $this->_table = (object)[
        'name'    => 'brands',
        'fields'  => '`brand_id`, `name`'
      ];
    }

    public function find($brandID = null){
      $dem = 'Failed to find brand.';
      if(is_null($brandID) === false){
        $brand = $this->_db->get($this->_table->fields, $this->_table->name, '`brand_id` = ?', array($brandID));
        if($brand->errorStatus() === false){
          if($brand->dataCount() == 1){
            // brand found
            $tmpbrand = $brand->getFirstResult();
            return (object)[
              'id'      => $tmpbrand->brand_id,
              'name'    => strtoupper($tmpbrand->name)
            ];
          } else {
            // brand not found
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
      $dem = 'Failed to find brand.';
      $response = (object)[
        'status'      => null, // ture -> processed, false -> processing error
        'type'        => null,
        'data'      => null
      ];
      if(is_null($name) === false){
        $name = strtoupper($name);
        $brand = $this->_db->get($this->_table->fields, $this->_table->name, '`name` = ?', array($name));
        if($brand->errorStatus() === false){
          $response->status = true;
          if($brand->dataCount() == 1){
            // brand found
            $tmpbrand = $brand->getFirstResult();
            $response->data = array();
            array_push($response->data, (object)[
              'id'      => $tmpbrand->brand_id,
              'name'    => strtoupper($tmpbrand->name)
            ]);
          } else {
            // brand not found
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