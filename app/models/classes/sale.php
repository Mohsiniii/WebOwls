<?php
class Sale extends custom_error
{

  private $_db        = null,
    $_table     = null,
    $_buyer     = null,
    $_storeItem = null;

  public function __construct()
  {
    $this->_db        = DB::getInstance();
    $this->_buyer     = new Buyer();
    $this->_table = (object)[
      'name'      => 'sales',
      'fields'    => '`sale_id`, `receipt_no`, `buyer_id`, `gross_total`, `discount`, `sold_on`, `added_on`, `dues_status`'
    ];
    $this->_storeItem = new StoreItem();
  }

  public function find($saleID = null, $params = null)
  {
    if (is_null($saleID) === false) {
      $findSale = $this->_db->get($this->_table->fields, $this->_table->name, '`sale_id` = ?', array($saleID));
      if ($findSale->errorStatus() === false) {
        if ($findSale->dataCount() == 1) {
          $sale = $findSale->getFirstResult();
          $response = (object)[
            'id'          => $sale->sale_id,
            'receiptNo'   => $sale->receipt_no,
            'buyer'       => $this->_buyer->find($sale->buyer_id),
            'bill'        => (object)[
              'grossTotal'    => Input::roundMoney($sale->gross_total),
              'discount'      => Input::roundMoney($sale->discount),
              'netTotal'      => Input::roundMoney(($sale->gross_total - $sale->discount))
            ],
            'payment'     => null,
            'dues'        => $sale->dues_status,
            'date'        => $sale->added_on
          ];
          if (is_null($params) === false and is_object($params) === true) {
            $params = (object)$params;
            if (isset($params->payment) === true and $params->payment === true) {
              $response->payment = $this->getPaymentDetails($sale->sale_id);
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

  public function getAll($storeID = null, $params = null)
  {
    if (is_null($storeID) === false) {
      $findsales = $this->_db->get($this->_table->fields, $this->_table->name, '`store_id` = ? AND `sale_status` = ? ORDER BY `sold_on` DESC', array($storeID, 1));
      if ($findsales->errorStatus() === false) {
        if ($findsales->dataCount() > 0) {
          $sales = array();
          foreach ($findsales->getResults() as $sale) {
            array_push($sales, $this->find($sale->sale_id, $params));
          }
          if (count($sales) > 0) {
            return $sales;
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

  public function getQueue($storeID = null, $params = null)
  {
    if (is_null($storeID) === false) {
      $findsales = $this->_db->get($this->_table->fields, $this->_table->name, '`store_id` = ? AND `sale_status` = ? AND `queued_on` IS NOT NULL ORDER BY `sold_on` DESC', array($storeID, 1));
      if ($findsales->errorStatus() === false) {
        if ($findsales->dataCount() > 0) {
          $sales = array();
          foreach ($findsales->getResults() as $sale) {
            array_push($sales, $this->find($sale->sale_id, $params));
          }
          if (count($sales) > 0) {
            return $sales;
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

  public function getOrderLine($saleID = null, $params = null)
  {
    $dem = 'Orderline not found.';
    if (is_null($saleID) === false) {
      $orderLine = array();
      $findOrderLine = $this->_db->get('`sol_id`, `siv_id`, `quantity`, `price`', 'sales_order_line', '`sale_id` = ? AND `sol_status` = ?', array($saleID, 1));
      if ($findOrderLine->errorStatus() === false and $findOrderLine->dataCount() > 0) {
        $orderLine = array();
        foreach ($findOrderLine->getResults() as $ol) {
          $storeItem = $this->_storeItem->findBySIVID($ol->siv_id);
          $tmp = (object)[
            'id'            => $ol->sol_id,
            'variant'        => $this->_storeItem->findVariant($ol->siv_id),
            'itemName'      => ($storeItem !== false and is_null($storeItem) === false and isset($storeItem->item->name) === true) ? $storeItem->item->name : null,
            'quantity'      => $ol->quantity,
            'price'          => Input::roundMoney($ol->price),
            'stockEntries'  => null
          ];
          if (is_object($params) === true) {
            $params = (object)$params;
            if (isset($params->stockEntries) === true and $params->stockEntries === true) {
              $findStockEntries = $this->_db->get('`solse_id`, `sivs_id`, `quantity`', 'sales_order_line_stock_entries', '`sol_id` = ? AND `solse_status` = ?', array($tmp->id, 1));
              if ($findStockEntries->errorStatus() === false and $findStockEntries->dataCount() > 0) {
                $tmp->stockEntries = array();
                foreach ($findStockEntries->getResults() as $se) {
                  array_push($tmp->stockEntries, (object)[
                    'id'        => $se->solse_id,
                    'sivsID'    => $se->sivs_id,
                    'quantity'  => $se->quantity
                  ]);
                }
              }
            }
          }
          array_push($orderLine, $tmp);
        }
        if (is_array($orderLine) === true and count($orderLine) > 0) {
          return $orderLine;
        } else {
          return null;
        }
      } else {
        return null;
      }
    } else {
      $this->setError($dem);
      return null;
    }
  }

  public function getPaymentDetails($saleID = null)
  {
    if (is_null($saleID) === false) {
      $findPayments = $this->_db->get('`sp_id`, `pt_id`', 'sales_payments', '`sale_id` = ? AND `sp_status` = ? ORDER BY `added_on` DESC', array($saleID, 1));
      if ($findPayments->errorStatus() === false) {
        if ($findPayments->dataCount() > 0) {
          $response = (object)[
            'total'          => 0,
            'first'          => 0,
            'installments'  => 0,
            'entries'        => array()
          ];
          foreach ($findPayments->getResults() as $payment) {
            $findTransaction = $this->_db->get('`pt_id`, `amount`, `type`, `major`, `added_on`', 'cash_book', '`pt_id` = ? AND `type` = ? AND `major` IN (?, ?)', array($payment->pt_id, 2, 3, 4));
            if ($findTransaction->errorStatus() === false and $findTransaction->dataCount() == 1) {
              $transaction = $findTransaction->getFirstResult();
              array_push($response->entries, (object)[
                'spID'      => $payment->sp_id,
                'ptID'      => $transaction->pt_id,
                'amount'    => $transaction->amount,
                'type'      => $transaction->type,
                'major'      => $transaction->major,
                'date'      => $transaction->added_on
              ]);
              $response->total += $transaction->amount;
              if ($transaction->major == 3) {
                $response->first = $transaction->amount;
              } elseif ($transaction->major == 4) {
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

  public function getMonthlyData($storeID = null, $year = null, $month = null)
  {
    if (is_null($storeID) === false and is_null($year) === false and is_null($month) === false) {
      $findSales = $this->_db->get('`sale_id`, `gross_total`, `discount`', $this->_table->name, '`store_id` = ? AND year(sold_on) = ? AND month(sold_on) = ? AND `sale_status` = ? ORDER BY `sold_on` DESC', array($storeID, $year, $month, 1));
      if ($findSales->errorStatus() === false) {
        if ($findSales->dataCount() > 0) {
          $response = (object)[
            'total'     => 0,
            'entries'   => array()
          ];
          foreach ($findSales->getResults() as $sale) {
            $response->total += Input::roundMoney(($sale->gross_total - $sale->discount));
            array_push($response->entries, (object)[
              'bill'      => (object)[
                'grossTotal'    => Input::roundMoney($sale->gross_total),
                'discount'      => Input::roundMoney($sale->discount),
                'netTotal'      => Input::roundMoney(($sale->gross_total - $sale->discount))
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

  private function getReport($storeID = null, $startDate = null, $endDate = null, $slice = null)
  {
    if (is_null($storeID) === false and is_null($startDate) === false and is_null($endDate) === false) {
      $findSale = $this->_db->get('`sale_id`, `gross_total`, `discount`', $this->_table->name, '`store_id` = ? AND date(sold_on) >= ? AND date(sold_on) <= ? AND `sale_status` = ? ORDER BY `sold_on` DESC', array($storeID, $startDate, $endDate, 1));
      if ($findSale->errorStatus() === false) {
        if ($findSale->dataCount() > 0) {
          $response = (object)[
            'total'     => 0,
            'discount'  => 0,
            'entries'   => array(),
            'items'     => array()
          ];
          foreach ($findSale->getResults() as $sale) {
            $response->total += Input::roundMoney(($sale->gross_total));
            $response->discount += Input::roundMoney(($sale->discount));
            array_push($response->entries, $this->find($sale->sale_id));
            // get sale orderline
            $saleOrderLine = $this->_db->get('`siv_id`, `quantity`', 'sales_order_line', '`sale_id` = ? AND `sol_status` = ?', array($sale->sale_id, 1));
            if ($saleOrderLine->errorStatus() === false and $saleOrderLine->dataCount() > 0) {
              foreach ($saleOrderLine->getResults() as $sEntry) {
                $variant = $this->_storeItem->findVariant($sEntry->siv_id, (object)['item' => true]);
                if ($variant !== false and is_null($variant) === false) {
                  if (isset($response->items[$sEntry->siv_id]) === false) {
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
          if (count($response->items) > 0) {
            array_multisort(array_column($response->items, 'quantity'), SORT_DESC, $response->items);
            if (isset($slice) === false or is_numeric($slice) === false) {
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

  private function _getCustomReport($storeID = null, $buyerID = null, $brandID = null, $dateRange = null, $slice = null)
  {
    if (is_null($storeID) === false and (is_null($buyerID) === false or is_null($brandID) === false)) {
      $findSale = null;
      if (is_null($buyerID) === false and empty($buyerID) === false) {
        $findSale = $this->_db->get('`sale_id`, `gross_total`, `discount`', $this->_table->name, '`store_id` = ? AND `buyer_id` = ? AND date(`sold_on`) >= ? AND date(`sold_on`) <= ? AND `sale_status` = ? ORDER BY `sold_on` DESC', array($storeID, $buyerID, $dateRange->start, $dateRange->end, 1));
      } else {
        $findSale = $this->_db->get('`sale_id`, `gross_total`, `discount`', $this->_table->name, '`store_id` = ? AND date(`sold_on`) >= ? AND date(`sold_on`) <= ? AND `sale_status` = ? ORDER BY `sold_on` DESC', array($storeID, $dateRange->start, $dateRange->end, 1));
      }
      if (is_null($findSale) === false and $findSale->errorStatus() === false) {
        if ($findSale->dataCount() > 0) {
          $response = (object)[
            'total'     => 0,
            'discount'  => 0,
            'brand'     => 0,
            'entries'   => array(),
            'items'     => array()
          ];
          foreach ($findSale->getResults() as $sale) {
            // get sale orderline
            $saleOrderLine = $this->_db->get('`siv_id`, `quantity`, `price`', 'sales_order_line', '`sale_id` = ? AND `sol_status` = ?', array($sale->sale_id, 1));
            if ($saleOrderLine->errorStatus() === false and $saleOrderLine->dataCount() > 0) {
              $brandFound = false;
              foreach ($saleOrderLine->getResults() as $sEntry) {
                $variant = $this->_storeItem->findVariant($sEntry->siv_id, (object)['item' => true]);
                if ($variant !== false and is_null($variant) === false) {
                  if ((is_null($brandID) === false and empty($brandID) === false) and $variant->item->brand->id == $brandID) {
                    $brandFound = true;
                    $response->brand += Input::roundMoney(($sEntry->quantity * $sEntry->price));
                  } elseif (is_null($brandID) === false and empty($brandID) === false) {
                    continue;
                  }
                  if (isset($response->items[$sEntry->siv_id]) === false) {
                    $response->items[$sEntry->siv_id] = (object)[
                      'siVariant'   => $variant,
                      'quantity'    => $sEntry->quantity
                    ];
                  } else {
                    $response->items[$sEntry->siv_id]->quantity += $sEntry->quantity;
                  }
                }
              }
              if ((is_null($brandID) or empty($brandID)) or $brandFound === true) {
                // enter sale
                $response->total += Input::roundMoney(($sale->gross_total));
                $response->discount += Input::roundMoney(($sale->discount));
                array_push($response->entries, $this->find($sale->sale_id));
              }
            }
          }
          if (count($response->items) > 0) {
            array_multisort(array_column($response->items, 'quantity'), SORT_DESC, $response->items);
            if (isset($slice) === false or is_numeric($slice) === false) {
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

  public function getDailyReport($storeID = null, $date = null)
  {
    if (is_null($storeID) === false and is_null($date) === false) {
      $report = (object)[
        'totalAmount'   => null,
        'totalDiscount' => null,
        'entries'       => null,
        'topItems'    => null
      ];
      $totalPurchases = $this->getReport($storeID, $date, $date, 5);
      if ($totalPurchases !== false and is_null($totalPurchases) === false) {
        $report->totalAmount    = $totalPurchases->total;
        $report->totalDiscount  = $totalPurchases->discount;
        $report->entries        = $totalPurchases->entries;
        $report->topItems     = $totalPurchases->items;
        return $report;
      } else {
        if ($totalPurchases === false) {
          return false;
        } else {
          return null;
        }
      }
    } else {
      return false;
    }
  }

  public function getMonthlyReport($storeID = null, $date = null)
  {
    if (is_null($storeID) === false and is_null($date) === false) {
      $report = (object)[
        'totalAmount'   => null,
        'totalDiscount' => null,
        'entries'       => null,
        'topItems'    => null
      ];
      $totalPurchases = $this->getReport($storeID, $date . '-01', $date . '-31', 5);
      if ($totalPurchases !== false and is_null($totalPurchases) === false) {
        $report->totalAmount    = $totalPurchases->total;
        $report->totalDiscount  = $totalPurchases->discount;
        $report->entries        = $totalPurchases->entries;
        $report->topItems     = $totalPurchases->items;
        return $report;
      } else {
        if ($totalPurchases === false) {
          return false;
        } else {
          return null;
        }
      }
    } else {
      return false;
    }
  }

  public function getYearlyReport($storeID = null, $date = null)
  {
    if (is_null($storeID) === false and is_null($date) === false) {
      $report = (object)[
        'totalAmount'   => null,
        'totalDiscount' => null,
        'entries'       => null,
        'topItems'    => null
      ];
      $totalPurchases = $this->getReport($storeID, $date . '-01-01', $date . '-12-31', 5);
      if ($totalPurchases !== false and is_null($totalPurchases) === false) {
        $report->totalAmount    = $totalPurchases->total;
        $report->totalDiscount  = $totalPurchases->discount;
        $report->entries        = $totalPurchases->entries;
        $report->topItems     = $totalPurchases->items;
        return $report;
      } else {
        if ($totalPurchases === false) {
          return false;
        } else {
          return null;
        }
      }
    } else {
      return false;
    }
  }

  public function getCustomReport($storeID = null, $customer = null, $brand = null, $dateRange = null)
  {
    if (is_null($storeID) === false) {
      $report = (object)[
        'totalAmount'   => null,
        'totalDiscount' => null,
        'totalBrand'    => null,
        'entries'       => null,
        'topItems'    => null
      ];
      $totalSales = $this->_getCustomReport($storeID, $customer, $brand, $dateRange, 5);
      if ($totalSales !== false and is_null($totalSales) === false) {
        $report->totalAmount    = $totalSales->total;
        $report->totalDiscount  = $totalSales->discount;
        $report->totalBrand     = $totalSales->brand;
        $report->entries        = $totalSales->entries;
        $report->topItems     = $totalSales->items;
        return $report;
      } else {
        if ($totalSales === false) {
          return false;
        } else {
          return null;
        }
      }
    } else {
      return false;
    }
  }

  public function getMonthlyRevenueReport($storeID = null, $year = null, $month = null)
  {
    $dem = 'Revenue not found.';
    if (is_null($storeID) === false and is_null($year) === false and is_null($month) === false) {
      $revenue = (object)[
        'salePrice'     => 0,
        'costPrice'     => 0,
        'revenue'       => 0
      ];
      for ($i = 1; $i <= 32; $i++) {
        $day = ($i < 10) ? '0' . $i : $i;
        $dailyRevenueReport = $this->getDailyRevenueReport($storeID, $year . '-' . $month . '-' . $day);
        if ($this->_parseRevenueReport($dailyRevenueReport) === true) {
          $revenue->salePrice = Input::roundMoney(($revenue->salePrice + $dailyRevenueReport->salePrice));
          $revenue->costPrice = Input::roundMoney(($revenue->costPrice + $dailyRevenueReport->costPrice));
          $revenue->revenue   = Input::roundMoney(($revenue->revenue + $dailyRevenueReport->revenue));
        }
      }
      return $revenue;
    } else {
      $this->setError($dem);
      return null;
    }
  }

  public function getYearlyRevenueReport($storeID = null, $year = null)
  {
    $dem = 'Revenue not found.';
    if (is_null($storeID) === false and is_null($year) === false) {
      $revenue = (object)[
        'salePrice'     => 0,
        'costPrice'     => 0,
        'revenue'       => 0
      ];
      for ($i = 1; $i <= 12; $i++) {
        $month = ($i < 10) ? '0' . $i : $i;
        $monthlyRevenueReport = $this->getMonthlyRevenueReport($storeID, $year, $month);
        if ($this->_parseRevenueReport($monthlyRevenueReport) === true) {
          $revenue->salePrice = Input::roundMoney(($revenue->salePrice + $monthlyRevenueReport->salePrice));
          $revenue->costPrice = Input::roundMoney(($revenue->costPrice + $monthlyRevenueReport->costPrice));
          $revenue->revenue   = Input::roundMoney(($revenue->revenue + $monthlyRevenueReport->revenue));
        }
      }
      return $revenue;
    } else {
      $this->setError($dem);
      return null;
    }
  }

  private function _parseRevenueReport($data = null)
  {
    if (is_object($data) === true) {
      $data = (object)$data;
      if (isset($data->salePrice) === true and isset($data->costPrice) === true and isset($data->revenue) === true) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function getDailyRevenueReport($storeID = null, $date = null)
  {
    $dem = 'Revenue not found.';
    if (is_null($storeID) === false and is_null($date) === false) {
      $daySales = $this->_db->get('`sale_id`, `gross_total`, `discount`', 'sales', '`store_id` = ? AND date(sold_on) = ? AND `sale_status` = ?', array($storeID, $date, 1));
      if ($daySales->errorStatus() === false) {
        if ($daySales->dataCount() > 0) {
          // day sales found, loop them one by one
          $revenue = (object)[
            'salePrice'     => 0,
            'costPrice'     => 0,
            'revenue'       => 0
          ];
          foreach ($daySales->getResults() as $sale) {
            $salePrice = Input::roundMoney($sale->gross_total - $sale->discount);
            $revenue->salePrice = Input::roundMoney(($revenue->salePrice + $salePrice));
            $saleCostPrice = 0;
            $salesOrderLine = $this->_db->get('`sol_id`, `siv_id`, `quantity`', 'sales_order_line', '`sale_id` = ? AND `sol_status` = ?', array($sale->sale_id, 1));
            if ($salesOrderLine->errorStatus() === false) {
              if ($salesOrderLine->dataCount() > 0) {
                // slaes order line found, loop them one by one
                foreach ($salesOrderLine->getResults() as $sol) {
                  // find stock entries
                  $solEntryCostPrice = 0;
                  $solStockEntries = $this->_db->get('`sivs_id`, `quantity`', 'sales_order_line_stock_entries', '`sol_id` = ? AND `solse_status` = ?', array($sol->sol_id, 1));
                  if ($solStockEntries->errorStatus() === false) {
                    if ($solStockEntries->dataCount() > 0) {
                      // stock entries found, loop them one by one
                      foreach ($solStockEntries->getResults() as $stockEntry) {
                        $findStockDetails = $this->_db->get('`price`', 'store_items_variants_stocks', '`sivs_id` = ?', array($stockEntry->sivs_id));
                        if ($findStockDetails->errorStatus() === false) {
                          if ($findStockDetails->dataCount() == 1) {
                            $stockDetails = $findStockDetails->getFirstResult();
                            $stockPurchasePrice = (isset($stockDetails->price) === true) ? $stockDetails->price : null;
                            $stockEntryCostPrice = Input::roundMoney(($stockEntry->quantity * $stockPurchasePrice));
                            $solEntryCostPrice = Input::roundMoney(($solEntryCostPrice + $stockEntryCostPrice));
                          } else {
                            // stock details not found
                          }
                        } else {
                          // error in finding stock details
                          return false;
                        }
                      }
                    }
                  } else {
                    // error in finding stock entries
                    return false;
                  }
                  $saleCostPrice = Input::roundMoney(($saleCostPrice + $solEntryCostPrice));
                }
              } else {
                // sales order line not found
              }
            } else {
              // error in finding sales order line
              return false;
            }
            $revenue->costPrice = Input::roundMoney(($revenue->costPrice + $saleCostPrice));
          }
          $revenue->revenue = Input::roundMoney($revenue->salePrice - $revenue->costPrice);
          return $revenue;
        } else {
          // day sales not found
          return null;
        }
      } else {
        // error in finding day sales
        return false;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  public function isReceiptNoValid($storeID = null, $receiptNo = null)
  {
    if (is_null($storeID) === false and is_null($receiptNo) === false) {
      $check = $this->_db->get('receipt_no', $this->_table->name, '`store_id` = ? AND `receipt_no` = ? AND `sale_status` IN (?,?)', array($storeID, $receiptNo, 1, 2));
      if ($check->errorStatus() === false) {
        if ($check->dataCount() == 0) {
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

  public function getDues($storeID = null)
  {
    $dem = 'Dues not found.';
    if (is_null($storeID) === false) {
      $findDues = $this->_db->get('`sale_id`', $this->_table->name, '`store_id` = ? AND `dues_status` = ? AND `sale_status` = ? ORDER BY `added_on` DESC', array($storeID, 2, 1));
      if ($findDues->errorStatus() === false) {
        if ($findDues->dataCount() > 0) {
          $tmpDues = array();
          foreach ($findDues->getResults() as $due) {
            array_push($tmpDues, $this->find($due->sale_id, (object)['payment' => true]));
          }
          if (count($tmpDues) > 0) {
            return $tmpDues;
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

  public function getBuyerDues($storeID = null)
  {
    $dem = 'Dues not found.';
    if (is_null($storeID) === false) {
      $findBuyers = $this->_db->get('`buyer_id`', $this->_table->name, '`store_id` = ? AND `dues_status` = ? AND `sale_status` = ? GROUP BY `buyer_id` ORDER BY `added_on` ASC', array($storeID, 2, 1));
      if ($findBuyers->errorStatus() === false) {
        if ($findBuyers->dataCount() > 0) {
          $tmpDues = array();
          foreach ($findBuyers->getResults() as $buyer) {
            $buyerID = $buyer->buyer_id;
            $findBuyerDues = $this->_db->get('`sale_id`', $this->_table->name, '`store_id` = ? AND `buyer_id` = ? AND `dues_status` = ? AND `sale_status` = ?', array($storeID, $buyerID, 2, 1));
            if ($findBuyerDues->errorStatus() === false and $findBuyerDues->dataCount() > 0) {
              $tmpBuyerDues = (object)[
                'buyer'     => null,
                'dues'      => (object)[
                  'total'       => 0,
                  'sales'       => array()
                ]
              ];
              foreach ($findBuyerDues->getResults() as $buyerDues) {
                $saleDetails = $this->find($buyerDues->sale_id, (object)['payment' => true]);
                if (is_null($saleDetails) === false and isset($saleDetails->buyer) === true and isset($saleDetails->bill->netTotal) === true and isset($saleDetails->payment->total) === true) {
                  if (is_null($tmpBuyerDues->buyer) === true) {
                    $tmpBuyerDues->buyer = $saleDetails->buyer;
                  }
                  $saleDues = Input::roundMoney(($saleDetails->bill->netTotal - $saleDetails->payment->total));
                  $tmpBuyerDues->dues->total += $saleDues;
                }
              }
              array_push($tmpDues, $tmpBuyerDues);
            }
          }
          if (count($tmpDues) > 0) {
            return $tmpDues;
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

  public function getBuyerDueSales($storeID = null, $buyerID = null)
  {
    $dem = 'Dues not found.';
    if (is_null($storeID) === false and is_null($buyerID) === false) {
      $findBuyerDues = $this->_db->get('`sale_id`', $this->_table->name, '`store_id` = ? AND `buyer_id` = ? AND `dues_status` = ? AND `sale_status` = ? ORDER BY `added_on` ASC', array($storeID, $buyerID, 2, 1));
      if ($findBuyerDues->errorStatus() === false) {
        if ($findBuyerDues->dataCount() > 0) {
          $tmpDueSales = array();
          foreach ($findBuyerDues->getResults() as $buyerDues) {
            array_push($tmpDueSales, $this->find($buyerDues->sale_id, (object)['payment' => true]));
          }
          if (count($tmpDueSales) > 0) {
            return $tmpDueSales;
          } else {
            $this->setError($dem);
            return false;
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

  public function getBuyerDuesHistory($buyerID = null)
  {
    $dem = 'Dues not found.';
    if (is_null($buyerID) === false) {
      $findFirstDueEntry = $this->_db->get('`sale_id`, `added_on`', $this->_table->name, '`buyer_id` = ? AND `dues_status` = ? AND `sale_status` = ? ORDER BY `added_on` ASC LIMIT 0,1', array($buyerID, 2, 1));
      if ($findFirstDueEntry->errorStatus() === false and $findFirstDueEntry->dataCount() == 1) {
        $firstDueEntry = $findFirstDueEntry->getFirstResult();
        $saleEntries = $this->_db->get('`sale_id`, `added_on`', $this->_table->name, '`buyer_id` = ? AND `added_on` >= ? AND `sale_status` = ? ORDER BY `added_on` ASC', array($buyerID, $firstDueEntry->added_on, 1));
        if ($saleEntries->errorStatus() === false and $saleEntries->dataCount() > 0) {
          $tmpHistoryEntries = array();
          foreach ($saleEntries->getResults() as $saleEntry) {
            array_push($tmpHistoryEntries, $this->find($saleEntry->sale_id, (object)['payment' => true]));
          }
          if (is_array($tmpHistoryEntries) === true and count($tmpHistoryEntries) > 0) {
            return $tmpHistoryEntries;
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

  public function getBuyerSales($storeID = null, $buyerID = null)
  {
    $dem = 'Dues not found.';
    if (is_null($storeID) === false and is_null($buyerID) === false) {
      $findBuyerDues = $this->_db->get('`sale_id`', $this->_table->name, '`store_id` = ? AND `buyer_id` = ? AND `sale_status` = ? ORDER BY `added_on` ASC', array($storeID, $buyerID, 1));
      if ($findBuyerDues->errorStatus() === false) {
        if ($findBuyerDues->dataCount() > 0) {
          $tmpDueSales = array();
          foreach ($findBuyerDues->getResults() as $buyerDues) {
            array_push($tmpDueSales, $this->find($buyerDues->sale_id, (object)['payment' => true]));
          }
          if (count($tmpDueSales) > 0) {
            return $tmpDueSales;
          } else {
            $this->setError($dem);
            return false;
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

  public function lastReceiptNo($storeID = null)
  {
    if (is_null($storeID) === false) {
      $lastSale = $this->_db->get('`receipt_no`', 'sales', '`store_id` = ? ORDER BY `added_on` DESC LIMIT 0,1', array($storeID));
      if ($lastSale->errorStatus() === false) {
        if ($lastSale->dataCount() == 1) {
          $sale = $lastSale->getFirstResult();
          return (isset($sale->receipt_no) === true) ? $sale->receipt_no : null;
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

  public function getYearlyChartData($storeID = null, $year = null)
  {
    $dem = 'Chart data not found.';
    if (is_null($storeID) === false and is_null($year) === false) {
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
      for ($i = 1; $i <= 12; $i++) {
        $month = ($i < 10) ? '0' . $i : $i;
        $monthlyChartData = $this->getMonthlyChartData($storeID, $response->year, $month);
        if (is_object($monthlyChartData) === true) {
          $monthlyChartData = (object)$monthlyChartData;
          if (isset($monthlyChartData->data) === true and count(get_object_vars($monthlyChartData->data)) > 0) {
            foreach ($monthlyChartData->data as $data) {
              $response->data->$month = Input::roundMoney(($response->data->$month + $data));
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

  public function getMonthlyChartData($storeID = null, $year = null, $month = null)
  {
    $dem = 'Chart data not found.';
    if (is_null($storeID) === false and is_null($year) === false and is_null($month) === false) {
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
      for ($i = 1; $i <= 31; $i++) {
        $day = ($i < 10) ? '0' . $i : $i;
        $date = $year . '-' . $month . '-' . $day;
        $dailyChartData = $this->getDailyChartData($storeID, $date);
        if (is_object($dailyChartData) === true) {
          $dailyChartData = (object)$dailyChartData;
          if (isset($dailyChartData->data) === true) {
            $response->data->$day = Input::roundMoney(($response->data->$day + $dailyChartData->data));
          }
        }
      }
      return $response;
    } else {
      $this->setError($dem);
      return false;
    }
  }

  public function getDailyChartData($storeID = null, $date = null)
  {
    $dem = 'Chart data not found.';
    if (is_null($storeID) === false and is_null($date) === false) {
      $response = (object)[
        'date'      => $date,
        'data'      => 0
      ];
      $dailySales = $this->_db->get('`gross_total`, `discount`', 'sales', '`store_id` = ? AND date(sold_on) = ? AND `sale_status` = ?', array($storeID, $date, 1));
      if ($dailySales->errorStatus() === false) {
        if ($dailySales->dataCount() > 0) {
          foreach ($dailySales->getResults() as $data) {
            $grossTotal = (isset($data->gross_total) === true) ? $data->gross_total : null;
            $discount   = (isset($data->discount) === true) ? $data->discount : null;
            $netTotal   = Input::roundMoney(($grossTotal - $discount));
            $response->data = Input::roundMoney(($response->data + $netTotal));
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
