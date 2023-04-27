<?php
  class Expense extends custom_error {
    private $_db        = null,
            $_table     = null,
            $_cashBook  = null;

    public function __construct(){
      $this->_db        = DB::getInstance();
      $this->_cashBook  = new CashBook();
      $this->_table     = (object)[
        'name'          => 'expenses',
        'fields'        => '`expense_id`, `ec_id`, `pt_id`, `note`, `added_on`'
      ];
    }

    public function find($expenseID = null){
      $dem = 'Expense not found.';
      if(is_null($expenseID) === false){
        $findExpense = $this->_db->get($this->_table->fields, $this->_table->name, '`expense_id` = ?', array($expenseID));
        if($findExpense->errorStatus() === false){
          if($findExpense->dataCount() == 1){
            $tmpExpense = $findExpense->getFirstResult();
            $cashBookEntry = $this->_cashBook->find($tmpExpense->pt_id);
            return (object)[
              'id'        => $tmpExpense->expense_id,
              'category'  => $this->findCategory($tmpExpense->ec_id),
              'amount'    => (isset($cashBookEntry->amount) === true) ? $cashBookEntry->amount:null,
              'note'      => $tmpExpense->note,
              'addedOn'   => $tmpExpense->added_on
            ];
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

    public function findCategory($categoryID = null){
      if(is_null($categoryID) === false){
        $findCategory = $this->_db->get('`name`', 'expense_categories', '`ec_id` = ?', array($categoryID));
        if($findCategory->errorStatus() === false){
          if($findCategory->dataCount() == 1){
            $tmpCategory = $findCategory->getFirstResult();
            return (object)[
              'name'      => $tmpCategory->name
            ];
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

    public function getAll($storeID = null){
      $dem = 'Expenses not found.';
      if(is_null($storeID) === false){
        $findExpenses = $this->_db->get('`expense_id`', $this->_table->name, '`store_id` = ? AND `expense_status` = ? ORDER BY `added_on` DESC', array($storeID, 1));
        if($findExpenses->errorStatus() === false){
          if($findExpenses->dataCount() > 0){
            $tmpExpenses = array();
            foreach($findExpenses->getResults() as $expense){
              array_push($tmpExpenses, $this->find($expense->expense_id));
            }
            if(count($tmpExpenses) > 0){
              return $tmpExpenses;
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

    public function searchType($name = null){
      $dem = 'Expense type not found.';
      if(is_null($name) === false AND empty($name) === false){
        $name = strtoupper($name);
        $findType = $this->_db->get('`ec_id`', 'expense_categories', '`name` = ?', array($name));
        if($findType->errorStatus() === false){
          if($findType->dataCount() == 1){
            $expenseType = $findType->getFirstResult();
            return (object)[
              'id'      => $expenseType->ec_id
            ];
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
    }
  }
?>