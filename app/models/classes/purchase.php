<?php
  class Purchase extends custom_error {

    private $_db        = null,
            $_table     = null,
            $_supplier  = null,
            $_storeItem = null;

    public function __construct(){
      $this->_db        = DB::getInstance();
      $this->_supplier  = new Supplier();
      $this->_table = (object)[
        'name'      => 'purchases',
        'fields'    => '`purchase_id`, `receipt_no`, `supplier_id`, `gross_total`, `discount`, `purchased_on`'
      ];
      $this->_storeItem = new StoreItem();
    }

    public function find($purchaseID = null, $params = null){
      if(is_null($purchaseID) === false){
        $findPurchase = $this->_db->get($this->_table->fields, $this->_table->name, '`purchase_id` = ?', array($purchaseID));
        if($findPurchase->errorStatus() === false){
          if($findPurchase->dataCount() == 1){
            $purchase = $findPurchase->getFirstResult();
            $response = (object)[
              'id'          => $purchase->purchase_id,
              'receiptNo'   => $purchase->receipt_no,
              'supplier'    => $this->_supplier->find($purchase->supplier_id),
              'bill'        => (object)[
                'grossTotal'    => Input::roundMoney($purchase->gross_total),
                'discount'      => Input::roundMoney($purchase->discount),
                'netTotal'      => Input::roundMoney(($purchase->gross_total-$purchase->discount))
              ],
              'payment'     => null,
              'date'        => $purchase->purchased_on
            ];
            if(is_null($params) === false AND is_object($params) === true){
              $params = (object)$params;
              if(isset($params->payment) === true AND $params->payment === true){
                $response->payment = $this->getPaymentDetails($purchase->purchase_id);
              }
            }
            return $response;
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
    
    public function getAll($storeID = null, $params = null){
      if(is_null($storeID) === false){
        $findPurchases = $this->_db->get($this->_table->fields, $this->_table->name, '`store_id` = ? AND `purchase_status` = ? ORDER BY `purchased_on` DESC', array($storeID, 1));
        if($findPurchases->errorStatus() === false){
          if($findPurchases->dataCount() > 0){
            $purchases = array();
            foreach($findPurchases->getResults() as $purchase){
              array_push($purchases, $this->find($purchase->purchase_id, $params));
            }
            if(count($purchases) > 0){
              return $purchases;
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

    public function getSupplierSupplies($storeID = null, $supplierID = null){
      $dem = 'Supplies not found.';
      if(is_null($storeID) === false AND is_null($supplierID) === false){
        $findPurchases = $this->_db->get($this->_table->fields, $this->_table->name, '`store_id` = ? AND `supplier_id` = ? AND `purchase_status` = ? ORDER BY `purchased_on` DESC', array($storeID, $supplierID, 1));
        if($findPurchases->errorStatus() === false){
          if($findPurchases->dataCount() > 0){
            $purchases = array();
            foreach($findPurchases->getResults() as $purchase){
              array_push($purchases, $this->find($purchase->purchase_id));
            }
            if(count($purchases) > 0){
              return $purchases;
            } else {
              $this->setError($dem);
              return null;
            }
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

    public function getPaymentDetails($purchaseID = null){
      if(is_null($purchaseID) === false){
				$findPayments = $this->_db->get('`pt_id`', 'purchases_payments', '`purchase_id` = ? AND `pp_status` = ?', array($purchaseID, 1));
				if($findPayments->errorStatus() === false){
					if($findPayments->dataCount() > 0){
						$response = (object)[
							'total'					=> 0,
							'first'					=> 0,
							'installments'	=> 0,
							'entries'				=> array()
						];
						foreach($findPayments->getResults() as $payment){
							$findTransaction = $this->_db->get('`amount`, `type`, `major`, `added_on`', 'cash_book', '`pt_id` = ? AND `type` = ? AND `major` IN (?, ?)', array($payment->pt_id, 1, 1, 2));
              if($findTransaction->errorStatus() === false AND $findTransaction->dataCount() == 1){
								$transaction = $findTransaction->getFirstResult();
								array_push($response->entries, (object)[
									'amount'		=> $transaction->amount,
									'type'			=> $transaction->type,
									'major'			=> $transaction->major,
									'date'			=> $transaction->added_on
								]);
								$response->total += $transaction->amount;
								if($transaction->major == 1){
									$response->first = $transaction->amount;
								} elseif($transaction->major == 2){
									$response->installments += $transaction->amount;
								}
							}
						}
						return $response;
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

    private function getReport($storeID = null, $startDate = null, $endDate = null, $slice = null){
      if(is_null($storeID) === false AND is_null($startDate) === false AND is_null($endDate) === false){
        $findPurchases = $this->_db->get('`purchase_id`, `gross_total`, `discount`', $this->_table->name, '`store_id` = ? AND date(purchased_on) >= ? AND date(purchased_on) <= ? AND `purchase_status` = ? ORDER BY `purchased_on` DESC', array($storeID, $startDate, $endDate, 1));
        if($findPurchases->errorStatus() === false){
          if($findPurchases->dataCount() > 0){
            $response = (object)[
              'total'     => 0,
              'discount'  => 0,
              'entries'   => array(),
              'items'     => array()
            ];
            foreach($findPurchases->getResults() as $purchase){
              $response->total += Input::roundMoney(($purchase->gross_total));
              $response->discount += Input::roundMoney(($purchase->discount));
              array_push($response->entries, $this->find($purchase->purchase_id));
              $stockEntries = $this->_db->get('`siv_id`, `quantity`', 'store_items_variants_stocks', '`purchase_id` = ? AND `sivs_status` = ?', array($purchase->purchase_id, 1));
              if($stockEntries->errorStatus() === false AND $stockEntries->dataCount() > 0){
                foreach($stockEntries->getResults() as $sEntry){
                  $variant = $this->_storeItem->findVariant($sEntry->siv_id, (object)['item' => true]);
                  if($variant !== false AND is_null($variant) === false){
                    if(isset($response->items[$sEntry->siv_id]) === false){
                      $response->items[$sEntry->siv_id] = (object)[
                        'siVariant'   => $variant,
                        'quantity'    => $sEntry->quantity
                      ];
                    } else {
                      $response->items[$sEntry->siv_id]->quantity += $sEntry->quantity;
                    }
                  }
                }
              }
            }
            if(count($response->items) > 0){
              array_multisort(array_column($response->items, 'quantity'), SORT_DESC, $response->items);
              if(isset($slice) === false OR is_numeric($slice) === false){
                $slice = 5;
              }
              $response->items = array_slice($response->items, 0, $slice);
            }
            return $response;
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

    public function getDailyReport($storeID = null, $date = null){
      if(is_null($storeID) === false AND is_null($date) === false){
        $report = (object)[
          'totalAmount'   => null,
          'totalDiscount' => null,
          'entries'       => null,
          'topItems'    => null
        ];
        $totalPurchases = $this->getReport($storeID, $date, $date, 5);
        if($totalPurchases !== false AND is_null($totalPurchases) === false){
          $report->totalAmount    = $totalPurchases->total;
          $report->totalDiscount  = $totalPurchases->discount;
          $report->entries        = $totalPurchases->entries;
          $report->topItems     = $totalPurchases->items;
          return $report;
        } else {
          if($totalPurchases === false){
            return false;
          } else {
            return null;
          }
        }
      } else {
        return false;
      }
    }

    public function getMonthlyReport($storeID = null, $date = null){
      if(is_null($storeID) === false AND is_null($date) === false){
        $report = (object)[
          'totalAmount'   => null,
          'totalDiscount' => null,
          'entries'       => null,
          'topItems'    => null
        ];
        $totalPurchases = $this->getReport($storeID, $date.'-01', $date.'-31', 5);
        if($totalPurchases !== false AND is_null($totalPurchases) === false){
          $report->totalAmount    = $totalPurchases->total;
          $report->totalDiscount  = $totalPurchases->discount;
          $report->entries        = $totalPurchases->entries;
          $report->topItems     = $totalPurchases->items;
          return $report;
        } else {
          if($totalPurchases === false){
            return false;
          } else {
            return null;
          }
        }
      } else {
        return false;
      }
    }

    public function getYearlyReport($storeID = null, $date = null){
      if(is_null($storeID) === false AND is_null($date) === false){
        $report = (object)[
          'totalAmount'   => null,
          'totalDiscount' => null,
          'entries'       => null,
          'topItems'    => null
        ];
        $totalPurchases = $this->getReport($storeID, $date.'-01-01', $date.'-12-31', 5);
        if($totalPurchases !== false AND is_null($totalPurchases) === false){
          $report->totalAmount    = $totalPurchases->total;
          $report->totalDiscount  = $totalPurchases->discount;
          $report->entries        = $totalPurchases->entries;
          $report->topItems     = $totalPurchases->items;
          return $report;
        } else {
          if($totalPurchases === false){
            return false;
          } else {
            return null;
          }
        }
      } else {
        return false;
      }
    }

    public function getMonthlyData($storeID = null, $year = null, $month = null){
      if(is_null($storeID) === false AND is_null($year) === false AND is_null($month) === false){
        $findPurchases = $this->_db->get('`purchase_id`, `gross_total`, `discount`', $this->_table->name, '`store_id` = ? AND year(purchased_on) = ? AND month(purchased_on) = ? AND `purchase_status` = ? ORDER BY `purchased_on` DESC', array($storeID, $year, $month, 1));
        if($findPurchases->errorStatus() === false){
          if($findPurchases->dataCount() > 0){
            $response = (object)[
              'total'     => 0,
              'entries'   => array()
            ];
            foreach($findPurchases->getResults() as $purchase){
              $response->total += Input::roundMoney(($purchase->gross_total-$purchase->discount));
              array_push($response->entries, (object)[
                'bill'      => (object)[
                  'grossTotal'    => Input::roundMoney($purchase->gross_total),
                  'discount'      => Input::roundMoney($purchase->discount),
                  'netTotal'      => Input::roundMoney(($purchase->gross_total-$purchase->discount))
                ]
              ]);
            }
            return $response;
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

    public function isReceiptNoValid($storeID = null, $receiptNo = null){
      if(is_null($storeID) === false AND is_null($receiptNo) === false){
        $check = $this->_db->get('receipt_no', $this->_table->name, '`store_id` = ? AND `receipt_no` = ? AND `purchase_status` IN (?,?)', array($storeID, $receiptNo, 1,2));
        if($check->errorStatus() === false){
          if($check->dataCount() == 0){
            return true;
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

    public function lastReceiptNo($storeID = null){
      if(is_null($storeID) === false){
        $lastSale = $this->_db->get('`receipt_no`', $this->_table->name, '`store_id` = ? ORDER BY `added_on` DESC LIMIT 0,1', array($storeID));
        if($lastSale->errorStatus() === false){
          if($lastSale->dataCount() == 1){
            $sale = $lastSale->getFirstResult();
            return (isset($sale->receipt_no) === true) ? $sale->receipt_no:null;
          } else {
            return '0';
          }
        } else {
          return null;
        }
      } else {
        return false;
      }
    }

    public function getYearlyChartData($storeID = null, $year = null){
      $dem = 'Chart data not found.';
      if(is_null($storeID) === false AND is_null($year) === false){
        $response = (object)[
          'year'      => $year,
          'data'      => (object)[
            '01'          => 0,
            '02'          => 0,
            '03'          => 0,
            '04'          => 0,
            '05'          => 0,
            '06'          => 0,
            '07'          => 0,
            '08'          => 0,
            '09'          => 0,
            '10'          => 0,
            '11'          => 0,
            '12'          => 0
          ]
        ];
        for($i = 1; $i <= 12; $i++){
          $month = ($i < 10) ? '0'.$i:$i;
          $monthlyChartData = $this->getMonthlyChartData($storeID, $response->year, $month);
          if(is_object($monthlyChartData) === true){
            $monthlyChartData = (object)$monthlyChartData;
            if(isset($monthlyChartData->data) === true AND count(get_object_vars($monthlyChartData->data)) > 0){
              foreach($monthlyChartData->data as $data){
                $response->data->$month = Input::roundMoney(($response->data->$month+$data));
              }
            }
          }
        }
        return $response;
      } else {
        $this->setError($dem);
        return false;
      }
    }

    public function getMonthlyChartData($storeID = null, $year = null, $month = null){
      $dem = 'Chart data not found.';
      if(is_null($storeID) === false AND is_null($year) === false AND is_null($month) === false){
        $response = (object)[
          'month'     => $month,
          'data'      => (object)[
            '01'          => 0,
            '02'          => 0,
            '03'          => 0,
            '04'          => 0,
            '05'          => 0,
            '06'          => 0,
            '07'          => 0,
            '08'          => 0,
            '09'          => 0,
            '10'          => 0,
            '11'          => 0,
            '12'          => 0,
            '13'          => 0,
            '14'          => 0,
            '15'          => 0,
            '16'          => 0,
            '17'          => 0,
            '18'          => 0,
            '19'          => 0,
            '20'          => 0,
            '21'          => 0,
            '22'          => 0,
            '23'          => 0,
            '24'          => 0,
            '25'          => 0,
            '26'          => 0,
            '27'          => 0,
            '28'          => 0,
            '29'          => 0,
            '30'          => 0,
            '31'          => 0
          ]
        ];
        for($i = 1; $i <= 31; $i++){
          $day = ($i < 10) ? '0'.$i:$i;
          $date = $year.'-'.$month.'-'.$day;
          $dailyChartData = $this->getDailyChartData($storeID, $date);
          if(is_object($dailyChartData) === true){
            $dailyChartData = (object)$dailyChartData;
            if(isset($dailyChartData->data) === true){
              $response->data->$day = Input::roundMoney(($response->data->$day+$dailyChartData->data));
            }
          }
        }
        return $response;
      } else {
        $this->setError($dem);
        return false;
      }
    }

    public function getDailyChartData($storeID = null, $date = null){
      $dem = 'Chart data not found.';
      if(is_null($storeID) === false AND is_null($date) === false){
        $response = (object)[
          'date'      => $date,
          'data'      => 0
        ];
        $dailyPurchases = $this->_db->get('`gross_total`, `discount`', 'purchases', '`store_id` = ? AND date(purchased_on) = ? AND `purchase_status` = ?', array($storeID, $date, 1));
        if($dailyPurchases->errorStatus() === false){
          if($dailyPurchases->dataCount() > 0){
            foreach($dailyPurchases->getResults() as $data){
              $grossTotal = (isset($data->gross_total) === true) ? $data->gross_total:null;
              $discount   = (isset($data->discount) === true) ? $data->discount:null;
              $netTotal   = Input::roundMoney(($grossTotal-$discount));
              $response->data = Input::roundMoney(($response->data+$netTotal));
            }
            return $response;
          } else {
            $this->setError('There is no record yet.');
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