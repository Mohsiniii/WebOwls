<?php
  class Supplier extends custom_error {
    // internal objects
		private $_db      = null,
            $_type 		= 'WO_RASEED_SUPPLIER',
            $_table   = null;

    public function __construct(){
      $this->_db    = DB::getInstance();
      $this->_table = (object)[
        'name'      => 'suppliers',
        'fields'    => '`supplier_id`, `name`, `contact`'
      ];
    }

    public function find($supplierID = null){
      if(is_null($supplierID) === false){
        $findSupplier = $this->_db->get($this->_table->fields, $this->_table->name, '`supplier_id` = ?', array($supplierID));
        if($findSupplier->errorStatus() === false){
          if($findSupplier->dataCount() == 0){
            return null;
          } elseif($findSupplier->dataCount() == 1){
            $supplier = $findSupplier->getFirstResult();
            return (object)[
              'id'      => $supplier->supplier_id,
              'name'    => $supplier->name,
              'contact' => $supplier->contact
            ];
          } else {
            return false;
          }
        } else {
          return false;
        }
      } else {
        return false;
      }
    }

    public function getAll($storeID = null){
      $dem = 'Buyers not found';
      if(is_null($storeID) === false){
        $findBuyers = $this->_db->get('`supplier_id`', $this->_table->name, '`store_id` = ?', array($storeID));
        if($findBuyers->errorStatus() === false){
          if($findBuyers->dataCount() == 0){
            return null;
          } elseif($findBuyers->dataCount() > 0){
            $tmpBuyers = array();
            foreach($findBuyers->getResults() as $b){
              array_push($tmpBuyers, $this->find($b->supplier_id));
            }
            if(count($tmpBuyers) > 0){
              return $tmpBuyers;
            } else {
              $this->setError($dem);
              return null;
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

    public function findByNC($name = null, $contact = null, $storeID = null){
      if(is_null($name) === false AND is_null($contact) === false AND is_null($storeID) === false){
        $findSupplier = $this->_db->get($this->_table->fields, $this->_table->name, '`name` = ? AND `store_id` = ? AND `supplier_status` = ?', array($name, $storeID, 1));
        if($findSupplier->errorStatus() === false){
          if($findSupplier->dataCount() == 0){
            return null;
          } elseif($findSupplier->dataCount() == 1){
            $supplier = $findSupplier->getFirstResult();
            return (object)[
              'id'      => $supplier->supplier_id,
              'name'    => $supplier->name,
              'contact' => $supplier->contact
            ];
          } else {
            return false;
          }
        } else {
          return false;
        }
      } else {
        return false;
      }
    }
  }
?>