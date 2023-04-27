<?php
class con_item extends Controller
{

  public static function add($action = null)
  {
    $response = (object)[
      'status'      => null,
      'type'        => null,
      'message'     => (object)[
        'error'         => array(),
        'success'       => null
      ],
      'data'        => null
    ];
    $user = new Owner();
    $checkUser = $user->checkLogin();
    if ($checkUser !== false and isset($checkUser->status) === true and $checkUser->status === true and isset($checkUser->user) === true and $checkUser->user == 'WO_RASEED_OWNER') {
      // check form submission
      if (Form::submitted() === true and Input::postExists('addInventoryItem') === true and strtolower(Input::getPost('addInventoryItem')) == 'true') {
        $basicItemDetails = new Form('addInventoryItem');
        $basicItemDetails->validate();
        if ($basicItemDetails->errorStatus() === false) {
          // basic item's inputs validated, check variants
          $addStoreItem = $user->addStoreItem($basicItemDetails->getInputs(), Input::getPost('option'));
          if (isset($addStoreItem->status) === true and $addStoreItem->status === true) {
            // store item successfully
            Session::flash('successMessage', 'Item added to your inventory.');
            Redirect::to('./dashboard/item/view/all');
          } else {
            // failed to add store item
            array_push($response->message->error, $user->getError());
          }
        } else {
          // errors in item's inputs validation
          foreach ($basicItemDetails->getErrors() as $error) {
            array_push($response->message->error, $error);
          }
        }
      }
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'item-add.php';
      if (file_exists(self::$tmp_file) === true) {
        $response->data = (object)[
          'user'          => (object)[
            'name'            => $user->getData('name'),
            'email'           => $user->getData('email'),
          ],
          'store'        => (object)[
            'name'            => $user->getStoreData('name'),
            'fronts'          => null,
          ]
        ];
        self::setView(self::$tmp_file, $response);
      } else {
        $response->status = false;
        self::_showError();
      }
    } else {
      // user is not logged in
      Redirect::to('./account/login');
    }
  }

  public static function addVariants()
  {
    $response = (object)[
      'status'      => null,
      'type'        => null,
      'message'     => (object)[
        'error'         => array(),
        'success'       => null
      ],
      'data'        => null
    ];
    $user = new Owner();
    $checkUser = $user->checkLogin();
    if ($checkUser !== false and isset($checkUser->status) === true and $checkUser->status === true and isset($checkUser->user) === true and $checkUser->user == 'WO_RASEED_OWNER') {
      // check item id
      if (Input::getExists('item') === false) {
        Redirect::to('./dashboard/item/view/all');
      }
      // check form submission
      if (Form::submitted() === true and Input::postExists('addItemVariant') === true and strtolower(Input::getPost('addItemVariant')) == 'true') {
        if ($user->addVariants(Input::get('item'), $_POST) === true) {
          Session::flash('successMessage', 'Variants addedd successfully.');
          Redirect::to('./dashboard/item/add-finish?item=' . Input::get('item'));
        } else {
          array_push($response->message->error, $user->getError());
        }
      }
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'item-variants-add.php';
      if (file_exists(self::$tmp_file) === true) {
        // $faculty = $user->getFaculty();var_dump($faculty);die();
        $response->data = (object)[
          'user'          => (object)[
            'name'            => $user->getData('name'),
            'email'           => $user->getData('email'),
          ],
          'store'        => (object)[
            'name'            => $user->getStoreData('name'),
            'fronts'          => null
          ],
          'item'        => $user->getItem(Input::get('item')),
          'options'     => $user->getItemOptions(Input::get('item'))
        ];
        self::setView(self::$tmp_file, $response);
      } else {
        $response->status = false;
        self::_showError();
      }
    } else {
      // user is not logged in
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'main' . DS . 'visitor' . DS . 'index.php';
      if (file_exists(self::$tmp_file) === true) {
        require_once self::$tmp_file;
      } else {
        $response->status = false;
        self::_showError(self::$_dem);
      }
    }
  }

  public static function addStock()
  {
    $response = (object)[
      'status'      => null,
      'type'        => null,
      'message'     => (object)[
        'error'         => array(),
        'success'       => null
      ],
      'data'        => null
    ];
    $user = new Owner();
    $checkUser = $user->checkLogin();
    if ($checkUser !== false and isset($checkUser->status) === true and $checkUser->status === true and isset($checkUser->user) === true and $checkUser->user == 'WO_RASEED_OWNER') {
      // check item id
      if (Input::getExists('item') === false) {
        Redirect::to('./dashboard/item/view/all');
      }
      // check form submission
      if (Form::submitted() === true and Input::postExists('addItemVariantStock') === true and strtolower(Input::getPost('addItemVariantStock')) == 'true') {
        if ($user->addStock(Input::get('item'), $_POST) === true) {
          Session::flash('successMessage', 'Stock addedd successfully.');
          Redirect::to('./dashboard/item/add-finish?item=' . Input::get('item'));
        } else {
          array_push($response->message->error, $user->getError());
        }
      }
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'item-stock-add.php';
      if (file_exists(self::$tmp_file) === true) {
        // $faculty = $user->getFaculty();var_dump($faculty);die();
        $response->data = (object)[
          'user'          => (object)[
            'name'            => $user->getData('name'),
            'email'           => $user->getData('email'),
          ],
          'store'        => (object)[
            'name'            => $user->getStoreData('name'),
            'fronts'          => null
          ],
          'item'        => $user->getItem(Input::get('item')),
          'variants'    => $user->getItemVariants(Input::get('item')),
        ];
        self::setView(self::$tmp_file, $response);
      } else {
        $response->status = false;
        self::_showError();
      }
    } else {
      // user is not logged in
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'main' . DS . 'visitor' . DS . 'index.php';
      if (file_exists(self::$tmp_file) === true) {
        require_once self::$tmp_file;
      } else {
        $response->status = false;
        self::_showError(self::$_dem);
      }
    }
  }

  public static function addFinish()
  {
    $response = (object)[
      'status'      => null,
      'type'        => null,
      'message'     => (object)[
        'error'         => array(),
        'success'       => null
      ],
      'data'        => null
    ];
    $user = new Owner();
    $checkUser = $user->checkLogin();
    if ($checkUser !== false and isset($checkUser->status) === true and $checkUser->status === true and isset($checkUser->user) === true and $checkUser->user == 'WO_RASEED_OWNER') {
      // check item id
      if (Input::getExists('item') === false) {
        Redirect::to('./dashboard/item/view/all');
      }
      // check form submission
      if (Form::submitted() === true and Input::postExists('addItemVariantStock') === true and strtolower(Input::getPost('addItemVariantStock')) == 'true') {
        if ($user->addStock(Input::get('item'), $_POST) === true) {
          Session::flash('successMessage', 'Stock addedd successfully.');
          Redirect::to('./dashboard/item/add-finish?item=' . Input::get('item'));
        } else {
          array_push($response->message->error, $user->getError());
        }
      }
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'item-finish.php';
      if (file_exists(self::$tmp_file) === true) {
        // $faculty = $user->getFaculty();var_dump($faculty);die();
        $response->data = (object)[
          'user'          => (object)[
            'name'            => $user->getData('name'),
            'email'           => $user->getData('email'),
          ],
          'store'        => (object)[
            'name'            => $user->getStoreData('name'),
            'fronts'          => null
          ],
          'item'        => Input::get('item'),
          'variants'    => $user->getItemVariants(Input::get('item')),
        ];
        self::setView(self::$tmp_file, $response);
      } else {
        $response->status = false;
        self::_showError();
      }
    } else {
      // user is not logged in
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'main' . DS . 'visitor' . DS . 'index.php';
      if (file_exists(self::$tmp_file) === true) {
        require_once self::$tmp_file;
      } else {
        $response->status = false;
        self::_showError(self::$_dem);
      }
    }
  }

  public static function view($action = null)
  {
    $response = (object)[
      'status'      => null,
      'type'        => null,
      'message'     => (object)[
        'error'         => array(),
        'success'       => null
      ],
      'data'        => null
    ];
    $user = new Owner();
    $checkUser = $user->checkLogin();
    if ($checkUser !== false and isset($checkUser->status) === true and $checkUser->status === true and isset($checkUser->user) === true and $checkUser->user == 'WO_RASEED_OWNER') {
      if (is_null($action) === false and is_string($action) === true and strlen($action) == 32) {
        self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'item-view.php';
        if (file_exists(self::$tmp_file) === true) {
          $response->data = (object)[
            'user'          => (object)[
              'name'            => $user->getData('name'),
              'email'           => $user->getData('email'),
            ],
            'store'         => (object)[
              'name'            => $user->getStoreData('name'),
              'fronts'          => null
            ],
            'item'         => $user->getItem($action, (object)[
              'variants'    => (object)[
                'stock'         => true,
                'price'         => true,
                'discount'      => true
              ]
            ])
          ];
          self::setView(self::$tmp_file, $response);
        } else {
          $response->status = false;
          self::_showError();
        }
      } else {
        self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'item-view-all.php';
        if (file_exists(self::$tmp_file) === true) {
          $response->data = (object)[
            'user'          => (object)[
              'name'            => $user->getData('name'),
              'email'           => $user->getData('email'),
            ],
            'store'         => (object)[
              'name'            => $user->getStoreData('name'),
              'fronts'          => null
            ],
            'items'         => $user->getItems()
          ];
          self::setView(self::$tmp_file, $response);
        } else {
          $response->status = false;
          self::_showError();
        }
      }
    } else {
      // user is not logged in
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'main' . DS . 'visitor' . DS . 'index.php';
      if (file_exists(self::$tmp_file) === true) {
        require_once self::$tmp_file;
      } else {
        $response->status = false;
        self::_showError(self::$_dem);
      }
    }
  }

  public static function edit($action = null)
  {
    $response = (object)[
      'status'      => null,
      'type'        => null,
      'message'     => (object)[
        'error'         => array(),
        'success'       => null
      ],
      'data'        => null
    ];
    $user = new Owner();
    $checkUser = $user->checkLogin();
    if ($checkUser !== false and isset($checkUser->status) === true and $checkUser->status === true and isset($checkUser->user) === true and $checkUser->user == 'WO_RASEED_OWNER') {
      if (is_null($action) === false and is_string($action) === true and strlen($action) == 32) {
        if (Input::postExists('editItem') === true and Input::postExists('itemName') === true and Input::postExists('itemCategory') === true and Input::postExists('itemBrand') === true) {
          $basicItemDetails = new Form('addInventoryItem');
          $basicItemDetails->validate();
          if ($basicItemDetails->errorStatus() === false) {
            // basic item's inputs validated, check variants
            if ($user->editStoreItem($action, $basicItemDetails->getInputs()) === true) {
              $response->message->success = 'Details updated successfully.';
              Session::flash('successMessage', 'Details updated successfully.');
              Redirect::to('./dashboard/item/view/' . $action);
              exit();
            } else {
              array_push($response->message->error, $user->getError());
            }
          } else {
            // errors in item's inputs validation
            foreach ($basicItemDetails->getErrors() as $error) {
              array_push($response->message->error, $error);
            }
          }
        }
        self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'item-edit.php';
        if (file_exists(self::$tmp_file) === true) {
          $response->data = (object)[
            'user'          => (object)[
              'name'            => $user->getData('name'),
              'email'           => $user->getData('email'),
            ],
            'store'         => (object)[
              'name'            => $user->getStoreData('name'),
              'fronts'          => null
            ],
            'item'         => $user->getItem($action)
          ];
          self::setView(self::$tmp_file, $response);
        } else {
          $response->status = false;
          self::_showError();
        }
      } else {
        self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'item-view-all.php';
        if (file_exists(self::$tmp_file) === true) {
          $response->data = (object)[
            'user'          => (object)[
              'name'            => $user->getData('name'),
              'email'           => $user->getData('email'),
            ],
            'store'         => (object)[
              'name'            => $user->getStoreData('name'),
              'fronts'          => null
            ],
            'items'         => $user->getItems()
          ];
          self::setView(self::$tmp_file, $response);
        } else {
          $response->status = false;
          self::_showError();
        }
      }
    } else {
      // user is not logged in
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'main' . DS . 'visitor' . DS . 'index.php';
      if (file_exists(self::$tmp_file) === true) {
        require_once self::$tmp_file;
      } else {
        $response->status = false;
        self::_showError(self::$_dem);
      }
    }
  }

  public static function remove($action = null)
  {
    $response = (object)[
      'status'      => null,
      'type'        => null,
      'message'     => (object)[
        'error'         => array(),
        'success'       => null
      ],
      'data'        => null
    ];
    $user = new Owner();
    $checkUser = $user->checkLogin();
    if ($checkUser !== false and isset($checkUser->status) === true and $checkUser->status === true and isset($checkUser->user) === true and $checkUser->user == 'WO_RASEED_OWNER') {
      if (is_null($action) === false and is_string($action) === true and strlen($action) == 32) {
        if ($user->removeStoreItem($action) === true) {
          Session::flash('successMessage', 'Item removed successfully.');
        } else {
          Session::flash('errorMessage', $user->getError());
          array_push($response->message->error, $user->getError());
        }
        Redirect::to('./dashboard/item/view/all/');
      } else {
        Redirect::to('./dashboard/item/view/all/');
      }
    } else {
      // user is not logged in
      Redirect::to('./dashboard/');
    }
  }

  public static function removeVariant($action = null)
  {
    $response = (object)[
      'status'      => null,
      'type'        => null,
      'message'     => (object)[
        'error'         => array(),
        'success'       => null
      ],
      'data'        => null
    ];
    $user = new Owner();
    $checkUser = $user->checkLogin();
    if ($checkUser !== false and isset($checkUser->status) === true and $checkUser->status === true and isset($checkUser->user) === true and $checkUser->user == 'WO_RASEED_OWNER') {
      if (is_null($action) === false and is_string($action) === true and strlen($action) == 32) {
        if ($user->removeStoreItemVariant($action) === true) {
          Session::flash('successMessage', 'Item variant removed successfully.');
        } else {
          Session::flash('errorMessage', $user->getError());
          array_push($response->message->error, $user->getError());
        }
        Redirect::to('./dashboard/item/view/' . Input::get('item') . '/');
      } else {
        Redirect::to('./dashboard/item/view/all/');
      }
    } else {
      // user is not logged in
      Redirect::to('./dashboard/');
    }
  }

  private function viewAll()
  {
    //
  }
}
