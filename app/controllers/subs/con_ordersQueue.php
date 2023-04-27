<?php

class con_ordersQueue extends Controller
{
  public static function index()
  {
    echo 'index';
  }

  public static function home()
  {
    echo 'home';
  }

  public static function new()
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
    $user = new User();
    // user is logged in, check which one it is
    $checkUser = $user->checkLogin();
    if (isset($checkUser->status) === true and $checkUser->status === true and isset($checkUser->user) === true) {
      $directory = 'visitor';
      switch ($checkUser->user) {
        case 'WO_RASEED_OWNER':
          $user = new Owner();
          $directory = 'owner';
          break;

        case 'WO_RASEED_SALES_MAN':
          $user = new SalesMan();
          $directory = 'salesman';
          break;

        default:
          self::_showError();
          break;
          exit();
      }
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . $directory . DS . 'orders-queue-start.php';
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
      if (Form::submitted() === true and Input::postExists('checkOutSale') === true and strtolower(Input::getPost('checkOutSale')) == 'true' and Input::postExists('saleItemID') === true) {
        $tmpArray = array();
        $saleItem     = Input::getPost('saleItem');
        $saleItemID   = Input::getPost('saleItemID');
        $saleQuantity = Input::getPost('saleQuantity');
        $salePrice    = Input::getPost('salePrice');
        for ($i = 0; $i < count(Input::getPost('saleItemID')); $i++) {
          $tmp = (object)[
            'saleItem'      => $saleItem[$i],
            'saleItemID'    => $saleItemID[$i],
            'saleQuantity'  => $saleQuantity[$i],
            'salePrice'     => $salePrice[$i],
          ];
          array_push($tmpArray, json_encode($tmp));
        }
        if ($user->saleStart($tmpArray) === true) {
          // processed successfully
          self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'orders-queue-checkout.php';
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
              'cart'          => Session::get('WO_RASEED_CART_SALE'),
              'lastReceiptNo' => $user->lastSaleReceiptNo(),
              'buyers'        => $user->getBuyers()
            ];
            self::setView(self::$tmp_file, $response);
          } else {
            $response->status = false;
            self::_showError();
          }
        } else {
          // processing failed
          array_push($response->message->error, $user->getError());
          self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'orders-queue-new.php';
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
        }
      } else {
        Redirect::to('./dashboard/orders-queue/new');
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
      if (Form::submitted() === true and Input::postExists('confirmSale') === true and strtolower(Input::getPost('confirmSale')) == 'true') {
        if ($user->saleConfirm($_POST, true) === true) {
          Session::flash('successMessage', 'Order has been queued successfully.');
          Redirect::to('./dashboard/orders-queue/new');
          exit();
        } else {
          Session::flash('errorMessage', 'Failed to complete sale.');
          array_push($response->message->error, $user->getError());
        }
      } else {
        Redirect::to('./dashboard/orders-queue/new');
        exit();
      }
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'orders-queue-checkout.php';
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
          'cart'          => Session::get('WO_RASEED_CART_SALE')
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

  public static function view()
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
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'orders-queue-view-all.php';
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
          'stockBook'     => $user->getQueue((object)[
            'payment'         => true
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
}
