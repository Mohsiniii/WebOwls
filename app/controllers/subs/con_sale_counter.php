<?php
class con_sale_counter extends Controller
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
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'sale-start.php';
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
        $saleCounterForm = new Form('addSaleCounter');
        $saleCounterForm->validate();
        if ($saleCounterForm->errorStatus() === false) {
          // basic inputs validated
          if ($user->addSaleCounter($saleCounterForm->getInputs()) === true) {
            // store item successfully
            Session::flash('successMessage', 'Sale counter has been added successfully.');
            Redirect::to('./dashboard/sale-counter/view/all');
            exit();
          } else {
            // failed to add store item
            array_push($response->message->error, $user->getError());
          }
        } else {
          // errors in item's inputs validation
          foreach ($saleCounterForm->getErrors() as $error) {
            array_push($response->message->error, $error);
          }
        }
      }
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'sale-counter-add.php';
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
      exit();
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
      // check form submission
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'sale-counter-view-all.php';
      if (file_exists(self::$tmp_file) === true) {
        $response->data = (object)[
          'user'          => (object)[
            'name'            => $user->getData('name'),
            'email'           => $user->getData('email'),
          ],
          'store'        => (object)[
            'name'            => $user->getStoreData('name'),
            'fronts'          => null,
          ],
          'saleCounters'  => $user->getSaleCounters()
        ];
        self::setView(self::$tmp_file, $response);
      } else {
        $response->status = false;
        self::_showError();
      }
    } else {
      // user is not logged in
      Redirect::to('./account/login');
      exit();
    }
  }
}
