<?php
  class CashBook extends custom_error {
    private $_db        = null,
            $_table     = null,
            $_purchase  = null,
            $_sale      = null;

    public function __construct(){
      $this->_db        = DB::getInstance();
      $this->_table     = (object)[
        'name'              => 'cash_book',
        'fields'            => '`pt_id`, `amount`, `type`, `major`, `added_on`'
      ];
    }

    public function find($ptID = null){
      if(is_null($ptID) === false){
        $findTransaction = $this->_db->get($this->_table->fields, $this->_table->name, '`pt_id` = ? AND `pt_status` = ?', array($ptID, 1));
        if($findTransaction->errorStatus() === false AND $findTransaction->dataCount() == 1){
          $transaction = $findTransaction->getFirstResult();
          $stockTransaction = null;
          if($transaction->type == 1 AND in_array($transaction->major, array(1,2))){
            // purchase
            $this->_purchase  = new Purchase();
            $findPayment = $this->_db->get('`purchase_id`', 'purchases_payments', '`pt_id` = ?', array($transaction->pt_id));
            if($findPayment->errorStatus() === false AND $findPayment->dataCount() == 1){
              $payment = $findPayment->getFirstResult();
              $purchaseID = (isset($payment->purchase_id) === true) ? $payment->purchase_id:null;
              $stockTransaction = $this->_purchase->find($purchaseID);
            }
          } elseif($transaction->type == 2 AND in_array($transaction->major, array(3,4))){
            // sale
            $this->_sale  = new Sale();
            $findPayment = $this->_db->get('`sale_id`', 'sales_payments', '`pt_id` = ?', array($transaction->pt_id));
            if($findPayment->errorStatus() === false AND $findPayment->dataCount() == 1){
              $payment = $findPayment->getFirstResult();
              $saleID = (isset($payment->sale_id) === true) ? $payment->sale_id:null;
              $stockTransaction = $this->_sale->find($saleID);
            }
          }
          return (object)[
            'amount'		        => $transaction->amount,
            'type'			        => $transaction->type,
            'major'			        => $transaction->major,
            'date'			        => $transaction->added_on,
            'stockTransaction'  => $stockTransaction
          ];
        }
      }
    }

    public function getAll($storeID = null){
      if(is_null($storeID) === false){
        $findCashBook = $this->_db->get('`pt_id`', $this->_table->name, '`store_id` = ? AND `type` IN (?,?) AND `pt_status` = ? ORDER BY `added_on` DESC', array($storeID, 1, 2, 1));
        if($findCashBook->errorStatus() === false){
          if($findCashBook->dataCount() > 0){
            $cashBook = array();
            foreach($findCashBook->getResults() as $entry){
              array_push($cashBook, $this->find($entry->pt_id));
            }
            if(count($cashBook) > 0){
              return $cashBook;
            } else {
              return null;
            }
          } else {
            return null;
          }
        } else {
          return false;
        }
      } else {
        return false;
      }
    }

    public function getPurchases($storeID = null){
      if(is_null($storeID) === false){
        $findCashBook = $this->_db->get('`pt_id`', $this->_table->name, '`store_id` = ? AND `type` = ? AND `major` IN (?,?) AND `pt_status` = ? ORDER BY `added_on` DESC', array($storeID, 1, 1,2, 1));
        if($findCashBook->errorStatus() === false){
          if($findCashBook->dataCount() > 0){
            $cashBook = array();
            foreach($findCashBook->getResults() as $entry){
              array_push($cashBook, $this->find($entry->pt_id));
            }
            if(count($cashBook) > 0){
              return $cashBook;
            } else {
              return null;
            }
          } else {
            return null;
          }
        } else {
          return false;
        }
      } else {
        return false;
      }
    }

    public function getSales($storeID = null){
      if(is_null($storeID) === false){
        $findCashBook = $this->_db->get('`pt_id`', $this->_table->name, '`store_id` = ? AND `type` = ? AND `major` IN(?,?) AND `pt_status` = ? ORDER BY `added_on` DESC', array($storeID, 2, 3,4, 1));
        if($findCashBook->errorStatus() === false){
          if($findCashBook->dataCount() > 0){
            $cashBook = array();
            foreach($findCashBook->getResults() as $entry){
              array_push($cashBook, $this->find($entry->pt_id));
            }
            if(count($cashBook) > 0){
              return $cashBook;
            } else {
              return null;
            }
          } else {
            return null;
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