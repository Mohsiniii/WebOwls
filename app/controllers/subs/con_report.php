<?php
class con_report extends Controller
{
  public static function index($action = null)
  {
    Redirect::to('./dashboard/home/');
  }

  public static function purchase($action = null)
  {
    $response = (object)[
      'status'      => true,
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
      $purchaseReport = null;
      $reportType = 'DAILY';
      if (Input::getExists('type') === true) {
        $reportType = strtoupper(Input::get('type'));
      }
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'report-purchase.php';
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
          'report'        => (object)[
            'type'            => $reportType,
            'data'            => $user->getPurchaseReport($reportType)
          ]
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

  public static function sale($action = null)
  {
    $response = (object)[
      'status'      => true,
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
      $purchaseReport = null;
      $reportType = 'DAILY';
      if (Input::getExists('type') === true) {
        $reportType = strtoupper(Input::get('type'));
      }
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'report-sale.php';
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
          'report'        => (object)[
            'type'            => $reportType,
            'data'            => $user->getSaleReport($reportType)
          ]
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

  public static function custom($action = null)
  {
    $response = (object)[
      'status'      => true,
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
      $purchaseReport = null;
      $reportType = 'DAILY';
      if (Input::getExists('type') === true) {
        $reportType = strtoupper(Input::get('type'));
      }
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'report-custom.php';
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
          'buyers'        => $user->getBuyers(),
          'brands'        => $user->getBrands(),
          'report'        => (object)[
            'type'            => $reportType,
            'data'            => $user->getCustomSaleReport(Input::getPost('customer'), Input::getPost('brand'), Input::getPost('daterange'), $reportType)
          ]
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

  public static function profitLoss($action = null)
  {
    $response = (object)[
      'status'      => true,
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
      $purchaseReport = null;
      $reportType = 'DAILY';
      if (Input::getExists('type') === true) {
        $reportType = strtoupper(Input::get('type'));
      }
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'report-profitLoss.php';
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
          'report'        => (object)[
            'type'            => $reportType,
            'data'            => $user->getProfitLossReport($reportType)
          ]
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

  public static function profit($action = null)
  {
    echo 'Dashboard/Report/Profit';
  }
}
