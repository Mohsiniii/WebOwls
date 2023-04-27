<?php
class con_buyer extends Controller
{
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
        self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'buyer-view-all.php';
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
            'buyers'        => $user->getBuyers()
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

  public static function sales($action = null)
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
        self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'buyer-sales.php';
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
            'buyer'         => $user->getBuyer($action),
            'sales'         => $user->getBuyerSales($action)
          ];
          self::setView(self::$tmp_file, $response);
        } else {
          $response->status = false;
          self::_showError();
        }
      } else {
        Redirect::to('./dasbhoard/buyer/view/all/');
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
        if (Input::postExists('buyerName') === true) {
          // form submitted
          if ($user->updateBuyer($action, (object)[
            'name'    => Input::getPost('buyerName'),
            'contact' => Input::getPost('buyerContact'),
            'area'    => Input::getPost('buyerArea'),
          ]) === true) {
            $response->message->success = 'Customer profile has been updated.';
            Session::flash('successMessage', 'Customer profile has been updated');
          } else {
            $response->message->error = $user->getError();
          }
        }
        self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'buyer-edit.php';
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
            'buyer'         => $user->getBuyer($action),
            'sales'         => $user->getBuyerSales($action)
          ];
          self::setView(self::$tmp_file, $response);
        } else {
          $response->status = false;
          self::_showError();
        }
      } else {
        Redirect::to('./dasbhoard/buyer/view/all/');
        exit();
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
}
