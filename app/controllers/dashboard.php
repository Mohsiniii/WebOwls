<?php
class Dashboard extends Controller
{

  public static function index()
  {
    $user = new User();
    $checkUser = $user->checkLogin();
    if ($checkUser !== false) {
      // user is logged in, check which one it is
      if (isset($checkUser->status) === true and $checkUser->status === true and isset($checkUser->user) === true) {
        switch ($checkUser->user) {
          case 'WO_RASEED_OWNER':
            $user = new Owner();
            break;

          case 'WO_RASEED_SALES_MAN':
            $user = new SalesMan();
            break;

          default:
            self::_showError();
            break;
            exit();
        }
        Redirect::to('./dashboard/sale/');
        $response = (object)[
          'status'      => true,
          'type'        => null,
          'data'        => null
        ];
        $response->data = (object)[
          'user'          => (object)[
            'name'            => $user->getData('name'),
            'cnic'            => $user->getData('cnic'),
            'email'           => $user->getData('email'),
            'contact'         => $user->getData('contact')
          ],
          'store'     => (object)[
            'name'            => $user->getStoreData('name')
          ],
          'report'    => $user->getMonthlyReport()
        ];
        self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'dashboard.php';
        if (file_exists(self::$tmp_file) === true) {
          self::setView(self::$tmp_file, $response);
        } else {
          self::_showError();
        }
      } else {
        self::_showError();
      }
    } else {
      self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner' . DS . 'dashboard.php';
      if (file_exists(self::$tmp_file) === true) {
        require_once self::$tmp_file;
      } else {
        die(self::$_dem);
      }
    }
  }

  public static function item($method = null, $action = null)
  {
    if (is_null($method) === true) {
      $method = 'index';
    }
    self::$tmp_file = ROOT_PATH . 'app/controllers/subs/con_item.php';
    if (file_exists(self::$tmp_file) === true) {
      require_once self::$tmp_file;
      if (class_exists('con_item') === true and method_exists('con_item', strtolower($method)) === false) {
        $method = 'index';
      }
      if (class_exists('con_item') === true and method_exists('con_item', strtolower($method))) {
        call_user_func_array(['con_item', strtolower($method)], [$action]);
      } else {
        self::_showError();
      }
    } else {
      self::_showError();
    }
  }

  public static function purchase($method = null, $action = null)
  {
    if (is_null($method) === true) {
      $method = 'index';
    }
    $tmpController = 'con_purchase';
    self::$tmp_file = ROOT_PATH . 'app/controllers/subs/' . $tmpController . '.php';
    if (file_exists(self::$tmp_file) === true) {
      require_once self::$tmp_file;
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method)) === false) {
        $method = 'index';
      }
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method))) {
        call_user_func_array([$tmpController, strtolower($method)], [$action]);
      } else {
        self::_showError();
      }
    } else {
      self::_showError();
    }
  }

  public static function sale($method = null, $action = null)
  {
    if (is_null($method) === true) {
      $method = 'index';
    }
    $tmpController = 'con_sale';
    self::$tmp_file = ROOT_PATH . 'app/controllers/subs/' . $tmpController . '.php';
    if (file_exists(self::$tmp_file) === true) {
      require_once self::$tmp_file;
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method)) === false) {
        $method = 'index';
      }
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method))) {
        call_user_func_array([$tmpController, strtolower($method)], [$action]);
      } else {
        self::_showError();
      }
    } else {
      self::_showError();
    }
  }

  public static function saleCounter($method = null, $action = null)
  {
    if (is_null($method) === true) {
      $method = 'index';
    }
    $tmpController = 'con_sale_counter';
    self::$tmp_file = ROOT_PATH . 'app/controllers/subs/' . $tmpController . '.php';
    if (file_exists(self::$tmp_file) === true) {
      require_once self::$tmp_file;
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method)) === false) {
        $method = 'index';
      }
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method))) {
        call_user_func_array([$tmpController, strtolower($method)], [$action]);
      } else {
        self::_showError();
      }
    } else {
      self::_showError();
    }
  }

  public static function invoice($method = null, $action = null)
  {
    if (is_null($method) === true) {
      $method = 'index';
    }
    $tmpController = 'con_invoice';
    self::$tmp_file = ROOT_PATH . 'app/controllers/subs/' . $tmpController . '.php';
    if (file_exists(self::$tmp_file) === true) {
      require_once self::$tmp_file;
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method)) === false) {
        $method = 'index';
      }
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method))) {
        call_user_func_array([$tmpController, strtolower($method)], [$action]);
      } else {
        self::_showError();
      }
    } else {
      self::_showError();
    }
  }

  public static function stockBook($method = null, $action = null)
  {
    if (is_null($method) === true) {
      $method = 'index';
    }
    $tmpController = 'con_stockBook';
    self::$tmp_file = ROOT_PATH . 'app/controllers/subs/' . $tmpController . '.php';
    if (file_exists(self::$tmp_file) === true) {
      require_once self::$tmp_file;
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method)) === false) {
        $method = 'index';
      }
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method))) {
        call_user_func_array([$tmpController, strtolower($method)], [$action]);
      } else {
        self::_showError();
      }
    } else {
      self::_showError();
    }
  }

  public static function cashBook($method = null, $action = null)
  {
    if (is_null($method) === true) {
      $method = 'index';
    }
    $tmpController = 'con_cashBook';
    self::$tmp_file = ROOT_PATH . 'app/controllers/subs/' . $tmpController . '.php';
    if (file_exists(self::$tmp_file) === true) {
      require_once self::$tmp_file;
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method)) === false) {
        $method = 'index';
      }
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method))) {
        call_user_func_array([$tmpController, strtolower($method)], [$action]);
      } else {
        self::_showError();
      }
    } else {
      self::_showError();
    }
  }

  public static function dues($method = null, $action = null)
  {
    if (is_null($method) === true) {
      $method = 'index';
    }
    $tmpController = 'con_dues';
    self::$tmp_file = ROOT_PATH . 'app/controllers/subs/' . $tmpController . '.php';
    if (file_exists(self::$tmp_file) === true) {
      require_once self::$tmp_file;
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method)) === false) {
        $method = 'index';
      }
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method))) {
        call_user_func_array([$tmpController, strtolower($method)], [$action]);
      } else {
        self::_showError();
      }
    } else {
      self::_showError();
    }
  }

  public static function charts($method = null, $action = null)
  {
    if (is_null($method) === true) {
      $method = 'index';
    }
    $tmpController = 'con_chart';
    self::$tmp_file = ROOT_PATH . 'app/controllers/subs/' . $tmpController . '.php';
    if (file_exists(self::$tmp_file) === true) {
      require_once self::$tmp_file;
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method)) === false) {
        $method = 'index';
      }
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method))) {
        call_user_func_array([$tmpController, strtolower($method)], [$action]);
      } else {
        self::_showError();
      }
    } else {
      self::_showError();
    }
  }

  public static function buyer($method = null, $action = null)
  {
    if (is_null($method) === true) {
      $method = 'index';
    }
    $tmpController = 'con_buyer';
    self::$tmp_file = ROOT_PATH . 'app/controllers/subs/' . $tmpController . '.php';
    if (file_exists(self::$tmp_file) === true) {
      require_once self::$tmp_file;
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method)) === false) {
        $method = 'index';
      }
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method))) {
        call_user_func_array([$tmpController, strtolower($method)], [$action]);
      } else {
        self::_showError();
      }
    } else {
      self::_showError();
    }
  }

  public static function supplier($method = null, $action = null)
  {
    if (is_null($method) === true) {
      $method = 'index';
    }
    $tmpController = 'con_supplier';
    self::$tmp_file = ROOT_PATH . 'app/controllers/subs/' . $tmpController . '.php';
    if (file_exists(self::$tmp_file) === true) {
      require_once self::$tmp_file;
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method)) === false) {
        $method = 'index';
      }
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method))) {
        call_user_func_array([$tmpController, strtolower($method)], [$action]);
      } else {
        self::_showError();
      }
    } else {
      self::_showError();
    }
  }

  public static function expense($method = null, $action = null)
  {
    if (is_null($method) === true) {
      $method = 'index';
    }
    $tmpController = 'con_expense';
    self::$tmp_file = ROOT_PATH . 'app/controllers/subs/' . $tmpController . '.php';
    if (file_exists(self::$tmp_file) === true) {
      require_once self::$tmp_file;
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method)) === false) {
        $method = 'index';
      }
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method))) {
        call_user_func_array([$tmpController, strtolower($method)], [$action]);
      } else {
        self::_showError();
      }
    } else {
      self::_showError();
    }
  }

  public static function report($method = null, $action = null)
  {
    if (is_null($method) === true) {
      $method = 'index';
    }
    $tmpController = 'con_report';
    self::$tmp_file = ROOT_PATH . 'app/controllers/subs/' . $tmpController . '.php';
    if (file_exists(self::$tmp_file) === true) {
      require_once self::$tmp_file;
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method)) === false) {
        $method = 'index';
      }
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method))) {
        call_user_func_array([$tmpController, strtolower($method)], [$action]);
      } else {
        self::_showError();
      }
    } else {
      self::_showError();
    }
  }

  public static function ordersQueue($method = null, $action = null)
  {
    if (is_null($method) === true) {
      $method = 'index';
    }
    $tmpController = 'con_ordersQueue';
    self::$tmp_file = ROOT_PATH . 'app/controllers/subs/' . $tmpController . '.php';
    if (file_exists(self::$tmp_file) === true) {
      require_once self::$tmp_file;
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method)) === false) {
        $method = 'index';
      }
      if (class_exists($tmpController) === true and method_exists($tmpController, strtolower($method))) {
        call_user_func_array([$tmpController, strtolower($method)], [$action]);
      } else {
        self::_showError();
      }
    } else {
      self::_showError();
    }
  }








  // public static function profile($method = null){
  //   if(is_null($method) === true){
  //     $method = 'index';
  //   }
  //   self::$tmp_file = ROOT_PATH.'app/controllers/subs/profile.php';
  //   if(file_exists(self::$tmp_file) === true){
  //     require_once self::$tmp_file;
  //     if(class_exists('Profile') === true AND method_exists('Profile', strtolower($method)) === false){
  //       $method = 'index';
  //     }
  //     if(class_exists('Profile') === true AND method_exists('Profile', strtolower($method))){
  //       call_user_func_array(['Profile', strtolower($method)], []);
  //     } else {
  //       self::_showError();
  //     }
  //   } else {
  //     self::_showError();
  //   }
  // }

  // public static function school($method = null, $action = null){
  //   if(is_null($method) === true){
  //     $method = 'index';
  //   }
  //   self::$tmp_file = ROOT_PATH.'app/controllers/subs/school.php';
  //   if(file_exists(self::$tmp_file) === true){
  //     require_once self::$tmp_file;
  //     if(class_exists('School') === true AND method_exists('School', strtolower($method)) === false){
  //       $method = 'index';
  //     }
  //     if(class_exists('School') === true AND method_exists('School', strtolower($method))){
  //       call_user_func_array(['School', strtolower($method)], [$action]);
  //     } else {
  //       self::_showError();
  //     }
  //   } else {
  //     self::_showError();
  //   }
  // }

  // public static function campus($method = null, $action = null){
  //   if(is_null($method) === true){
  //     $method = 'index';
  //   }
  //   self::$tmp_file = ROOT_PATH.'app/controllers/subs/con_campus.php';
  //   if(file_exists(self::$tmp_file) === true){
  //     require_once self::$tmp_file;
  //     if(class_exists('con_campus') === true AND method_exists('con_campus', strtolower($method)) === false){
  //       $method = 'index';
  //     }
  //     if(class_exists('con_campus') === true AND method_exists('con_campus', strtolower($method))){
  //       call_user_func_array(['con_campus', strtolower($method)], [$action]);
  //     } else {
  //       self::_showError();
  //     }
  //   } else {
  //     self::_showError();
  //   }
  // }

  // public static function faculty($method = null, $action = null){
  //   if(is_null($method) === true){
  //     $method = 'index';
  //   }
  //   self::$tmp_file = ROOT_PATH.'app/controllers/subs/con_faculty.php';
  //   if(file_exists(self::$tmp_file) === true){
  //     require_once self::$tmp_file;
  //     if(class_exists('con_faculty') === true AND method_exists('con_faculty', strtolower($method)) === false){
  //       $method = 'index';
  //     }
  //     if(class_exists('con_faculty') === true AND method_exists('con_faculty', strtolower($method))){
  //       call_user_func_array(['con_faculty', strtolower($method)], [$action]);
  //     } else {
  //       self::_showError();
  //     }
  //   } else {
  //     self::_showError();
  //   }
  // }

  // public static function student($method = null, $action = null){
  //   if(is_null($method) === true){
  //     $method = 'index';
  //   }
  //   self::$tmp_file = ROOT_PATH.'app/controllers/subs/con_student.php';
  //   if(file_exists(self::$tmp_file) === true){
  //     require_once self::$tmp_file;
  //     if(class_exists('con_student') === true AND method_exists('con_student', strtolower($method)) === false){
  //       $method = 'index';
  //     }
  //     if(class_exists('con_student') === true AND method_exists('con_student', strtolower($method))){
  //       call_user_func_array(['con_student', strtolower($method)], [$action]);
  //     } else {
  //       self::_showError();
  //     }
  //   } else {
  //     self::_showError();
  //   }
  // }

  // public static function class($method = null, $action = null){
  //   if(is_null($method) === true){
  //     $method = 'index';
  //   }
  //   self::$tmp_file = ROOT_PATH.'app/controllers/subs/con_class.php';
  //   if(file_exists(self::$tmp_file) === true){
  //     require_once self::$tmp_file;
  //     if(class_exists('con_class') === true AND method_exists('con_class', strtolower($method)) === false){
  //       $method = 'index';
  //     }
  //     if(class_exists('con_class') === true AND method_exists('con_class', strtolower($method))){
  //       call_user_func_array(['con_class', strtolower($method)], [$action]);
  //     } else {
  //       self::_showError();
  //     }
  //   } else {
  //     self::_showError();
  //   }
  // }
}
