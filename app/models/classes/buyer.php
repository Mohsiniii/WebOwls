<?php
class Buyer extends custom_error
{
  // internal objects
  private $_db      = null,
    $_type     = 'WO_RASEED_BUYER',
    $_table   = null;

  public function __construct()
  {
    $this->_db    = DB::getInstance();
    $this->_table = (object)[
      'name'          => 'buyers',
      'fields'        => '`buyer_id`, `name`, `contact`, `area`'
    ];
  }

  public function find($buyerID = null)
  {
    if (is_null($buyerID) === false) {
      $findBuyer = $this->_db->get($this->_table->fields, $this->_table->name, '`buyer_id` = ?', array($buyerID));
      if ($findBuyer->errorStatus() === false) {
        if ($findBuyer->dataCount() == 0) {
          return null;
        } elseif ($findBuyer->dataCount() == 1) {
          $buyer = $findBuyer->getFirstResult();
          return (object)[
            'id'      => $buyer->buyer_id,
            'name'    => $buyer->name,
            'contact' => $buyer->contact,
            'area'    => $buyer->area
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

  public function getAll($storeID = null)
  {
    $dem = 'Buyers not found';
    if (is_null($storeID) === false) {
      $findBuyers = $this->_db->get('`buyer_id`', $this->_table->name, '`store_id` = ?', array($storeID));
      if ($findBuyers->errorStatus() === false) {
        if ($findBuyers->dataCount() == 0) {
          return null;
        } elseif ($findBuyers->dataCount() > 0) {
          $tmpBuyers = array();
          foreach ($findBuyers->getResults() as $b) {
            array_push($tmpBuyers, $this->find($b->buyer_id));
          }
          if (count($tmpBuyers) > 0) {
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

  public function findByNC($name = null, $contact = null, $storeID = null)
  {
    if (is_null($name) === false and is_null($contact) === false and is_null($storeID) === false) {
      $findBuyer = $this->_db->get($this->_table->fields, $this->_table->name, '`name` = ? AND `store_id` = ? AND `buyer_status` = ?', array($name, $storeID, 1));
      if ($findBuyer->errorStatus() === false) {
        if ($findBuyer->dataCount() == 0) {
          return null;
        } elseif ($findBuyer->dataCount() == 1) {
          $buyer = $findBuyer->getFirstResult();
          return (object)[
            'id'      => $buyer->buyer_id,
            'name'    => $buyer->name,
            'contact' => $buyer->contact
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
