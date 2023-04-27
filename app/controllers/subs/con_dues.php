<?php
  class con_dues extends Controller {
    public static function index(){
      echo 'dues->home';
    }

    public static function sales($action = null){
      $response = (object)[
        'status'			=> null,
        'type'				=> null,
        'message'     => (object)[
          'error'         => array(),
          'success'       => null
        ],
        'data'				=> null
      ];
      $user = new Owner();
      $checkUser = $user->checkLogin();
      if($checkUser !== false AND isset($checkUser->status) === true AND $checkUser->status === true AND isset($checkUser->user) === true AND $checkUser->user == 'WO_RASEED_OWNER'){
        self::$tmp_file = ROOT_PATH.'app'.DS.'views'.DS.'dashboard'.DS.'owner'.DS.'dues-sale.php';
        if(file_exists(self::$tmp_file) === true){
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
            'dues'        => $user->getSalesDues(),
          ];
          self::setView(self::$tmp_file, $response);
        } else {
          $response->status = false;
          self::_showError();
        }
      } else {
        // user is not logged in
        self::$tmp_file = ROOT_PATH.'app'.DS.'views'.DS.'main'.DS.'visitor'.DS.'index.php';
        if(file_exists(self::$tmp_file) === true){
          require_once self::$tmp_file;
        } else {
          $response->status = false;
          self::_showError(self::$_dem);
        }
      }
    }

    public static function buyers($action = null){
      $response = (object)[
        'status'			=> null,
        'type'				=> null,
        'message'     => (object)[
          'error'         => array(),
          'success'       => null
        ],
        'data'				=> null
      ];
      $user = new Owner();
      $checkUser = $user->checkLogin();
      if($checkUser !== false AND isset($checkUser->status) === true AND $checkUser->status === true AND isset($checkUser->user) === true AND $checkUser->user == 'WO_RASEED_OWNER'){
        self::$tmp_file = ROOT_PATH.'app'.DS.'views'.DS.'dashboard'.DS.'owner'.DS.'dues-buyers.php';
        if(file_exists(self::$tmp_file) === true){
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
            'dues'        => $user->getBuyersDues(),
          ];
          self::setView(self::$tmp_file, $response);
        } else {
          $response->status = false;
          self::_showError();
        }
      } else {
        // user is not logged in
        self::$tmp_file = ROOT_PATH.'app'.DS.'views'.DS.'main'.DS.'visitor'.DS.'index.php';
        if(file_exists(self::$tmp_file) === true){
          require_once self::$tmp_file;
        } else {
          $response->status = false;
          self::_showError(self::$_dem);
        }
      }
    }

    public static function buyer($action = null){
      $response = (object)[
        'status'			=> null,
        'type'				=> null,
        'message'     => (object)[
          'error'         => array(),
          'success'       => null
        ],
        'data'				=> null
      ];
      $user = new Owner();
      $checkUser = $user->checkLogin();
      if($checkUser !== false AND isset($checkUser->status) === true AND $checkUser->status === true AND isset($checkUser->user) === true AND $checkUser->user == 'WO_RASEED_OWNER'){
        self::$tmp_file = ROOT_PATH.'app'.DS.'views'.DS.'dashboard'.DS.'owner'.DS.'dues-buyer-history.php';
        if(file_exists(self::$tmp_file) === true){
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
            'buyer'       => $user->getBuyer($action),
            'dues'        => $user->getBuyerDuesHistory($action),
          ];
          self::setView(self::$tmp_file, $response);
        } else {
          $response->status = false;
          self::_showError();
        }
      } else {
        // user is not logged in
        self::$tmp_file = ROOT_PATH.'app'.DS.'views'.DS.'main'.DS.'visitor'.DS.'index.php';
        if(file_exists(self::$tmp_file) === true){
          require_once self::$tmp_file;
        } else {
          $response->status = false;
          self::_showError(self::$_dem);
        }
      }
    }

    public static function receive($action = null){
      $response = (object)[
        'status'			=> null,
        'type'				=> null,
        'message'     => (object)[
          'error'         => array(),
          'success'       => null
        ],
        'data'				=> null
      ];
      $user = new Owner();
      $checkUser = $user->checkLogin();
      if($checkUser !== false AND isset($checkUser->status) === true AND $checkUser->status === true AND isset($checkUser->user) === true AND $checkUser->user == 'WO_RASEED_OWNER'){
        if(strtolower($action) == 'sale'){
          // sale dues
          if(Input::postExists('dues') === true AND Input::postExists('saleID') === true AND Input::postExists('amount') === true){
            if($user->receiveDues(Input::getPost('saleID'), Input::getPost('amount')) === true){
              $response->message->success = 'Dues received successfully.';
              $response->status = true;
            } else {
              $response->message->error = $user->getError();
              $response->status = false;
            }
          } else {
            $response->message->error = 'Failed to receive dues. Please try again.';
            $response->status = false;
          }
        } elseif(strtolower($action == 'buyer')){
          // buyer dues
          if(Input::postExists('dues') === true AND Input::postExists('buyerID') === true AND Input::postExists('amount') === true){
            if($user->receiveBuyerDues(Input::getPost('buyerID'), Input::getPost('amount')) === true){
              $response->message->success = 'Dues received successfully.';
              $response->status = true;
            } else {
              $response->message->error = $user->getError();
              $response->status = false;
            }
          } else {
            $response->message->error = 'Failed to receive dues. Please try again.';
            $response->status = false;
          }
        } else {
          // error
          $response->message->error = 'Failed to receive dues. Please try again.';
          $response->status = false;
        }
      } else {
        // user is not logged in
        $response->message->error = 'Your session has expired, please login again.';
        $response->status = false;
      }
      echo json_encode($response);
    }
  }
