<?php
class con_purchase extends Controller
{

  public static function index($action = null)
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
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'purchase-start.php';
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
          'items'         => $user->getItems((object)[
            'variants'    => true
          ])
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

  public static function checkout($action = null)
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
      if (Form::submitted() === true and Input::postExists('checkOutPurchase') === true and strtolower(Input::getPost('checkOutPurchase')) == 'true' and Input::postExists('purchaseItemID') === true) {
        $tmpArray = array();
        $purchaseItem     = Input::getPost('purchaseItem');
        $purchaseItemID   = Input::getPost('purchaseItemID');
        $purchaseQuantity = Input::getPost('purchaseQuantity');
        $purchasePrice    = Input::getPost('purchasePrice');
        for ($i = 0; $i < count(Input::getPost('purchaseItemID')); $i++) {
          $tmp = (object)[
            'purchaseItem'      => $purchaseItem[$i],
            'purchaseItemID'    => $purchaseItemID[$i],
            'purchaseQuantity'  => $purchaseQuantity[$i],
            'purchasePrice'     => $purchasePrice[$i],
          ];
          array_push($tmpArray, json_encode($tmp));
        }
        if ($user->purchaseStart($tmpArray) === true) {
          // die('true');
        } else {
          Redirect::to('./dashboard/purchase');
        }
      } else {
        Redirect::to('./dashboard/purchase');
      }
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'purchase-checkout.php';
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
          'items'         => $user->getItems((object)[
            'variants'    => true
          ]),
          'cart'          => Session::get('WO_RASEED_CART'),
          'lastReceiptNo' => $user->lastPurchaseReceiptNo(),
          'suppliers'     => $user->getSuppliers()
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

  public static function confirm($action = null)
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
      if (Form::submitted() === true and Input::postExists('confirmPurchase') === true and strtolower(Input::getPost('confirmPurchase')) == 'true') {
        if ($user->purchaseConfirm($_POST) === true) {
          Session::flash('successMessage', 'Purchasing completed successfully.');
          Redirect::to('./dashboard/purchase');
          exit();
        } else {
          Session::flash('errorMessage', 'Failed to complete purchasing.');
          array_push($response->message->error, $user->getError());
          // Redirect::to('./dashboard/purchase');
        }
      } else {
        Redirect::to('./dashboard/purchase');
      }
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'purchase-checkout.php';
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
          'items'         => $user->getItems((object)[
            'variants'    => true
          ]),
          'cart'          => Session::get('WO_RASEED_CART')
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

  public static function receipt($action = null)
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
      // check receipt no
      $receipt = null;
      if (is_null($action) === false and is_string($action) === true and strlen($action) === 32) {
        $receipt = $user->getPurchaseReceipt($action);
        if ($receipt === false or is_null($receipt) === true) {
          Redirect::to('./dashboard/purchase');
        }
      } else {
        Redirect::to('./dashboard/purchase');
      }
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'purchase-receipt.php';
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
          'items'         => $user->getItems((object)[
            'variants'        => true
          ]),
          'receipt'       => $receipt
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
}
