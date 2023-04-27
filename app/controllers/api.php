<?php
	class Api extends Controller {

		private static 	$root_path 	= 'client',
										$model 			= 'Client';

		public static function fetchSivPrice($user = null){
			$user = new User();
      $jsonResponse = (object)[
        'status'    => false,
        'message'   => null,
        'data'      => null
      ];
      $dem = 'Failed to find price.';
      $checkUser = $user->checkLogin();
			if($checkUser !== false){
        // user is logged in, check which one it is
        if(isset($checkUser->status) === true AND $checkUser->status === true AND isset($checkUser->user) === true){
          switch($checkUser->user){
            case 'WO_RASEED_OWNER':
              $user = new Owner();
              if(Input::postExists('sivID') === true){
                $sivPrice = $user->getSivPrice(Input::getPost('sivID'));
                if(is_object($sivPrice) === true){
                  $jsonResponse->status = true;
                  $jsonResponse->message = 'Item price found.';
                  $jsonResponse->data = $sivPrice;
                }
              } else {
                $jsonResponse->status = false;
                $jsonResponse->message = $dem;
              }
            break;
            default:
              $jsonResponse->status = false;
              $jsonResponse->message = $dem;
            break;
          }
        } else {
          $jsonResponse->status = false;
          $jsonResponse->message = $dem;
        }
			} else {
        $jsonResponse->status = false;
        $jsonResponse->message = $dem;
			}
      echo json_encode($jsonResponse);
		}
	}
?>