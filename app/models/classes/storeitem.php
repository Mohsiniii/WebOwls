<?php
class StoreItem extends custom_error
{
  private $_db          = null,
    $_item         = null,
    $_category    = null,
    $_unit        = null,
    $_brand       = null,
    $_option      = null,
    $_table       = null;

  public function __construct()
  {
    $this->_db        = DB::getInstance();
    $this->_item      = new Item();
    $this->_category  = new Category();
    $this->_unit      = new Unit();
    $this->_brand     = new Brand();
    $this->_option    = new Option();
    $this->_table     = (object)[
      'name'      => 'store_items',
      'fields'    => '`si_id`, `item_id`, `category_id`, `brand_id`, `general_salt`, `box_units`'
    ];
  }

  public function find($storeItemID = null, $params = null)
  {
    $dem = 'Failed to find store items.';
    if (is_null($storeItemID) === false) {
      $findStoreItem = $this->_db->get($this->_table->fields, $this->_table->name, '`si_id` = ?', array($storeItemID));
      if ($findStoreItem->errorStatus() === false) {
        if ($findStoreItem->dataCount() == 1) {
          $storeItem = $findStoreItem->getFirstResult();
          $response = (object)[
            'id'          => $storeItem->si_id,
            'item'        => $this->_item->find($storeItem->item_id),
            'category'    => $this->_category->find($storeItem->category_id),
            'brand'       => $this->_brand->find($storeItem->brand_id),
            'general'     => $storeItem->general_salt,
            'units'       => $storeItem->box_units,
            'options'     => $this->getOptions($storeItem->si_id),
            'variants'    => null
          ];
          if (is_object($params) === true) {
            $params = (object)$params;
            if (count(get_object_vars($params)) > 0) {
              if (isset($params->variants) === true) {
                $response->variants = $this->getVariants($storeItem->si_id, $params->variants);
              }
            }
          }
          return $response;
        } else {
          $this->setError('Currently there is not any item in the store.');
          return null;
        }
      } else {
        $this->setError($dem);
        return null;
      }
    } else {
      $this->setError($dem);
      return null;
    }
  }

  public function findbyNameICB($itemName = null, $categoryName = null, $brandName = null, $storeID = null)
  {
    $dem = 'Failed to find item.';
    $response = (object)[
      'status'    => null, // ture -> processed, false -> processing error
      'type'      => null,
      'data'      => null
    ];
    $response->status = false;
    if (is_null($itemName) === false and is_null($categoryName) === false and is_null($brandName) === false and is_null($storeID) === false) {
      // find item
      $item = $this->_item->findByName($itemName);
      if (isset($item->status) === true or $item->status === true) {
        // processed successfully, check data
        if (isset($item->data) === true and is_array($item->data) === true and count($item->data) == 1) {
          // item found
          $item->data = $item->data[0];
          $category = $this->_category->findByName($categoryName);
          if (isset($category->status) === true and $category->status === true) {
            // processed successfully, check data
            if (isset($category->data) === true and is_array($category->data) === true and count($category->data) == 1) {
              // category found
              $category->data = $category->data[0];
              $brand = $this->_brand->findByName($brandName);
              if (isset($brand->status) === true and $brand->status === true) {
                // processed successfully, check data
                if (isset($brand->data) === true and is_array($brand->data) === true and count($brand->data) == 1) {
                  // brand found
                  $brand->data = $brand->data[0];
                  $storeItem = $this->_db->get($this->_table->fields, $this->_table->name, '`item_id` = ? AND `category_id` = ? AND `brand_id` = ? AND `store_id` = ? AND `si_status` = ?', array($item->data->id, $category->data->id, $brand->data->id, $storeID, 1));
                  if ($storeItem->errorStatus() === false) {
                    $response->status = true;
                    $response->data = array();
                    if ($storeItem->dataCount() == 1) {
                      $tmpStoreItem = $storeItem->getFirstResult();
                      array_push($response->data, (object)[
                        'id'        => $tmpStoreItem->si_id,
                        'item'      => $item->data,
                        'category'  => $category->data,
                        'brand'     => $brand->data
                      ]);
                    } else {
                      // store item not found
                    }
                  } else {
                    $response->status = false;
                  }
                  return $response;
                } else {
                  // brand not found
                  $response->status = true;
                }
              } else {
                // processing failed
                $response->status = false;
              }
            } else {
              // category not found
              $response->status = true;
            }
          } else {
            // processing failed
            $response->status = false;
          }
          return $response;
        } else {
          // item not found
          $response->status = true;
        }
        return $response;
      } else {
        // processing error
        $this->setError($dem);
        return $response;
      }
    } else {
      $this->setError($dem);
      return $response;
    }
  }

  public function findByIDICB($itemID = null, $categoryID = null, $brandID = null, $storeID = null)
  {
    $dem = 'Store item not found.';
    $response = (object)[
      'status'    => null,
      'data'      => null
    ];
    $response->status = false;
    if (is_null($itemID) === false and is_null($categoryID) === false and is_null($brandID) === false) {
      $findStoreItem = $this->_db->get($this->_table->fields, $this->_table->name, '`item_id` = ? AND `category_id` = ? AND `brand_id` = ? AND `store_id` = ? AND `si_status` = ?', array($itemID, $categoryID, $brandID, $storeID, 1));
      if ($findStoreItem->errorStatus() === false) {
        $response->status = true;
        if ($findStoreItem->dataCount() == 1) {
          $response->data = array();
          $storeItem = $findStoreItem->getFirstResult();
          array_push($response->data, (object)[
            'id'        => $storeItem->si_id,
            'item'      => $storeItem->item_id,
            'category'  => $storeItem->category_id,
            'brand'     => $storeItem->brand_id
          ]);
        } else {
          $this->setError($dem);
        }
        return $response;
      } else {
        $this->setError($dem);
        return $response;
      }
    } else {
      $this->setError($dem);
      return $response;
    }
  }

  public function findBySIVID($sivID = null)
  {
    if (is_null($sivID) === false) {
      $findSIV = $this->_db->get('`si_id`', 'store_items_variants', '`siv_id` = ?', array($sivID));
      if ($findSIV->errorStatus() === false and $findSIV->dataCount() == 1) {
        $storeItem = $this->find($findSIV->getFirstResult()->si_id);
        if ($storeItem !== false and is_null($storeItem) === false) {
          return $storeItem;
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
    $dem = 'Failed to find store items.';
    if (is_null($storeID) === false) {
      $findStoreItems = $this->_db->get($this->_table->fields, $this->_table->name, '`store_id` = ? AND `si_status` = ? ORDER BY `added_on` DESC', array($storeID, 1));
      if ($findStoreItems->errorStatus() === false) {
        if ($findStoreItems->dataCount() > 0) {
          $tmpStoreItems = array();
          foreach ($findStoreItems->getResults() as $si) {
            array_push($tmpStoreItems, $this->find($si->si_id, $params));
          }
          if (count($tmpStoreItems) > 0) {
            return $tmpStoreItems;
          } else {
            $this->setError($dem);
            return null;
          }
        } else {
          $this->setError('Currently there is not any item in the store.');
          return null;
        }
      } else {
        $this->setError($dem);
        return null;
      }
    } else {
      $this->setError($dem);
      return null;
    }
  }

  public function getOptions($storeItemID = null)
  {
    if (is_null($storeItemID) === false) {
      $findOptions = $this->_db->get('`sio_id`, `io_id`', 'store_items_options', '`si_id` = ? AND `sio_status` = ? ORDER BY `added_on` ASC', array($storeItemID, 1));
      if ($findOptions->errorStatus() === false and $findOptions->dataCount() > 0) {
        $tmpOptions = array();
        foreach ($findOptions->getResults() as $opt) {
          array_push($tmpOptions, (object)[
            'id'        => $opt->sio_id,
            'option'    => $this->_option->find($opt->io_id)
          ]);
        }
        if (count($tmpOptions) > 0) {
          return $tmpOptions;
        } else {
          return null;
        }
      } else {
        return null;
      }
    } else {
      return null;
    }
  }

  public function getVariants($storeItemID = null, $params = null)
  {
    if (is_null($storeItemID) === false) {
      $findVariants = $this->_db->get('`siv_id`, `si_id`', 'store_items_variants', '`si_id` = ? AND `siv_status` = ? ORDER BY `added_on` ASC', array($storeItemID, 1));
      if ($findVariants->errorStatus() === false and $findVariants->dataCount() > 0) {
        $tmpVariants = array();
        foreach ($findVariants->getResults() as $var) {
          $response = (object)[
            'id'        => $var->siv_id,
            'options'   => $this->_getVariantOptions($var->siv_id),
            'stock'     => null,
            'price'     => null,
            'discount'  => null
          ];
          if (is_object($params) === true) {
            $params = (object)$params;
            if (count(get_object_vars($params)) > 0) {
              if (isset($params->stock) === true and $params->stock === true) {
                $response->stock = $this->getVariantStock($var->siv_id);
              }
              if (isset($params->price) === true and $params->price === true) {
                $response->price = $this->_getVariantPrice($var->siv_id);
              }
              if (isset($params->discount) === true and $params->discount === true) {
                $response->discount = $this->_getVariantDiscount($var->siv_id);
              }
            }
          }
          array_push($tmpVariants, $response);
        }
        if (count($tmpVariants) > 0) {
          return $tmpVariants;
        } else {
          return null;
        }
      } else {
        return null;
      }
    } else {
      return null;
    }
  }
  public function findVariant($sivID = null, $params = null)
  {
    if (is_null($sivID) === false) {
      $findVariant = $this->_db->get('`siv_id`, `si_id`', 'store_items_variants', '`siv_id` = ? AND `siv_status` = ? ORDER BY `added_on` ASC', array($sivID, 1));
      if ($findVariant->errorStatus() === false and $findVariant->dataCount() == 1) {
        $variant = $findVariant->getFirstResult();
        $response = (object)[
          'id'        => $variant->siv_id,
          'item'      => null,
          'options'   => $this->_getVariantOptions($variant->siv_id),
          'stock'     => null,
          'price'     => null,
          'discount'  => null
        ];
        if (is_object($params) === true) {
          $params = (object)$params;
          if (count(get_object_vars($params)) > 0) {
            if (isset($params->item) === true and $params->item === true) {
              $response->item = $this->findBySIVID($sivID);
            }
            if (isset($params->stock) === true and $params->stock === true) {
              $response->stock = $this->getVariantStock($variant->siv_id);
            }
            if (isset($params->price) === true and $params->price === true) {
              $response->price = $this->_getVariantPrice($variant->siv_id);
            }
            if (isset($params->discount) === true and $params->discount === true) {
              $response->discount = $this->_getVariantDiscount($variant->siv_id);
            }
          }
        }
        return $response;
      } else {
        return null;
      }
    } else {
      return null;
    }
  }
  private function _getVariantOptions($sivID = null)
  {
    if (is_null($sivID) === false) {
      $findVarOptions = $this->_db->get('`sivo_id`, `siv_id`, `sio_id`, `sio_value`, `unit_id`', 'store_items_variants_options', '`siv_id` = ? AND `sivo_status` = ? ORDER BY `sio_id` DESC', array($sivID, 1));
      if ($findVarOptions->errorStatus() === false and $findVarOptions->dataCount() > 0) {
        $tmpVarOptions = array();
        foreach ($findVarOptions->getResults() as $vop) {
          $tmpOpt = $this->_findStoreItemOption($vop->sio_id);
          $tmpUnit = $this->_unit->find($vop->unit_id);
          array_push($tmpVarOptions, (object)[
            'id'      => $vop->sivo_id,
            'option'  => (isset($tmpOpt->option->name) === true) ? $tmpOpt->option->name : null,
            'value'   => strtoupper($vop->sio_value),
            'unit'    => (isset($tmpUnit->name) === true) ? $tmpUnit->name : null,
          ]);
        }
        if (count($tmpVarOptions) > 0) {
          return $tmpVarOptions;
        } else {
          return null;
        }
      } else {
        return null;
      }
    } else {
      return null;
    }
  }
  public function getVariantStock($sivID = null)
  {
    if (is_null($sivID) === false) {
      $findStock = $this->_db->get('`remaining`', 'store_items_variants_stocks', '`siv_id` = ? AND `sivs_status` = ?', array($sivID, 1));
      if ($findStock->errorStatus() === false and $findStock->dataCount() > 0) {
        $totalStock = 0;
        foreach ($findStock->getResults() as $stock) {
          $totalStock += $stock->remaining;
        }
        return $totalStock;
      } else {
        return null;
      }
    } else {
      return null;
    }
  }
  public function getVariantStockEntries($sivID = null)
  {
    if (is_null($sivID) === false) {
      $findStock = $this->_db->get('`sivs_id`, `remaining`', 'store_items_variants_stocks', '`siv_id` = ? AND `sivs_status` = ?  ORDER BY `added_on` ASC', array($sivID, 1));
      if ($findStock->errorStatus() === false and $findStock->dataCount() > 0) {
        $stockEntries = array();
        foreach ($findStock->getResults() as $stock) {
          array_push($stockEntries, (object)[
            'id'          => $stock->sivs_id,
            'quantity'    => $stock->remaining
          ]);
        }
        if (count($stockEntries) > 0) {
          return $stockEntries;
        } else {
          return null;
        }
      } else {
        return null;
      }
    } else {
      return null;
    }
  }
  private function _getVariantPrice($sivID = null)
  {
    if (is_null($sivID) === false) {
      $findPrice = $this->_db->get('`amount`', 'store_items_variants_prices', '`siv_id` = ? AND `sivp_status` = ?', array($sivID, 1));
      if ($findPrice->errorStatus() === false and $findPrice->dataCount() == 1) {
        $tmpPrice = $findPrice->getFirstResult()->amount;
        $response = (object)[
          'actual'      => $tmpPrice,
          'discounted'  => $tmpPrice
        ];
        $discount = $this->_getVariantDiscount($sivID);
        if (is_null($discount) === false) {
          if (isset($discount->amount) === true and $discount->amount > 0) {
            $response->discounted = round(($tmpPrice - (($tmpPrice * $discount->amount) / 100)), 2, PHP_ROUND_HALF_UP);
          }
        }
        return $response;
      } else {
        return null;
      }
    } else {
      return null;
    }
  }
  private function _getVariantDiscount($sivID = null)
  {
    if (is_null($sivID) === false) {
      $findDiscount = $this->_db->get('`sivd_id`, `amount`', 'store_items_variants_discounts', '`siv_id` = ? AND `sivd_status` = ?', array($sivID, 1));
      if ($findDiscount->errorStatus() === false and $findDiscount->dataCount() == 1) {
        $tmpDiscount = $findDiscount->getFirstResult();
        return (object)[
          'id'      => $tmpDiscount->sivd_id,
          'amount'  => $tmpDiscount->amount
        ];
      } else {
        return null;
      }
    } else {
      return null;
    }
  }


  private function _findStoreItemOption($sioID = null)
  {
    if (is_null($sioID) === false) {
      $findOption = $this->_db->get('`sio_id`, `io_id`', 'store_items_options', '`sio_id` = ? ORDER BY `added_on` ASC', array($sioID));
      if ($findOption->errorStatus() === false and $findOption->dataCount() == 1) {
        $option = $findOption->getFirstResult();
        return (object)[
          'id'        => $option->sio_id,
          'option'    => $this->_option->find($option->io_id)
        ];
      } else {
        return null;
      }
    } else {
      return null;
    }
  }

  public function getBrands($storeID = null)
  {
    $findBrands = $this->_db->get('`brand_id` as `BID`', $this->_table->name, '`store_id` = ? AND `si_status` = ? GROUP BY `brand_id` DESC', array($storeID, 1));
    if ($findBrands->errorStatus() === false and $findBrands->dataCount() > 0) {
      $brands = array();
      foreach ($findBrands->getResults() as $brand) {
        array_push($brands, $this->_brand->find($brand->BID));
      }
      return $brands;
    } else {
      return null;
    }
  }
}
