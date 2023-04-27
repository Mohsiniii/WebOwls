<?php
class SalesMan extends User
{
  // internal objects
  private $_type     = 'WO_RASEED_SALES_MAN';
  // external objects
  private  $_store     = null,
    $_storeItem  = null,
    $_item       = null,
    $_category   = null,
    $_unit       = null,
    $_brand     = null,
    $_option     = null,
    $_supplier   = null,
    $_buyer     = null,
    $_purchase   = null,
    $_sale      = null,
    $_cashBook   = null,
    $_expense   = null;

  public function __construct()
  {
    parent::__construct($this->_type);
    $this->_store = new Store();
    if ($this->isLoggedIn() === true) {
      $tmpSalesMan = $this->_findByUID($this->_getPrivateData('userID'));
      if (is_null($tmpSalesMan) === false and is_object($tmpSalesMan) === true) {
        if (isset($tmpSalesMan->status) === true and $tmpSalesMan->status === true and isset($tmpSalesMan->data) === true and is_null($tmpSalesMan->data) === false and isset($tmpSalesMan->data->store) === true) {
          $this->_store     = new Store($tmpSalesMan->data->store);
          $this->_storeItem = new StoreItem();
          $this->_item       = new Item();
          $this->_category   = new Category();
          $this->_unit       = new Unit();
          $this->_brand     = new Brand();
          $this->_option     = new Option();
          $this->_supplier   = new Supplier();
          $this->_buyer     = new Buyer();
          $this->_purchase   = new Purchase();
          $this->_sale       = new Sale();
          $this->_cashBook   = new CashBook();
          $this->_expense   = new Expense();
        } else {
          $this->logout();
          Redirect::to('home/');
        }
      } else {
        $this->logout();
        Redirect::to('home/');
      }
    }
  }

  private function _findByUID($userID = null, $type = null)
  {
    $dem = 'Sales man not found.';
    $tmpResponse = (object)[
      'status'    => false,
      'data'      => null,
      'message'    => (object)[
        'success'      => null,
        'error'        => null
      ]
    ];
    if (is_null($userID) === false) {
      switch (strtoupper($type)) {
        case 'ACTIVE':
          $type = 1;
          break;
        default:
          $type = 1;
          break;
      }
      $findOwner = $this->_db->get('`sc_id` as `id`, `store_id`', 'sale_counters', '`user_id` = ? AND `sc_status` = ?', array($userID, $type));
      if ($findOwner->errorStatus() === false and $findOwner->dataCount() == 1) {
        $tmpSalesMan = $findOwner->getFirstResult();
        $tmpResponse->status = true;
        $tmpResponse->data = (object)[
          'id'        => $tmpSalesMan->id,
          'store'     => $tmpSalesMan->store_id,
        ];
        return $tmpResponse;
      } else {
        $tmpResponse->message->error = $dem;
        return $tmpResponse;
      }
    } else {
      $tmpResponse->message->error = $dem;
      return null;
    }
  }

  public function login($email = null, $password = null)
  {
    return parent::login($email, $password);
  }

  public function updateProfile($data = null)
  {
    $dem = 'Failed to update profile.';
    if ($this->__parseUpdateProfileData($data) === true) {
      // update person details
      $updatePerson = $this->_updateP((object)[
        'firstName'        => $data->firstName,
        'surname'          => $data->surname,
        'cnic'            => $data->cnic
      ]);
      if ($updatePerson !== false) {
        // person updated
        if ($this->_isEmailUnique($data->email) === false) {
          // email is not unique
          if ($data->email != $this->getData('email')) {
            $this->setError($dem . ' This email is already in use.');
            return false;
          }
        }
        $updateUser = null;
        // check either it is create or update
        if (isset($updatePerson->type) == 1 and isset($updatePerson->id) === true and is_null($updatePerson->id) === false) {
          // created
          $updateUser = $this->_db->update('users', '`person_id` = ?, `email` = ?, `contact` = ?', '`user_id` = ?', array($updatePerson->id, $data->email, $data->contact, $this->_getPrivateData('userID')));
        } else {
          // updated
          $updateUser = $this->_db->update('users', '`email` = ?, `contact` = ?', '`user_id` = ?', array($data->email, $data->contact, $this->_getPrivateData('userID')));
        }
        if (is_null($updateUser) === false and $updateUser->errorStatus() === false) {
          return true;
        } else {
          $this->setError($dem . 'Failed to update user details.');
          return false;
        }
      } else {
        // failed to update person details, error is set in Person class
        return false;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  public function changePassword($data = null)
  {
    $dem = 'Failed to change password.';
    if ($this->__parseChangePasswordData($data) === true) {
      // verify old password
      if (Hash::make($data->oldPassword . $this->_getPrivateData('token')) == $this->_getPrivateData('password')) {
        // verify new password
        if ($data->newPassword == $data->newPasswordConfirm) {
          // new password verified, change password
          $changePassword = $this->_db->update('users', '`password` = ?', '`user_id` = ?', array(Hash::make($data->newPassword . $this->_getPrivateData('token')), $this->_getPrivateData('userID')));
          if ($changePassword->errorStatus() === false) {
            return true;
          } else {
            $this->setError($dem);
            return false;
          }
        } else {
          // faield to verify new password
          $this->setError($dem . ' New password does not match.');
          return false;
        }
      } else {
        $this->setError($dem . ' You have entered incorrect password.');
        return false;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  // store
  public function getStoreData($key = null)
  {
    if (is_null($key) === false) {
      return $this->_store->getData($key);
    } else {
      return null;
    }
  }

  public function addStoreItem($basicDetails = null, $options = null)
  {
    $dem = 'Faield to add an item to inventory.';
    $response = (object)[
      'status'  => null,
      'data'    => null
    ];
    $response->status = false;
    if ($this->__parseItemBasicDetails($basicDetails) === true and is_null($options) === false and is_array($options) === true and count($options) > 0) {
      // validate options
      $validatedOptions = array();
      foreach ($options as $opt) {
        if (empty($opt) === true or is_null($opt) === true) {
          continue;
        }
        if (strlen($opt) > 100) {
          $opt = substr(strtoupper($opt), 0, 100);
        }
        array_push($validatedOptions, $opt);
      }
      if (count($validatedOptions) <= 0) {
        $this->setError($dem);
        return $response;
      }
      $storeItem = $this->_storeItem->findbyNameICB($basicDetails->itemName, $basicDetails->itemCategory, $basicDetails->itemBrand, $this->getStoreData('id'));
      if (isset($storeItem->status) === true and $storeItem->status === true) {
        // no error
        if (is_null($storeItem->data) === false and is_array($storeItem->data) === true and count($storeItem->data) == 1) {
          // store item found OR item already exists in inventory
          $this->setError('This item already exists in inventory.');
          return $response;
        } else {
          // new store item, add it
          // check item
          $item = $this->_item->findByName($basicDetails->itemName);
          if (isset($item->status) === false or $item->status === false) {
            // error occurred in finding item
            $this->setError($dem);
            return $response;
          }
          if (isset($item->data) === false or is_null($item->data) === true or is_array($item->data) === false or count($item->data) != 1) {
            // item not found, add 
            if ($this->_addItem($basicDetails->itemName) === false) {
              $this->setError($dem);
              return $response;
            }
            $item = $this->_item->findByName($basicDetails->itemName);
            if (isset($item->status) === false or $item->status === false or isset($item->data) === false or is_null($item->data) === true or is_array($item->data) === false or count($item->data) != 1) {
              $this->setError($dem);
              return $response;
            }
          }
          $item->data = $item->data[0];
          // done with item, check category
          $category = $this->_category->findByName($basicDetails->itemCategory);
          if (isset($category->status) === false or $category->status === false) {
            // error occurred in finding category
            $this->setError($dem);
            return $response;
          }
          if (isset($category->data) === false or is_null($category->data) === true or is_array($category->data) === false and count($category->data) != 1) {
            // category not found, add it
            if ($this->_addCategory($basicDetails->itemCategory) === false) {
              $this->setError($dem);
              return $response;
            }
            $category = $this->_category->findByName($basicDetails->itemCategory);
            if (isset($category->status) === false or $category->status === false or isset($category->data) === false or is_null($category->data) === true or is_array($category->data) === false and count($category->data) != 1) {
              $this->setError($dem);
              return $response;
            }
          }
          $category->data = $category->data[0];
          // done with category, check brand
          $brand = $this->_brand->findByName($basicDetails->itemBrand);
          if (isset($brand->status) === false or $brand->status === false) {
            // error occurred in finding brand
            $this->setError($dem);
            return $response;
          }
          if (isset($brand->data) === false or is_null($brand->data) === true or is_array($brand->data) === false and count($brand->data) != 1) {
            // brand not found, add it
            if ($this->_addBrand($basicDetails->itemBrand) === false) {
              $this->setError($dem);
              return $response;
            }
            $brand = $this->_brand->findByName($basicDetails->itemBrand);
            if (isset($brand->status) === false or $brand->status === false or isset($brand->data) === false or is_null($brand->data) === true or is_array($brand->data) === false and count($brand->data) != 1) {
              $this->setError($dem);
              return $response;
            }
          }
          $brand->data = $brand->data[0];
          // done with brand, check store item
          $storeItem = $this->_storeItem->findByIDICB($item->data->id, $category->data->id, $brand->data->id, $this->getStoreData('id'));
          if (isset($storeItem->status) === false or $storeItem->status === false) {
            // error occurred in finding store item
            $this->setError($dem);
            return $response;
          }
          if (isset($storeItem->data) === false or is_null($storeItem->data) === true or is_array($storeItem->data) === false and count($storeItem->data) != 1) {
            // item not found, add it
            $tmpStoreItemID = Hash::unique();
            $addStoreItem = $this->_db->insert('store_items', array(
              'si_id'        => $tmpStoreItemID,
              'item_id'      => $item->data->id,
              'category_id'  => $category->data->id,
              'brand_id'    => $brand->data->id,
              'store_id'    => $this->getStoreData('id'),
              'branch_id'    => null,
              'si_status'    => 1
            ));
            if ($addStoreItem->errorStatus() === false) {
              // add item options
              foreach ($validatedOptions as $opt) {
                $findOpt = $this->_option->findByName($opt);
                if (isset($findOpt->status) === true and $findOpt->status === true) {
                  if (isset($findOpt) === true and is_array($findOpt->data) and count($findOpt->data) == 1) {
                    // option found
                    $opt = $findOpt->data[0];
                  } else {
                    // option not found, add it
                    $tmpIOID = Hash::unique();
                    $addOpt = $this->_db->insert('items_options', array(
                      'io_id'        => $tmpIOID,
                      'name'        => strtoupper($opt)
                    ));
                    if ($addOpt->errorStatus() === false) {
                      // option addedd
                      $opt = (object)[
                        'id'    => $tmpIOID,
                        'name'  => strtoupper($opt)
                      ];
                    } else {
                      $this->setError('Item added successfully. However, failed to define product options. Please go to inventory and add them manually. Thanks!');
                      return $response;
                    }
                  }
                  // add item option
                  $addIO = $this->_db->insert('store_items_options', array(
                    'sio_id'      => Hash::unique(),
                    'si_id'        => $tmpStoreItemID,
                    'io_id'        => $opt->id,
                    'sio_status'  => 1
                  ));
                  if ($addIO->errorStatus() === true) {
                    $this->setError('Item added successfully. However, failed to define product options. Please go to inventory and add them manually. Thanks!');
                    return $response;
                  }
                } else {
                  $this->setError('Item added successfully. However, failed to define product options. Please go to inventory and add them manually. Thanks!');
                  return $response;
                }
              }
              Redirect::to('./dashboard/item/add-variants?item=' . $tmpStoreItemID);
            } else {
              $this->setError($dem);
              return $response;
            }
          } else {
            // item found
            $this->setError('This item is already exists in your inventory.');
            return $response;
          }
        }
      } else {
        // error occurred
        $this->setError($dem);
        return $response;
      }
    } else {
      $this->setError($dem);
      return $response;
    }
  }
  public function addVariants($storeItemID = null, $variants = null)
  {
    $dem = 'Failed to add variants.';
    if (is_null($storeItemID) === false and is_array($variants) === true and count($variants) > 0) {
      $itemOptions = $this->getItemOptions($storeItemID);
      if (is_null($itemOptions) === false and is_array($itemOptions) === true and count($itemOptions) > 0) {
        $validatedVariants = array();
        for ($i = 0; $i < count($variants['ioPrice']); $i++) {
          // loop all variants one by one
          $tmpVariant = (object)[
            'options'    => array(),
            'price'      => null,
            'discount'  => null
          ];
          foreach ($itemOptions as $opt) {
            // loop all options inside one variant
            $optInd = 'iov' . $opt->id;
            $optUnitInd = 'iovu' . $opt->id;
            $unitID = null;
            if (is_null($variants[$optUnitInd][$i]) === false and empty($variants[$optUnitInd][$i]) === false) {
              // unit provided
              $findOptUnit = $this->_unit->findByName($variants[$optUnitInd][$i]);
              if ($findOptUnit !== false and is_null($findOptUnit) === false) {
                // unit found
                $unitID = (isset($findOptUnit->id) === true) ? $findOptUnit->id : null;
              } else {
                if (is_null($findOptUnit) === true) {
                  // unit not found
                  if ($this->_unit->add($variants[$optUnitInd][$i]) === true) {
                    $findOptUnit = $this->_unit->findByName($variants[$optUnitInd][$i]);
                    if ($findOptUnit !== false and is_null($findOptUnit) === false) {
                      $unitID = (isset($findOptUnit->id) === true) ? $findOptUnit->id : null;
                    } else {
                      $this->setError($dem);
                      return false;
                    }
                  } else {
                    $this->setError($dem);
                    return false;
                  }
                } else {
                  // error in processing
                  $this->setError($dem);
                  return false;
                }
              }
            }
            if (isset($variants[$optInd][$i]) === true) {
              array_push($tmpVariant->options, (object)[
                'id'      => $opt->id,
                'option'  => $opt->option->name,
                'value'    => $variants[$optInd][$i],
                'unit'    => $unitID
              ]);
            } else {
              $this->setError($dem);
              return false;
            }
          }
          if (isset($variants['ioPrice'][$i]) === true and isset($variants['ioPrice'][$i]) === true) {
            $tmpVariant->price = $variants['ioPrice'][$i];
            $tmpVariant->discount = $variants['ioDiscount'][$i];
          } else {
            $this->setError($dem);
            return false;
          }
          array_Push($validatedVariants, $tmpVariant);
        }
        // variants validated, add them
        foreach ($validatedVariants as $var) {
          if ($this->_variantExists($storeItemID, $var->options) === false) {
            $tmpVarID = Hash::unique();
            $addVariant = $this->_db->insert('store_items_variants', array(
              'siv_id'      => $tmpVarID,
              'si_id'        => $storeItemID,
              'siv_status'  => 1
            ));
            if ($addVariant->errorStatus() === false) {
              // add variant options
              foreach ($var->options as $opt) {
                $addVarOpt = $this->_db->insert('store_items_variants_options', array(
                  'sivo_id'      => Hash::unique(),
                  'siv_id'      => $tmpVarID,
                  'sio_id'      => $opt->id,
                  'sio_value'    => strtoupper($opt->value),
                  'unit_id'      => $opt->unit,
                  'sivo_status'  => 1
                ));
                if ($addVarOpt->errorStatus() === false) {
                  continue;
                } else {
                  $this->setError($dem . ' Failed to add variant optioins. Kindly visit inventory to manage it manually.');
                  return false;
                }
              }
              // variant added, add pricing
              if ($this->_addVariantPrice($tmpVarID, $var->price) === false) {
                return false;
              }
              if ($this->_addVariantDiscount($tmpVarID, $var->discount) === false) {
                return false;
              }
            } else {
              $this->setError($dem . ' Kindly visit inventory to manage it manually.');
              return false;
            }
          } else {
            continue;
          }
        }
        return true;
      } else {
        $this->setError($dem);
        return false;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }
  private function _addVariantPrice($sivID = null, $price = null)
  {
    $dem = 'Failed to update price.';
    if (is_null($sivID) === false and is_null($price) === false) {
      $tmpPriceID = Hash::unique();
      $addPrice = $this->_db->insert('store_items_variants_prices', array(
        'sivp_id'      => $tmpPriceID,
        'siv_id'      => $sivID,
        'amount'      => $price,
        'sivp_status'  => 1
      ));
      if ($addPrice->errorStatus() === false) {
        $suspendPreviousPrices = $this->_db->update('store_items_variants_prices', '`sivp_status` = ?', '`siv_id` = ? AND `sivp_id` != ?', array(-1, $sivID, $tmpPriceID));
        if ($suspendPreviousPrices->errorStatus() === false) {
          return true;
        } else {
          $this->setError('New price added but failed to suspend previus price. Kindy try again or contact us to troubleshoot.');
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
  private function _addVariantDiscount($sivID = null, $discount = null)
  {
    $dem = 'Failed to update price.';
    if (is_null($sivID) === false and is_null($discount) === false) {
      $tmpDiscountID = Hash::unique();
      $addDiscount = $this->_db->insert('store_items_variants_discounts', array(
        'sivd_id'      => $tmpDiscountID,
        'siv_id'      => $sivID,
        'amount'      => $discount,
        'sivd_status'  => 1
      ));
      if ($addDiscount->errorStatus() === false) {
        $suspendPreviousDiscounts = $this->_db->update('store_items_variants_discounts', '`sivd_status` = ?', '`siv_id` = ? AND `sivd_id` != ?', array(-1, $sivID, $tmpDiscountID));
        if ($suspendPreviousDiscounts->errorStatus() === false) {
          return true;
        } else {
          $this->setError('New discount added but failed to suspend previus discount. Kindy try again or contact us to troubleshoot.');
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
  private function _variantExists($storeItemID = null, $options = null)
  {
    if (is_null($storeItemID) === false and is_null($options) === false) {
      $siVariants = $this->_storeItem->getVariants($storeItemID);
      if (is_null($siVariants) === true) {
        return false;
      }
      $variantFound = false;
      $vOptFound = false;
      $foundOpts = 0;
      // looping all variants one by one
      foreach ($siVariants as $var) {
        $variantFound = false;
        $vOptFound = false;
        $foundOpts = 0;
        // looping variant options one by one
        if (is_array($var->options) === true and count($var->options) > 0) {
          foreach ($var->options as $vOpt) {
            // search this variant option inside new options
            foreach ($options as $opt) {
              if (strtoupper($vOpt->option) == strtoupper($opt->option) and strtoupper($vOpt->value) == strtoupper($opt->value)) {
                // option found
                $foundOpts++;
                $vOptFound = true;
                break;
              }
            }
            if ($vOptFound === true) {
              $variantFound = true;
              continue;
            } else {
              $variantFound = false;
              break;
            }
          }
          if ($variantFound === true and $foundOpts == count($var->options)) {
            return true;
          } else {
            continue;
          }
        } else {
          return false;
        }
      }
      if ($variantFound === true and $foundOpts == count($var->options)) {
        return true;
      } else {
        return false;
      }
    } else {
      return null;
    }
  }

  public function editStoreItem($storeItemID = null, $basicDetails = null)
  {
    $dem = 'Failed to edit item details.';
    if ($this->isLoggedIn() === true and is_null($storeItemID) === false and $this->__parseItemBasicDetails($basicDetails) === true) {
      // done with validating inputs, check item
      $item = $this->_item->findByName($basicDetails->itemName);
      if (isset($item->status) === false or $item->status === false) {
        // error occurred in finding item
        $this->setError($dem);
        return false;
      }
      if (isset($item->data) === false or is_null($item->data) === true or is_array($item->data) === false or count($item->data) != 1) {
        // item not found, add 
        if ($this->_addItem($basicDetails->itemName) === false) {
          $this->setError($dem);
          return false;
        }
        $item = $this->_item->findByName($basicDetails->itemName);
        if (isset($item->status) === false or $item->status === false or isset($item->data) === false or is_null($item->data) === true or is_array($item->data) === false or count($item->data) != 1) {
          $this->setError($dem);
          return false;
        }
      }
      $item->data = $item->data[0];
      // done with item, check category
      $category = $this->_category->findByName($basicDetails->itemCategory);
      if (isset($category->status) === false or $category->status === false) {
        // error occurred in finding category
        $this->setError($dem);
        return false;
      }
      if (isset($category->data) === false or is_null($category->data) === true or is_array($category->data) === false and count($category->data) != 1) {
        // category not found, add it
        if ($this->_addCategory($basicDetails->itemCategory) === false) {
          $this->setError($dem);
          return false;
        }
        $category = $this->_category->findByName($basicDetails->itemCategory);
        if (isset($category->status) === false or $category->status === false or isset($category->data) === false or is_null($category->data) === true or is_array($category->data) === false and count($category->data) != 1) {
          $this->setError($dem);
          return false;
        }
      }
      $category->data = $category->data[0];
      // done with category, check brand
      $brand = $this->_brand->findByName($basicDetails->itemBrand);
      if (isset($brand->status) === false or $brand->status === false) {
        // error occurred in finding brand
        $this->setError($dem);
        return false;
      }
      if (isset($brand->data) === false or is_null($brand->data) === true or is_array($brand->data) === false and count($brand->data) != 1) {
        // brand not found, add it
        if ($this->_addBrand($basicDetails->itemBrand) === false) {
          $this->setError($dem);
          return false;
        }
        $brand = $this->_brand->findByName($basicDetails->itemBrand);
        if (isset($brand->status) === false or $brand->status === false or isset($brand->data) === false or is_null($brand->data) === true or is_array($brand->data) === false and count($brand->data) != 1) {
          $this->setError($dem);
          return false;
        }
      }
      $brand->data = $brand->data[0];

      // done with brand, check store item
      $storeItem = $this->_storeItem->findByIDICB($item->data->id, $category->data->id, $brand->data->id, $this->getStoreData('id'));
      if (isset($storeItem->status) === false or $storeItem->status === false) {
        // error occurred in finding store item
        $this->setError($dem);
        return false;
      }
      if (isset($storeItem->data) === false or is_null($storeItem->data) === true or is_array($storeItem->data) === false and count($storeItem->data) != 1 and isset($storeItem->data[0]->id) === true) {
        // item not found, update it
        $updateBasicDetails = $this->_db->update('store_items', '`item_id` = ?, `category_id` = ?, `brand_id` = ?', '`si_id` = ?', array($item->data->id, $category->data->id, $brand->data->id, $storeItemID));
        if ($updateBasicDetails->errorStatus() === false) {
          return true;
        } else {
          $this->setError($dem);
          return false;
        }
      } else {
        // item found
        if (isset($storeItem->data[0]->id) === true and $storeItem->data[0]->id == $storeItemID) {
          return true;
        } else {
          // item not found, update it
          $updateBasicDetails = $this->_db->update('store_items', '`item_id` = ?, `category_id` = ?, `brand_id` = ?', '`si_id` = ?', array($item->data->id, $category->data->id, $brand->data->id, $storeItemID));
          if ($updateBasicDetails->errorStatus() === false) {
            return true;
          } else {
            $this->setError($dem);
            return false;
          }
        }
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  public function removeStoreItem($storeItemID = null)
  {
    $dem = 'Failed to remove item.';
    if ($this->isLoggedIn() === true and is_null($storeItemID) === false) {
      $removeStoreItem = $this->_db->update('store_items', '`si_status` = ?', '`si_id` = ?', array(-1, $storeItemID));
      if ($removeStoreItem->errorStatus() === false and $removeStoreItem->dataCount() == 1) {
        return true;
      } else {
        $this->setError($dem);
        return false;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  public function removeStoreItemVariant($sivID = null)
  {
    $dem = 'Failed to remove item variant.';
    if ($this->isLoggedIn() === true and is_null($sivID) === false) {
      $removeSIV = $this->_db->update('store_items_variants', '`siv_status` = ?', '`siv_id` = ?', array(-1, $sivID));
      if ($removeSIV->errorStatus() === false and $removeSIV->dataCount() == 1) {
        return true;
      } else {
        $this->setError($dem);
        return false;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  // stock of all variants of one item
  public function addStock($storeItemID = null, $stock = null)
  {
    $dem = 'failed to add stock.';
    if (is_null($storeItemID) === false and is_null($stock) === false) {
      $storeItemVariants = $this->getItemVariants($storeItemID);
      if (is_null($storeItemVariants) === false and is_array($storeItemVariants) === true and count($storeItemVariants) > 0) {
        $validatedStock = array();
        foreach ($storeItemVariants as $siv) {
          $stockIndex = 'sivStock' . $siv->id;
          if (isset($stock[$stockIndex]) === true) {
            array_push($validatedStock, (object)[
              'sivID'        => $siv->id,
              'stock'        => (int)$stock[$stockIndex]
            ]);
          }
        }
        if (count($validatedStock) > 0) {
          // stock validated, add it
          foreach ($validatedStock as $vs) {
            $addStock = $this->_db->insert('store_items_variants_stocks', array(
              'sivs_id'      => Hash::unique(),
              'siv_id'      => $vs->sivID,
              'quantity'    => $vs->stock,
              'sivs_status'  => 1
            ));
            if ($addStock->errorStatus() === false) {
              continue;
            } else {
              $this->setError('Failed to add all stock. Kindly visit inventory to manage it or contact us.');
              return false;
            }
          }
          return true;
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

  public function getItemVariants($storeItemID = null)
  {
    $dem = 'Variants not found.';
    if (is_null($storeItemID) === false) {
      $itemVariants = $this->_storeItem->getVariants($storeItemID);
      if (is_null($itemVariants) === false) {
        return $itemVariants;
      } else {
        $this->setError($this->_storeItem->getError());
        return null;
      }
    } else {
      $this->setError($dem);
      return null;
    }
  }

  public function getItemOptions($storeItemID = null)
  {
    $dem = 'Item options not found.';
    if (is_null($storeItemID) === false) {
      $itemOptions = $this->_storeItem->getOptions($storeItemID);
      if (is_null($itemOptions) === false) {
        return $itemOptions;
      } else {
        $this->setError($this->_storeItem->getError());
        return null;
      }
    } else {
      $this->setError($dem);
      return null;
    }
  }

  public function getSivPrice($storeItemID = null)
  {
    $dem = 'Item price not found.';
    if (is_null($storeItemID) === false) {
      $siv = $this->_storeItem->findVariant($storeItemID, (object)['price' => true]);
      if (is_object($siv) === true and isset($siv->price) === true and is_object($siv->price)) {
        return $siv->price;
      } else {
        $this->setError($this->_storeItem->getError());
        return null;
      }
    } else {
      $this->setError($dem);
      return null;
    }
  }

  private function _addItem($itemName = null)
  {
    if (is_null($itemName) === false) {
      $addItem = $this->_db->insert('items', array(
        'item_id'      => Hash::unique(),
        'name'        => $itemName
      ));
      if ($addItem->errorStatus() === false) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  private function _addCategory($categoryName = null)
  {
    if (is_null($categoryName) === false) {
      $addcategory = $this->_db->insert('categories', array(
        'category_id'  => Hash::unique(),
        'name'        => $categoryName
      ));
      if ($addcategory->errorStatus() === false) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  private function _addBrand($brandName = null)
  {
    if (is_null($brandName) === false) {
      $addcategory = $this->_db->insert('brands', array(
        'brand_id'  => Hash::unique(),
        'name'      => $brandName
      ));
      if ($addcategory->errorStatus() === false) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  // get store item by id
  public function getItem($storeItemID = null, $params = [])
  {
    $storeItem = $this->_storeItem->find($storeItemID, $params);
    if (is_null($storeItem) === false) {
      return $storeItem;
    } else {
      $this->setError($this->_storeItem->getError());
      return null;
    }
  }

  // get all store items
  public function getItems($params = [])
  {
    $dem = 'Failed to find items.';
    $storeItems = $this->_storeItem->getAll($this->getStoreData('id'), $params);
    if (is_null($storeItems) === false) {
      return $storeItems;
    } else {
      $this->setError($this->_storeItem->getError());
      return null;
    }
  }

  // sale counter
  public function addSaleCounter($data = null)
  {
    $dem = 'Failed to add sale counter.';
    if (is_object($data) === true) {
      if ($this->_isEmailUnique($data->scEmail) === true) {
        $tmpPersonID = Hash::unique();
        $createPerson = $this->_createPerson((object)[
          'firstName'        => $data->scfirstName,
          'surname'          => $data->sclastName,
          'cnic'            => null
        ]);
        if (is_object($createPerson) === true and isset($createPerson->status) === true and $createPerson->status === true) {
          $tmpUserID = Hash::unique();
          $createUser = $this->_db->insert('users', array(
            'user_id'          => $tmpUserID,
            'person_id'        => $createPerson->id,
            'email'            => $data->scEmail,
            'token'            => Hash::make($tmpUserID),
            'password'        => Hash::make($data->scPassword, Hash::make($tmpUserID)),
            'role_id'          => 'c81e728d9d4c2f636f067f89cc14862c',
            'account_status'  => 1
          ));
          if ($createUser->errorStatus() === false) {
            $createSaleCounter = $this->_db->insert('sale_counters', array(
              'sc_id'          => Hash::unique(),
              'title'          => $data->counterTitle,
              'user_id'        => $tmpUserID,
              'store_id'      => $this->getStoreData('id'),
              'branch_id'      => null,
              'added_on'      => date('Y-m-d H:i:s'),
              'sc_status'      => 1
            ));
            if ($createSaleCounter->errorStatus() === false) {
              return true;
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
        $this->setError($dem . ' This email is already in use.');
        return false;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  public function getSaleCounters()
  {
    $dem = 'Failed to find sale counters';
    $find = $this->_db->get('`title`, `user_id` as `user`, `added_on` as `date`', 'sale_counters', '`store_id` = ? AND `sc_status` = ?', array($this->getStoreData('id'), 1));
    if ($find->errorStatus() === false and $find->datacount() > 0) {
      $tmp = array();
      foreach ($find->getResults() as $sc) {
        $sc->user = $this->_find($sc->user);
        $sc->user->person = $this->_findP($sc->user->person);
        array_push($tmp, $sc);
      }
      return $tmp;
    } else {
      $this->setError($dem);
      return false;
    }
  }

  // purchase
  public function purchaseStart($pCart = null)
  {
    $dem = 'Failed to process pruchase';
    if ($this->__parsePurchaseCartItems($pCart) === true) {
      $validatedItems = array();
      foreach ($pCart as $item) {
        $item = json_decode($item);
        array_push($validatedItems, $item->purchaseItemID);
        // if(in_array($item->purchaseItemID, $validatedItems) === true){
        // 	$this->setError($dem.' You have entered an item more than once.');
        // 	return false;
        // } else {
        // 	array_push($validatedItems, $item->purchaseItemID);
        // }
      }
      $validatedItems = array();
      $netTotal = 0;
      foreach ($pCart as $item) {
        $item = json_decode($item);
        array_push($validatedItems, (object)[
          'purchaseItem'      => $item->purchaseItem,
          'purchaseItemID'    => $item->purchaseItemID,
          'purchaseQuantity'  => $item->purchaseQuantity,
          'purchasePrice'      => $item->purchasePrice,
          'subTotal'          => round(($item->purchaseQuantity * $item->purchasePrice), 2, PHP_ROUND_HALF_UP)
        ]);
        $netTotal += round(($item->purchaseQuantity * $item->purchasePrice), 2, PHP_ROUND_HALF_UP);
      }
      Session::put('WO_RASEED_CART', (object)[
        'status'  => '0',
        'items'    => $validatedItems,
        'bill'    => (object)[
          'netTotal'  => $netTotal
        ]
      ]);
      if (Session::exists('WO_RASEED_CART') === true) {
        return true;
      } else {
        $this->setError($dem);
        return false;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  public function purchaseConfirm($details = null)
  {
    $dem = 'Failed to complete purchase.';
    if ($this->__parsePurchaseDetails($details) === true) {
      $supplier = $this->_supplier->findByNC($details['supplierName'], $details['supplierContact'], $this->getStoreData('id'));
      $tmpSupplierID = null;
      if ($supplier === false) {
        // error occurred
        $this->setError($dem);
        return false;
      }
      if (is_null($supplier) === true) {
        // supplier not found, register it
        $tmpSupplierID = Hash::unique();
        $registerSupplier = $this->_db->insert('suppliers', array(
          'supplier_id'      => $tmpSupplierID,
          'name'            => $details['supplierName'],
          'contact'          => $details['supplierContact'],
          'store_id'        => $this->getStoreData('id'),
          'supplier_status'  => 1
        ));
        if ($registerSupplier->errorStatus() === false) {
          $supplier = $this->_supplier->findByNC($details['supplierName'], $details['supplierContact'], $this->getStoreData('id'));
        } else {
          $this->setError($dem);
          return false;
        }
      } else {
        $tmpSupplierID = (isset($supplier->id) === true) ? $supplier->id : null;
      }
      if (is_null($tmpSupplierID) === false) {
        // supplier validated
        $tmpCart = Session::get('WO_RASEED_CART');
        if (is_null($tmpCart) == false and $this->__parseTmpPurchaseCart($tmpCart) === true) {
          // cart found, verify purchase receipt no
          $isValidReceiptNo = $this->_purchase->isReceiptNoValid($this->getStoreData('id'), $details['purchaseRaseedNo']);
          if ($isValidReceiptNo === true) {
            // start creating purchase entries
            $tmpPurchaseID = Hash::unique();
            $purchaseDiscount = (float)Input::sanitizeData($details['purchaseDiscount']);
            $createPurchase = $this->_db->insert('purchases', array(
              'purchase_id'      => $tmpPurchaseID,
              'receipt_no'      => $details['purchaseRaseedNo'],
              'store_id'        => $this->getStoreData('id'),
              'supplier_id'      => $tmpSupplierID,
              'gross_total'      => round(($tmpCart->bill->netTotal), 2, PHP_ROUND_HALF_UP),
              'discount'        => round($purchaseDiscount, 2, PHP_ROUND_HALF_UP),
              'notes'            => (empty(Input::getPost('purchaseNote')) === false) ? Input::getPost('purchaseNote') : null,
              'purchased_on'    => date('Y-m-d'),
              'payment_status'  => 2,
              'purchase_status'  => 2
            ));
            if ($createPurchase->errorStatus() === false) {
              // add purchasing items to stock
              foreach ($tmpCart->items as $pItem) {
                $addStock = $this->_db->insert('store_items_variants_stocks', array(
                  'sivs_id'      => Hash::unique(),
                  'siv_id'      => $pItem->purchaseItemID,
                  'purchase_id'  => $tmpPurchaseID,
                  'quantity'    => $pItem->purchaseQuantity,
                  'remaining'    => $pItem->purchaseQuantity,
                  'price'        => $pItem->purchasePrice,
                  'sivs_status'  => 2
                ));
                if ($addStock->errorStatus() === true) {
                  $this->setError($dem);
                  return false;
                }
              }
              // mark stock completed
              $activateStock = $this->_db->update('store_items_variants_stocks', '`sivs_status` = ?', '`purchase_id` = ?', array(1, $tmpPurchaseID));
              if ($activateStock->errorStatus() === false) {
                $completePurchase = $this->_db->update('purchases', '`purchase_status` = ?', '`purchase_id` = ?', array(1, $tmpPurchaseID));
                if ($completePurchase->errorStatus() === false) {
                  // purchase completed, add transaction
                  $tmpTransactionID = Hash::unique();
                  $addTransaction = $this->_db->insert('cash_book', array(
                    'pt_id'      => $tmpTransactionID,
                    'store_id'  => $this->getStoreData('id'),
                    'amount'    => round(($tmpCart->bill->netTotal - $purchaseDiscount), 2, PHP_ROUND_HALF_UP),
                    'type'      => 1,
                    'major'      => 1,
                    'pt_status'  => 2
                  ));
                  Session::delete('WO_RASEED_CART');
                  if ($addTransaction->errorStatus() === false) {
                    // transaction added, add pruchase payment
                    $addPurchasePayment = $this->_db->insert('purchases_payments', array(
                      'pp_id'        => Hash::unique(),
                      'purchase_id'  => $tmpPurchaseID,
                      'pt_id'        => $tmpTransactionID,
                      'pp_status'    => 1
                    ));
                    if ($addPurchasePayment->errorStatus() === false) {
                      // purchase payment added, activate transaction
                      $activateTransaction = $this->_db->update('cash_book', '`pt_status` = ?', '`pt_id` = ?', array(1, $tmpTransactionID));
                      if ($activateTransaction->errorStatus() === false) {
                        Session::flash('successMessage', 'Purchasing completed successfully.');
                        Redirect::to('./dashboard/purchase/receipt/' . $tmpPurchaseID);
                        exit();
                      } else {
                        Session::flash('errorMessage', $dem . ' Stock added but failed to complete transaction.');
                        Redirect::to('./dashboard/purchase/view/' . $tmpPurchaseID);
                        exit();
                      }
                    } else {
                      Session::flash('errorMessage', $dem . ' Stock added but failed to complete transaction.');
                      Redirect::to('./dashboard/purchase/view/' . $tmpPurchaseID);
                      exit();
                    }
                  } else {
                    Session::flash('errorMessage', $dem . ' Stock added but failed to complete transaction.');
                    Redirect::to('./dashboard/purchase/view/' . $tmpPurchaseID);
                    exit();
                  }
                  return true;
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
            if (is_null($isValidReceiptNo) === true) {
              $this->setError('This receipt no is already assigned to other purchase receipt.');
              return false;
            } else {
              $this->setError($dem);
              return false;
            }
          }
        } else {
          $this->setError($dem);
          return false;
        }
      } else {
        // supplier not validated
        $this->setError($dem);
        return false;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  public function getPurchaseReceipt($purchaseID = null)
  {
    $dem = 'Failed to find receipt.';
    if (is_null($purchaseID) === false) {
      $findPurchase = $this->_db->get('`supplier_id`, `receipt_no`, `gross_total`, `discount`, `notes`, `purchased_on`', 'purchases', '`purchase_id` = ? AND `store_id` = ? AND `purchase_status` = ?', array($purchaseID, $this->getStoreData('id'), 1));
      if ($findPurchase->errorStatus() === false) {
        if ($findPurchase->dataCount() > 0) {
          $purchase = $findPurchase->getFirstResult();
          $receipt = (object)[
            'no'          => $purchase->receipt_no,
            'orderLine'    => null,
            'bill'        => (object)[
              'grossTotal'    => Input::roundMoney($purchase->gross_total),
              'discount'      => Input::roundMoney($purchase->discount),
              'netTotal'      => Input::roundMoney(($purchase->gross_total - $purchase->discount)),
              'due'            => null
            ],
            'payment'      => $this->_purchase->getPaymentDetails($purchaseID),
            'notes'        => $purchase->notes,
            'date'        => $purchase->purchased_on
          ];
          if (isset($receipt->payment->first) === true) {
            $receipt->bill->due = Input::roundMoney((($purchase->gross_total - $purchase->discount) - $receipt->payment->first));
          }
          // find purchase order line
          $orderLine = $this->_db->get('`siv_id`, `quantity`, `price`', 'store_items_variants_stocks', '`purchase_id` = ? AND `sivs_status` = ?', array($purchaseID, 1));
          if ($orderLine->errorStatus() === false and $orderLine->dataCount() > 0) {
            $receipt->orderLine = array();
            foreach ($orderLine->getResults() as $ol) {
              $storeItem = $this->_storeItem->findBySIVID($ol->siv_id);
              array_push($receipt->orderLine, (object)[
                'variant'    => $this->_storeItem->findVariant($ol->siv_id),
                'itemName'  => ($storeItem !== false and is_null($storeItem) === false and isset($storeItem->item->name) === true) ? $storeItem->item->name : null,
                'quantity'  => $ol->quantity,
                'price'      => $ol->price
              ]);
            }
            return $receipt;
          } else {
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

  public function getPurchases($params = null)
  {
    if ($this->isLoggedIn() === true) {
      return $this->_purchase->getAll($this->getStoreData('id'), $params);
    } else {
      return null;
    }
  }

  public function lastPurchaseReceiptNo()
  {
    if ($this->isLoggedIn() === true) {
      return $this->_purchase->lastReceiptNo($this->getStoreData('id'));
    } else {
      return false;
    }
  }

  // sale
  public function saleStart($pCart = null)
  {
    $dem = 'Failed to process sale';
    if ($this->__parseSaleCartItems($pCart) === true) {
      $validatedItems = array();
      foreach ($pCart as $item) {
        $item = json_decode($item);
        if (in_array($item->saleItemID, $validatedItems) === true) {
          $this->setError($dem . ' You have entered an item more than once.');
          return false;
        } else {
          array_push($validatedItems, $item->saleItemID);
        }
      }
      $validatedItems = array();
      $netTotal = 0;
      foreach ($pCart as $item) {
        $item = json_decode($item);
        // check its stock
        $stock = $this->_storeItem->getVariantStock($item->saleItemID);
        if ($item->saleQuantity > $stock) {
          $this->setError($dem . " Stock of {$item->saleItem} is not enough.");
          return false;
        }
        array_push($validatedItems, (object)[
          'saleItem'      => $item->saleItem,
          'saleItemID'    => $item->saleItemID,
          'saleQuantity'  => $item->saleQuantity,
          'salePrice'      => $item->salePrice,
          'subTotal'          => Input::roundMoney($item->saleQuantity * $item->salePrice)
        ]);
        $netTotal += Input::roundMoney(($item->saleQuantity * $item->salePrice));
      }
      Session::put('WO_RASEED_CART_SALE', (object)[
        'status'  => '0',
        'items'    => $validatedItems,
        'bill'    => (object)[
          'netTotal'  => $netTotal
        ]
      ]);
      if (Session::exists('WO_RASEED_CART_SALE') === true) {
        return true;
      } else {
        $this->setError($dem);
        return false;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  public function saleConfirm($details = null)
  {
    $dem = 'Failed to complete sale.';
    if ($this->__parseSaleDetails($details) === true) {
      $tmpBuyerID = null;
      if (is_null($details['buyerName']) === false and empty($details['buyerName']) === false) {
        $buyer = $this->_buyer->findByNC($details['buyerName'], $details['buyerContact'], $this->getStoreData('id'));
        if ($buyer === false) {
          // error occurred
          $this->setError($dem);
          return false;
        } elseif (is_null($buyer) === true) {
          // buyer not found, register it
          $tmpBuyerID = Hash::unique();
          $registerSupplier = $this->_db->insert('buyers', array(
            'buyer_id'        => $tmpBuyerID,
            'name'            => $details['buyerName'],
            'contact'          => $details['buyerContact'],
            'store_id'        => $this->getStoreData('id'),
            'buyer_status'    => 1
          ));
          if ($registerSupplier->errorStatus() === false) {
            $buyer = $this->_supplier->findByNC($details['buyerName'], $details['buyerContact'], $this->getStoreData('id'));
          } else {
            $this->setError($dem);
            return false;
          }
        } else {
          $tmpBuyerID = (isset($buyer->id) === true) ? $buyer->id : null;
        }
      }
      // buyer validated
      $tmpCart = Session::get('WO_RASEED_CART_SALE');
      if (is_null($tmpCart) == false and $this->__parseTmpPurchaseCart($tmpCart) === true) {
        // check sale recept number
        $isValidReceiptNo = $this->_sale->isReceiptNoValid($this->getStoreData('id'), $details['saleRaseedNo']);
        if ($isValidReceiptNo === true) {
          // start creating sale entries
          $tmpSaleID = Hash::unique();
          $saleDiscount = (float)Input::sanitizeData($details['saleDiscount']);
          $createSale = $this->_db->insert('sales', array(
            'sale_id'          => $tmpSaleID,
            'receipt_no'      => $details['saleRaseedNo'],
            'store_id'        => $this->getStoreData('id'),
            'buyer_id'        => $tmpBuyerID,
            'gross_total'      => Input::roundMoney($tmpCart->bill->netTotal),
            'discount'        => Input::roundMoney($saleDiscount),
            'sold_on'          => date('Y-m-d'),
            'payment_status'  => 2,
            'dues_status'      => 2,
            'sale_status'      => 2
          ));
          if ($createSale->errorStatus() === false) {
            // add sales order line
            foreach ($tmpCart->items as $sItem) {
              $tmpSOLID = Hash::unique();
              $solEntry = $this->_db->insert('sales_order_line', array(
                'sol_id'      => $tmpSOLID,
                'sale_id'      => $tmpSaleID,
                'siv_id'      => $sItem->saleItemID,
                'quantity'    => $sItem->saleQuantity,
                'price'        => $sItem->salePrice,
                'sol_status'  => 2
              ));
              if ($solEntry->errorStatus() === false) {
                // add stock entries
                $requiredStock = $sItem->saleQuantity;
                $variantStockEntries = $this->_storeItem->getVariantStockEntries($sItem->saleItemID);
                foreach ($variantStockEntries as $vse) {
                  $currentEntryQuantity = 0;
                  if ($vse->quantity >= $requiredStock) {
                    // single stock entity
                    $currentEntryQuantity = $requiredStock;
                  } else {
                    // Multiple stock entries
                    $currentEntryQuantity = $vse->quantity;
                  }
                  $solStockEntry = $this->_db->insert('sales_order_line_stock_entries', array(
                    'solse_id'      => Hash::unique(),
                    'sol_id'        => $tmpSOLID,
                    'sivs_id'        => $vse->id,
                    'quantity'      => $currentEntryQuantity,
                    'solse_status'  => 2
                  ));
                  if ($solStockEntry->errorStatus() === false) {
                    // entry success, update stock
                    $remainingStock = (int)($vse->quantity - $currentEntryQuantity);
                    $updateVSE = $this->_db->update('store_items_variants_stocks', '`remaining` = ?', '`sivs_id` = ?', array($remainingStock, $vse->id));
                    if ($updateVSE->errorStatus() === true) {
                      $this->setError($dem);
                      return $this->_provokeSale();
                    }
                  } else {
                    // entry failure, prompt sale
                    $this->setError($dem);
                    return $this->_provokeSale();
                  }
                  $requiredStock -= $currentEntryQuantity;
                  if ($requiredStock > 0) {
                    continue;
                  } else {
                    break;
                  }
                }
              } else {
                $this->setError($dem);
                return $this->_provokeSale();
              }
              // activate order line entry
              $activateOrderLineEntry = $this->_db->update('sales_order_line', '`sol_status` = ?', '`sol_id` = ?', array(1, $tmpSOLID));
              if ($activateOrderLineEntry->errorStatus() === false) {
                // activated order line entry, activate its stock entries
                $activateStockEntries = $this->_db->update('sales_order_line_stock_entries', '`solse_status` = ?', '`sol_id` = ?', array(1, $tmpSOLID));
                if ($activateStockEntries->errorStatus() === false) {
                  continue;
                } else {
                  $this->setError($dem);
                  return $this->_provokeSale();
                }
              } else {
                // not-activated order line entry
                $this->setError($dem);
                return $this->_provokeSale();
              }
            }
            // complete sale
            $completeSale = $this->_db->update('sales', '`sale_status` = ?', '`sale_id` = ?', array(1, $tmpSaleID));
            if ($completeSale->errorStatus() === false) {
              // slae completed, make payment transaction
              $tmpPTID = Hash::unique();
              $createTransaction = $this->_db->insert('cash_book', array(
                'pt_id'        => $tmpPTID,
                'store_id'    => $this->getStoreData('id'),
                'amount'      => Input::roundMoney($details['salePaid']),
                'type'        => 2,
                'major'        => 3,
                'pt_status'    => 1
              ));
              if ($createTransaction->errorStatus() === false) {
                $addSalePayment = $this->_db->insert('sales_payments', array(
                  'sp_id'      => Hash::unique(),
                  'sale_id'    => $tmpSaleID,
                  'pt_id'      => $tmpPTID,
                  'sp_status'  => 1
                ));
                if ($addSalePayment->errorStatus() === false) {
                  $duesStatus = 2;
                  if (Input::roundMoney($details['salePaid']) == Input::roundMoney(Input::roundMoney($tmpCart->bill->netTotal) - Input::roundMoney($saleDiscount))) {
                    $duesStatus = 1;
                  }
                  $updatePaymentStatus = $this->_db->update('sales', '`payment_status` = ?, `dues_status` = ?', '`sale_id` = ?', array(1, $duesStatus, $tmpSaleID));
                  if ($updatePaymentStatus->errorStatus() === false) {
                    if ((Input::roundMoney($tmpCart->bill->netTotal) - Input::roundMoney($saleDiscount)) == Input::roundMoney($details['salePaid'])) {
                      Session::flash('successMessage', 'Sale completed successfully.');
                      Redirect::to('./dashboard/sale/receipt/' . $tmpSaleID);
                      exit();
                    } else {
                      Session::flash('successMessage', 'Sale completed successfully. However, a few amount is due from net total.');
                      Redirect::to('./dashboard/sale/receipt/' . $tmpSaleID);
                      exit();
                    }
                  } else {
                    $this->setError($dem);
                    Session::flash('errorMessage', 'Failed to complete payment. Please try again to make payment for this order.');
                    Redirect::to('./dashboard/sale/receipt/' . $tmpSaleID);
                    exit();
                  }
                } else {
                  $this->setError($dem);
                  Session::flash('errorMessage', 'Failed to complete payment. Please try again to make payment for this order.');
                  Redirect::to('./dashboard/sale/receipt/' . $tmpSaleID);
                  exit();
                }
              } else {
                $this->setError($dem);
                Session::flash('errorMessage', 'Failed to complete payment. Please try again to make payment for this order.');
                Redirect::to('./dashboard/sale/receipt/' . $tmpSaleID);
                exit();
              }
            } else {
              $this->setError($dem);
              return $this->_provokeSale();
            }
          } else {
            $this->setError($dem);
            return false;
          }
        } else {
          if (is_null($isValidReceiptNo) === true) {
            $this->setError('This receipt no is already assigned to other sale receipt.');
            return false;
          } else {
            $this->setError($dem);
            return false;
          }
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

  public function getSaleReceipt($saleID = null)
  {
    $dem = 'Failed to find sale receipt.';
    if (is_null($saleID) === false) {
      $findPurchase = $this->_db->get('`buyer_id`, `receipt_no`, `gross_total`, `discount`, `sold_on`, `dues_status`', 'sales', '`sale_id` = ? AND `store_id` = ? AND `sale_status` = ?', array($saleID, $this->getStoreData('id'), 1));
      if ($findPurchase->errorStatus() === false) {
        if ($findPurchase->dataCount() > 0) {
          $sale = $findPurchase->getFirstResult();
          $receipt = (object)[
            'no'          => $sale->receipt_no,
            'orderLine'    => null,
            'bill'        => (object)[
              'grossTotal'          => Input::roundMoney($sale->gross_total),
              'discount'            => Input::roundMoney($sale->discount),
              'netTotal'            => Input::roundMoney(($sale->gross_total - $sale->discount)),
              'due'                  => null
            ],
            'payment'      => $this->_sale->getPaymentDetails($saleID),
            'dues'        => $sale->dues_status,
            'date'        => $sale->sold_on
          ];
          if (isset($receipt->payment->first) === true) {
            $receipt->bill->due = Input::roundMoney((($sale->gross_total - $sale->discount) - $receipt->payment->first));
          }
          // find sale order line
          $orderLine = $this->_db->get('`siv_id`, `quantity`, `price`', 'sales_order_line', '`sale_id` = ? AND `sol_status` = ?', array($saleID, 1));
          if ($orderLine->errorStatus() === false and $orderLine->dataCount() > 0) {
            $receipt->orderLine = array();
            foreach ($orderLine->getResults() as $ol) {
              $storeItem = $this->_storeItem->findBySIVID($ol->siv_id);
              array_push($receipt->orderLine, (object)[
                'variant'    => $this->_storeItem->findVariant($ol->siv_id),
                'itemName'  => ($storeItem !== false and is_null($storeItem) === false and isset($storeItem->item->name) === true) ? $storeItem->item->name : null,
                'quantity'  => $ol->quantity,
                'price'      => Input::roundMoney($ol->price)
              ]);
            }
          } else {
            return false;
          }
          return $receipt;
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

  public function saleReverse($saleID = null)
  {
    $dem = 'Failed to reverse sale.';
    if ($this->isLoggedIn() === true and is_null($saleID) === false) {
      // get orderline
      $saleOL = $this->_sale->getOrderLine($saleID, (object)[
        'stockEntries'    => true
      ]);
      // get payment
      $salePayment = $this->_sale->getPaymentDetails($saleID);
      if (is_object($salePayment) === false) {
        $this->setError($dem);
        return false;
      }
      $salePayment = (object)$salePayment;
      if (!(isset($salePayment->entries) === true and is_array($salePayment->entries) === true and count($salePayment->entries) > 0)) {
        $this->setError($dem);
        return false;
      }
      if (is_null($saleOL) === false) {
        // suspend sale orderline
        foreach ($saleOL as $ol) {
          if (isset($ol->stockEntries) === true and is_array($ol->stockEntries) === true and count($ol->stockEntries) > 0) {
            // stock entries found
            foreach ($ol->stockEntries as $se) {
              $findStock = $this->_db->get('`remaining`', 'store_items_variants_stocks', '`sivs_id` = ?', array($se->sivsID));
              if ($findStock->errorStatus() === false and $findStock->dataCount() > 0) {
                // stock found
                $stockDetails = $findStock->getFirstResult();
                $remainingStock = (isset($stockDetails->remaining) === true) ? $stockDetails->remaining : null;
                $returningStock = (isset($se->quantity) === true) ? $se->quantity : null;
                $newStock = (int)$remainingStock + (int)$returningStock;
                $updateStock = $this->_db->update('store_items_variants_stocks', '`remaining` = ?', '`sivs_id` = ?', array($newStock, $se->sivsID));
                if ($updateStock->errorStatus() === false) {
                  // stock entry updated, remove this stock entry
                  $suspendStockEntry = $this->_db->update('sales_order_line_stock_entries', '`solse_status` = ?', '`solse_id` = ?', array(-11, $se->id));
                  if ($suspendStockEntry->errorStatus() === true) {
                    $suspendStockEntry = $this->_db->update('sales_order_line_stock_entries', '`solse_status` = ?', '`solse_id` = ?', array(-11, $se->id));
                    if ($suspendStockEntry->errorStatus() === true) {
                      $this->setError($dem . ' System failure, please contact with admin.');
                      return false;
                    }
                  }
                } else {
                  // failed to update stock entry
                  $this->setError($dem . ' System failure, please contact with admin.');
                  return false;
                }
              } else {
                // stock not found
                $this->setError($dem . ' System failure, please contact with admin.');
                return false;
              }
            }
            // done with all stock entries, update ol entry
            $suspendOLEntry = $this->_db->update('sales_order_line', '`sol_status` = ?', '`sol_id` = ?', array(-11, $ol->id));
            if ($suspendOLEntry->errorStatus() === true) {
              $suspendOLEntry = $this->_db->update('sales_order_line', '`sol_status` = ?', '`sol_id` = ?', array(-11, $ol->id));
              if ($suspendOLEntry->errorStatus() === true) {
                $this->setError($dem . ' System failure, please contact with admin.');
                return false;
              }
            }
          } else {
            // stock entries not found
            $this->setError($dem . ' System failure, please contact with admin.');
            return false;
          }
        }
        // done with sol, suspend payment
        foreach ($salePayment->entries as $spEntry) {
          if (isset($spEntry->spID) === true and isset($spEntry->ptID) === true) {
            $suspendSalePayment = $this->_db->update('sales_payments', '`sp_status` = ?', '`sp_id` = ?', array(-11, $spEntry->spID));
            if ($suspendSalePayment->errorStatus() === true) {
              $suspendSalePayment = $this->_db->update('sales_payments', '`sp_status` = ?', '`sp_id` = ?', array(-11, $spEntry->spID));
              if ($suspendSalePayment->errorStatus() === true) {
                $this->setError($dem . ' Failed to reverse payment details. Please contact with admin.');
                return false;
              }
            }
            $suspendPaymentTransaction = $this->_db->update('cash_book', '`pt_status` = ?', '`pt_id` = ?', array(-11, $spEntry->ptID));
            if ($suspendPaymentTransaction->errorStatus() === true) {
              $suspendPaymentTransaction = $this->_db->update('cash_book', '`pt_status` = ?', '`pt_id` = ?', array(-11, $spEntry->ptID));
              if ($suspendPaymentTransaction->errorStatus() === true) {
                $this->setError($dem . ' Failed to reverse payment details. Please contact with admin.');
                return false;
              }
            }
          } else {
            $this->setError($dem . ' Failed to reverse payment details. Please contact with admin.');
            return false;
          }
        }
        // done with payment suspension, suspend sale
        $suspendSale = $this->_db->update('sales', '`sale_status` = ?', '`sale_id` = ?', array(-11, $saleID));
        if ($suspendSale->errorStatus() === true) {
          $suspendSale = $this->_db->update('sales', '`sale_status` = ?', '`sale_id` = ?', array(-11, $saleID));
          if ($suspendSale->errorStatus() === true) {
            $this->setError('Failed to finalize the action. System failure, please contact with admin.');
            return false;
          }
        }
        return true;
      } else {
        $this->setError($dem);
        return false;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  private function _provokeSale($saleID = null)
  {
    die('sale provoked');
    return false;
  }

  public function getSales($params = null)
  {
    if ($this->isLoggedIn() === true) {
      return $this->_sale->getAll($this->getStoreData('id'), $params);
    } else {
      return null;
    }
  }

  public function lastSaleReceiptNo()
  {
    if ($this->isLoggedIn() === true) {
      return $this->_sale->lastReceiptNo($this->getStoreData('id'));
    } else {
      return false;
    }
  }

  // Data Parsers
  private function __parsePurchaseCartItems($items = null)
  {
    if (is_null($items) === false and is_array($items) === true and count($items) > 0) {
      foreach ($items as $item) {
        $item = json_decode($item);
        if (isset($item->purchaseItem) === true and isset($item->purchaseItemID) === true and isset($item->purchaseQuantity) === true and isset($item->purchasePrice) === true) {
          continue;
        } else {
          return false;
        }
      }
      return true;
    } else {
      return false;
    }
  }
  private function __parsePurchaseDetails($details = null)
  {
    if (is_null($details) === false and is_array($details) === true) {
      if (isset($details['purchaseRaseedNo']) === true and isset($details['supplierName']) === true and isset($details['supplierContact']) === true and isset($details['purchaseDiscount']) === true and isset($details['purchasePaid']) === true) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
  private function __parseTmpPurchaseCart($cart = null)
  {
    if (is_null($cart) === false and is_object($cart) === true) {
      $cart = (object)$cart;
      if (isset($cart->status) === true and $cart->status == 0 and isset($cart->items) === true and is_array($cart->items) === true and count($cart->items) > 0 and isset($cart->bill->netTotal) === true) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
  private function __parseSaleCartItems($items = null)
  {
    if (is_null($items) === false and is_array($items) === true and count($items) > 0) {
      foreach ($items as $item) {
        $item = json_decode($item);
        if (isset($item->saleItem) === true and isset($item->saleItemID) === true and isset($item->saleQuantity) === true and isset($item->salePrice) === true) {
          continue;
        } else {
          return false;
        }
      }
      return true;
    } else {
      return false;
    }
  }
  private function __parseSaleDetails($details = null)
  {
    if (is_null($details) === false and is_array($details) === true) {
      if (isset($details['saleRaseedNo']) === true and isset($details['buyerName']) === true and isset($details['buyerContact']) === true and isset($details['saleDiscount']) === true and isset($details['salePaid']) === true) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  // cash book
  public function getPurchasesCashbook()
  {
    if ($this->isLoggedIn() === true) {
      return $this->_cashBook->getPurchases($this->getStoreData('id'));
    } else {
      return null;
    }
  }

  public function getSalesCashbook()
  {
    if ($this->isLoggedIn() === true) {
      return $this->_cashBook->getSales($this->getStoreData('id'));
    } else {
      return null;
    }
  }

  public function getCashbook()
  {
    if ($this->isLoggedIn() === true) {
      return $this->_cashBook->getAll($this->getStoreData('id'));
    } else {
      return null;
    }
  }

  // dues
  public function getSalesDues()
  {
    if ($this->isLoggedIn() === true) {
      $dues = $this->_sale->getDues($this->getStoreData('id'));
      $this->setError($this->_sale->getError());
      return $dues;
    } else {
      return false;
    }
  }

  public function getBuyersDues()
  {
    if ($this->isLoggedIn() === true) {
      $dues = $this->_sale->getBuyerDues($this->getStoreData('id'));
      $this->setError($this->_sale->getError());
      return $dues;
    } else {
      return false;
    }
  }

  public function getBuyerDuesHistory($buyerID = null)
  {
    $dem = 'Failed to find buyer dues history.';
    if ($this->isLoggedIn() === true and is_null($buyerID) === false) {
      $duesHistory = $this->_sale->getBuyerDuesHistory($buyerID);
      if (is_null($duesHistory) === false) {
        $tempHistory = array();
        foreach ($duesHistory as $dh) {
          array_push($tempHistory, (object)[
            'date'          => $dh->date,
            'description'    => $this->_sale->getOrderLine($dh->id),
            'amount'        => $dh->bill->netTotal,
            'type'          => 1
          ]);
          foreach ($dh->payment->entries as $pe) {
            array_push($tempHistory, (object)[
              'date'          => $pe->date,
              'description'    => null,
              'amount'        => (float)$pe->amount,
              'type'          => 2
            ]);
          }
        }
        array_multisort(array_column($tempHistory, 'date'), SORT_ASC, array_column($tempHistory, 'type'), SORT_ASC, $tempHistory);
        return $tempHistory;
      } else {
        $this->setError($this->_sale->getError());
        return null;
      }
    } else {
      $this->setError($dem);
      return null;
    }
  }

  public function receiveDues($saleID = null, $amount = null)
  {
    $dem = 'Failed to receive dues.';
    if ($this->isLoggedIn() === true and is_null($saleID) === false and is_null($amount) === false and $amount > 0) {
      $sale = $this->_sale->find($saleID, (object)['payment' => true]);
      if (is_null($sale) === false and isset($sale->bill->netTotal) === true and isset($sale->payment->total) === true) {
        $remainingDues = Input::roundMoney(($sale->bill->netTotal - $sale->payment->total));
        if ($remainingDues > 0) {
          if ($amount <= $remainingDues) {
            if ($this->_makeSalePayment($saleID, $amount, 4) === true) {
              if ($amount == $remainingDues) {
                // mark payment of this sale received
                $completePayment = $this->_db->update('sales', '`dues_status` = ?', '`sale_id` = ?', array(3, $saleID));
                if ($completePayment->errorStatus() === true) {
                  $completePayment = $this->_db->update('sales', '`dues_status` = ?', '`sale_id` = ?', array(3, $saleID));
                }
                return true;
              } else {
                return true;
              }
            } else {
              return false;
            }
          } else {
            $this->setError($dem . ' The receiving amount is more than the remaining dues.');
            return false;
          }
        } else {
          $this->setError($dem . ' There are no remaining dues for this sale.');
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

  public function receiveBuyerDues($buyerID = null, $amount = null)
  {
    $dem = 'Failed to receive dues.';
    if ($this->isLoggedIn() === true and is_null($buyerID) === false and is_null($amount) === false and $amount > 0) {
      $dueSales = $this->_sale->getBuyerDueSales($this->getStoreData('id'), $buyerID);
      if (is_array($dueSales) === true and count($dueSales) > 0) {
        $totalReceivingAmount = $amount;
        foreach ($dueSales as $dueSale) {
          $remainingDues = Input::roundMoney(($dueSale->bill->netTotal - $dueSale->payment->total));
          $currentReceivingAmount = ($remainingDues >= $totalReceivingAmount) ? $totalReceivingAmount : $remainingDues;
          if ($this->receiveDues($dueSale->id, $currentReceivingAmount) === true) {
            $totalReceivingAmount -= $currentReceivingAmount;
            if ($totalReceivingAmount <= 0) {
              return true;
            }
          } else {
            $this->setError($dem);
            return false;
          }
        }
        return true;
      } else {
        $this->setError($dem);
        return false;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  private function _makeSalePayment($saleID = null, $amount = null, $major = null)
  {
    $dem = 'Failed to complete payment.';
    if ($this->isLoggedIn() === true and (is_null($saleID) === false and is_null($amount) === false and $amount > 0 and is_null($major) === false and in_array($major, array(3, 4)) === true)) {
      $cbID = Hash::unique(); // cash-book id
      $createCashBookEntry = $this->_db->insert('cash_book', array(
        'pt_id'        => $cbID,
        'store_id'    => $this->getStoreData('id'),
        'amount'      => Input::roundMoney($amount),
        'type'        => 2,
        'major'        => $major,
        'pt_status'    => 1
      ));
      if ($createCashBookEntry->errorStatus() === false) {
        $addSalePayment = $this->_db->insert('sales_payments', array(
          'sp_id'        => Hash::unique(),
          'sale_id'      => $saleID,
          'pt_id'        => $cbID,
          'sp_status'    => 1
        ));
        if ($addSalePayment->errorStatus() === false) {
          return true;
        } else {
          $removeCashBookEntry = $this->_db->update('cash_book', '`pt_status` = ?', '`pt_id` = ?', array(-1, $cbID));
          if ($removeCashBookEntry->errorStatus() === true) {
            $removeCashBookEntry = $this->_db->update('cash_book', '`pt_status` = ?', '`pt_id` = ?', array(-1, $cbID));
          }
          $this->setError($dem);
          return false;
        }
      } else {
        $this->setError($dem);
        return false;
      }
    } else {
      return false;
    }
  }


  // fronts
  public function registerBranch($branch = null, $address = null, $storeID = null)
  {
    $dem = 'Failed to register new store branch.';
    if ($this->isLoggedIn() === true) {
      $schoolID = $this->getStoreData('id');
    }
    if (is_null($storeID) === false and $this->__parseFrontData($branch) === true) {
      $check = $this->_db->get('`branch_id`', 'branches', '`store_id` = ? AND `name` = ? AND `branch_status` = ?', array($storeID, $branch->name, 1));
      if ($check->errorStatus() === false) {
        // no error
        if ($check->dataCount() == 0) {
          // new branch
          $tmpBranchID = Hash::unique();
          $registerBranch = $this->_db->insert('branches', array(
            'branch_id'       => $tmpBranchID,
            'store_id'       => $storeID,
            'name'          => $branch->name,
            'description'   => $branch->description,
            'branch_status'   => 1
          ));
          if ($registerBranch->errorStatus() === false) {
            if (is_null($address) === false) {
              if ($this->_address->updateBranchAddress($tmpBranchID, $address) === true) {
                return true;
              } else {
                $this->setError($this->_address->getError());
                return false;
              }
            }
          } else {
            $this->setError($dem);
            return false;
          }
        } else {
          // branch already exists with this name
          $this->setError($dem . ' You have already registered a branch with this name.');
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
  public function removeCampus($campus = null)
  {
    $dem = 'Failed to remove campus.';
    if (is_null($campus) === false) {
      if ($this->_school->findCampus($campus) === true) {
        $remove = $this->_db->update('campuses', '`campus_status` = ?', '`campus_id` = ?', array(-1, $campus));
        if ($remove->errorStatus() === false) {
          return true;
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
  public function setCampus($campusID = null)
  {
    if (is_null($campusID) === false) {
      Session::put('activeCampus', $campusID);
      return true;
    } else {
      return false;
    }
  }
  public function getCampus()
  {
    if (Session::exists('activeCampus') === true) {
      return Session::get('activeCampus');
    } else {
      $campuses = $this->getStoreData('campuses');
      if (is_null($campuses) === false) {
        if (is_array($campuses) === true and count($campuses) > 0) {
          $this->setCampus($campuses[0]->id);
          return $this->getCampus();
        }
      }
    }
  }

  // analysis
  public function getMonthlyReport()
  {
    if ($this->isLoggedIn() === true) {
      $year = date('Y');
      $month = date('m');
      $prevYear = date("Y", strtotime("-1 months"));
      $prevMonth = date("m", strtotime("-1 months"));
      $purchases = $this->_purchase->getMonthlyData($this->getStoreData('id'), $year, $month);
      // $prevPurchases = $this->_purchase->getMonthlyData($this->getStoreData('id'), $prevYear, $prevMonth);
      $sales = $this->_sale->getMonthlyData($this->getStoreData('id'), $year, $month);
      // $prevSales = $this->_sale->getMonthlyData($this->getStoreData('id'), $prevYear, $prevMonth);
      $revenue = $this->_sale->getMonthlyRevenueReport($this->getStoreData('id'), $year, $month);
      $report = (object)[
        'purchases'    => (object)[
          'previous'      => null,
          'current'        => $purchases,
          'analysis'      => (object)[
            'difference'      => null
          ]
        ],
        'sales'        => (object)[
          'previous'      => null,
          'current'        => $sales,
          'analysis'      => (object)[
            'difference'      => null
          ]
        ],
        'revenue'      => $revenue
      ];
      return $report;
    } else {
      return null;
    }
  }

  public function getYearlyPurchasesChartData()
  {
    $dem = 'Chart data not found.';
    if ($this->isLoggedIn() === true) {
      $response = (object)[
        'year'      => date('Y'),
        'data'      => (object)[
          'purchases'    => null,
          'sales'        => null
        ]
      ];
      $response->data->purchases   = $this->_purchase->getYearlyChartData($this->getStoreData('id'), date('Y'));
      $response->data->sales       = $this->_sale->getYearlyChartData($this->getStoreData('id'), date('Y'));
      return $response;
    } else {
      $this->setError($dem);
      return false;
    }
  }

  private function _calculateDifference($current = null, $previous = null)
  {
    if (is_null($current) === false and is_null($previous) === false) {
      $result = null;
      if ($current >= $previous) {
        // positive
        $result = (($current / $previous) * 100);
        return (Input::roundMoney($result - 100));
      } else {
        // negative
        $result = (($previous / $current) * 100);
        return - (Input::roundMoney($result - 100));
      }
    } else {
      return null;
    }
  }

  public function getPurchaseReport($type = null)
  {
    $dem = 'Purchase report not found.';
    if ($this->isLoggedIn() === true) {
      $purchaseReport = null;
      switch (strtoupper($type)) {
        case 'DAILY':
          $purchaseReport = $this->_purchase->getDailyReport($this->getStoreData('id'), date('Y-m-d'));
          break;
        case 'MONTHLY':
          $purchaseReport = $this->_purchase->getMonthlyReport($this->getStoreData('id'), date('Y-m'));
          break;
        case 'YEARLY':
          $purchaseReport = $this->_purchase->getYearlyReport($this->getStoreData('id'), date('Y'));
          break;
        default:
          $purchaseReport = $this->_purchase->getDailyReport($this->getStoreData('id'), date('Y-m-d'));
          break;
      }
      if ($purchaseReport !== false and is_null($purchaseReport) === false) {
        return $purchaseReport;
      } else {
        $this->setError($dem);
        return null;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  public function getSaleReport($type = null)
  {
    $dem = 'Purchase report not found.';
    if ($this->isLoggedIn() === true) {
      $purchaseReport = null;
      switch (strtoupper($type)) {
        case 'DAILY':
          $purchaseReport = $this->_sale->getDailyReport($this->getStoreData('id'), date('Y-m-d'));
          break;
        case 'MONTHLY':
          $purchaseReport = $this->_sale->getMonthlyReport($this->getStoreData('id'), date('Y-m'));
          break;
        case 'YEARLY':
          $purchaseReport = $this->_sale->getYearlyReport($this->getStoreData('id'), date('Y'));
          break;
        default:
          $purchaseReport = $this->_sale->getDailyReport($this->getStoreData('id'), date('Y-m-d'));
          break;
      }
      if ($purchaseReport !== false and is_null($purchaseReport) === false) {
        return $purchaseReport;
      } else {
        $this->setError($dem);
        return null;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  public function getProfitLossReport($type = null)
  {
    $dem = 'Purchase report not found.';
    if ($this->isLoggedIn() === true) {
      $purchaseReport = null;
      $saleReport = null;
      $revenue = null;
      switch (strtoupper($type)) {
        case 'DAILY':
          $purchaseReport = $this->_purchase->getDailyReport($this->getStoreData('id'), date('Y-m-d'));
          $saleReport     = $this->_sale->getDailyReport($this->getStoreData('id'), date('Y-m-d'));
          break;
        case 'MONTHLY':
          $purchaseReport = $this->_purchase->getMonthlyReport($this->getStoreData('id'), date('Y-m'));
          $saleReport     = $this->_sale->getMonthlyReport($this->getStoreData('id'), date('Y-m'));
          $revenue = $this->_sale->getMonthlyRevenueReport($this->getStoreData('id'), date('Y'), date('m'));
          break;
        case 'YEARLY':
          $purchaseReport = $this->_purchase->getYearlyReport($this->getStoreData('id'), date('Y'));
          $saleReport     = $this->_sale->getYearlyReport($this->getStoreData('id'), date('Y'));
          $revenue = $this->_sale->getYearlyRevenueReport($this->getStoreData('id'), date('Y'), date('m'));
          break;
        default:
          $purchaseReport = $this->_purchase->getDailyReport($this->getStoreData('id'), date('Y-m-d'));
          $saleReport     = $this->_sale->getDailyReport($this->getStoreData('id'), date('Y-m-d'));
          break;
      }
      $response = (object)[
        'sale'        => $saleReport,
        'purchase'    => $purchaseReport,
        'revenue'      => $revenue
      ];
      return $response;
    } else {
      $this->setError($dem);
      return false;
    }
  }

  // buyers
  public function getBuyer($buyerID = null)
  {
    $dem = 'Buyer not found.';
    if (is_null($buyerID) === false) {
      $buyer = $this->_buyer->find($buyerID);
      if (is_null($buyer) === false and $buyer !== false) {
        return $buyer;
      } else {
        $this->setError($this->_buyer->getError());
        return null;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  public function getBuyers()
  {
    $dem = 'Buyers not found.';
    if ($this->isLoggedIn() === true) {
      $buyers = $this->_buyer->getAll($this->getStoreData('id'));
      if (is_null($buyers) === false) {
        return $buyers;
      } else {
        $this->setError($this->_buyer->getError());
        return null;
      }
    } else {
      $this->setError($dem);
      return null;
    }
  }

  public function getBuyerSales($buyerID = null)
  {
    $dem = 'Buyer sales not found.';
    if (is_null($buyerID) === false) {
      $buyerSales = $this->_sale->getBuyerSales($this->getStoreData('id'), $buyerID);
      if (is_null($buyerSales) === false and $buyerSales !== false) {
        return $buyerSales;
      } else {
        $this->setError($this->_sale->getError());
        return null;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  // suppliers
  public function getSupplier($supplierID = null)
  {
    $dem = 'Supplier not found.';
    if (is_null($supplierID) === false) {
      $supplier = $this->_supplier->find($supplierID);
      if (is_null($supplier) === false and $supplier !== false) {
        return $supplier;
      } else {
        $this->setError($this->_supplier->getError());
        return null;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  public function getSuppliers()
  {
    $dem = 'Buyers not found.';
    if ($this->isLoggedIn() === true) {
      $buyers = $this->_supplier->getAll($this->getStoreData('id'));
      if (is_null($buyers) === false) {
        return $buyers;
      } else {
        $this->setError($this->_buyer->getError());
        return null;
      }
    } else {
      $this->setError($dem);
      return null;
    }
  }

  public function getSupplierSupplies($supplierID = null)
  {
    $dem = 'Supplies not found.';
    if (is_null($supplierID) === false) {
      $supplies = $this->_purchase->getSupplierSupplies($this->getStoreData('id'), $supplierID);
      if (is_null($supplies) === false and $supplies !== false) {
        return $supplies;
      } else {
        $this->setError($this->_sale->getError());
        return null;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }


  // person
  private function _createPerson($data = null)
  {
    if ($this->__parseCreatePersonData($data) === true) {
      $personID = Hash::unique();
      $createPerson = $this->_db->insert('persons', array(
        'person_id'   => $personID,
        'first_name'  => $data->firstName,
        'surname'     => $data->surname,
        'cnic'        => $data->cnic
      ));
      if ($createPerson->errorStatus() === false) {
        return (object)[
          'status'    => true,
          'type'      => 1, // creating person
          'id'        => $personID
        ];
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  // expenses
  public function newExpense($details = null)
  {
    $dem = 'Failed to add new expense.';
    if ($this->isLoggedIn() === true and $this->__parseExpenseData($details) === true) {
      $etID = null;
      $type = $this->_expense->searchType($details->expenseType);
      if (is_object($type) === true and isset($type->id) === true) {
        // expense type found
        $etID = $type->id;
      } elseif ($type !== false and is_null($type) === true) {
        // create expense type
        $etID = Hash::unique();
        $createExpenseType = $this->_db->insert('expense_categories', array(
          'ec_id'    => $etID,
          'name'    => $details->expenseType
        ));
        if ($createExpenseType->errorStatus() === true) {
          $this->setError($dem);
          return false;
        }
      } else {
        $this->setError($dem);
        return false;
      }
      $cbID = Hash::unique();
      $makeCashBookEntry = $this->_db->insert('cash_book', array(
        'pt_id'            => $cbID,
        'store_id'        => $this->getStoreData('id'),
        'amount'          => $details->expenseAmount,
        'type'            => 1,
        'major'            => 5,
        'pt_status'        => 1
      ));
      if ($makeCashBookEntry->errorStatus() === false) {
        // cash book entry done
        $createExpenseEntry = $this->_db->insert('expenses', array(
          'expense_id'      => hash::unique(),
          'ec_id'            => $etID,
          'store_id'        => $this->getStoreData('id'),
          'pt_id'            => $cbID,
          'note'            => $details->expenseNote,
          'expense_status'  => 1
        ));
        if ($createExpenseEntry->errorStatus() === false) {
          return true;
        } else {
          $suspendCashBookEntry = $this->_db->update('cash_book', '`pt_status` = ?', '`pt_id` = ?', array(-1, $cbID));
          if ($suspendCashBookEntry->errorStatus() === true) {
            $suspendCashBookEntry = $this->_db->update('cash_book', '`pt_status` = ?', '`pt_id` = ?', array(-1, $cbID));
          }
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

  public function getExpenses()
  {
    $dem = 'Expenses not found.';
    if ($this->isLoggedIn() === true) {
      $expenses = $this->_expense->getAll($this->getStoreData('id'));
      if (is_null($expenses) === false) {
        return $expenses;
      } else {
        $this->setError($dem);
        return null;
      }
    } else {
      $this->setError($dem);
      return false;
    }
  }

  // data parsers
  private function _parseRegisterData($data = null)
  {
    if (is_null($data) === false and is_object($data) === true) {
      $data = (object)$data;
      if (count(get_object_vars($data)) > 0) {
        if (isset($data->storeName) === true and isset($data->ownerEmail) === true and isset($data->ownerPassword) === true) {
          return true;
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
  private function __parseUpdateProfileData($data = null)
  {
    if (is_null($data) === false and is_object($data) === true) {
      $data = (object)$data;
      if (isset($data->firstName) === true and empty($data->firstName) === false and isset($data->surname) === true and empty($data->surname) === false and isset($data->email) === true and empty($data->email) === false and isset($data->contact) === true and empty($data->contact) === false) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
  private function __parseChangePasswordData($data = null)
  {
    if (is_null($data) === false and is_object($data) === true) {
      $data = (object)$data;
      if (isset($data->oldPassword) === true and isset($data->newPassword) === true and isset($data->newPasswordConfirm) === true) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  private function _parseStoreData($data = null)
  {
    if (is_null($data) === false and is_object($data) === true) {
      $data = (object)$data;
      if (count(get_object_vars($data)) > 0 and isset($data->storeName) === true) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
  private function __parseUpdateSchoolProfileData($data = null)
  {
    if (is_null($data) === false and is_object($data) === true) {
      $data = (object)$data;
      if (isset($data->schoolName) === true and empty($data->schoolName) === false and isset($data->schoolEmail) === true and empty($data->schoolEmail) === false and isset($data->schoolContact) === true and empty($data->schoolContact) === false) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
  private function __parseSchoolAddressData($data = null)
  {
    if (is_null($data) === false and is_object($data) === true) {
      $data = (object)$data;
      if (isset($data->country) === true and empty($data->country) === false and isset($data->state) === true and isset($data->province) === true and empty($data->province) === false and isset($data->city) === true and empty($data->city) === false and isset($data->street) === true and empty($data->street) === false) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  private function __parseFacultyData($data = null)
  {
    if (is_null($data) === false and is_object($data) === true) {
      $data = (object)$data;
      if (isset($data->firstName) === true and isset($data->surname) === true and isset($data->email) === true and isset($data->contact) === true) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  private function __parseFrontData($data = null)
  {
    if (is_null($data) === false and is_object($data) === true) {
      $data = (object)$data;
      if (isset($data->name) === true and empty($data->name) === false and isset($data->description) === true) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  private function __parseItemBasicDetails($data = null)
  {
    if (is_null($data) === false and is_object($data) === true) {
      $data = (object)$data;
      if (isset($data->itemName) === true and isset($data->itemCategory) === true and isset($data->itemBrand) === true) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  private function __parseItemVariantsDetails($data = null)
  {
    if (is_null($data) === false and is_array($data) === true and count($data) > 0) {
      foreach ($data as $variant) {
        if (is_object($variant) === true) {
          $variant = (object)$variant;
          if (isset($variant->size) === true and isset($variant->color) === true and isset($variant->price) === true) {
            true;
          } else {
            return false;
          }
        } else {
          return false;
        }
      }
      return true;
    } else {
      return false;
    }
  }

  private function __parseExpenseData($data = null)
  {
    if (is_object($data) === true) {
      $data = (object)$data;
      if (isset($data->expenseType) === true and isset($data->expenseAmount) === true and isset($data->expenseNote) === true) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}
