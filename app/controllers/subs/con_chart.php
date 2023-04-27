<?php
  class con_chart extends Controller {
    public static function index($action = null){
      $response = (object)[
        'status'			=> true,
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
        if(Input::getExists('major') === true AND strtolower(Input::get('major')) == 'sp' AND Input::getExists('type') === true AND strtolower(Input::get('type')) == 'year'){
          $purchasesChartData = $user->getYearlyPurchasesChartData();
          $response->data = $purchasesChartData;
        } else {
          if(Input::getExists('major') === false){
            $_GET['major'] = 'sp';
          }
          if(Input::getExists('type') === false){
            $_GET['type'] = 'year';
          }
        }
        echo json_encode($response);
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
  }
