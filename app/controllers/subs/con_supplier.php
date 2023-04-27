<?php
class con_supplier extends Controller
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
        self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'supplier-view-all.php';
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
            'suppliers'     => $user->getSuppliers()
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

  public static function purchases($action = null)
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
        self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'supplier-purchases.php';
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
            'buyer'         => $user->getSupplier($action),
            'supplies'      => $user->getSupplierSupplies($action)
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
}
