<?php
	class Controller {
		protected static  
      $_dem = 'Oops... An unknown error occurred.',
      $tmp_file 	= null;

		protected function model($model = null) {
			if($model){
				require_once 'app/models/User.php';
				require_once 'app/models/'.$model. '.php';
				return new $model();
			} else {
				return null;
			}
		}

		protected static function setView($view, $response = null){
			if(file_exists($view) === true){
				$actualLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				$appData = (object)[
					'name'					=> (object)[
						'abbr'						=> 'Raseed',
						'accr'						=> 'Raseed',
						'full'						=> 'Raseed'
					],
					'description'		=> (object)[
						'brief'						=> 'An Intelligent Multi-Vendor Solution.',
						'detail'					=> 'An Intelligent Multi-Vendor Solution.'
					],
					'keywords'			=> array('Raseed', 'An Intelligent Multi-Vendor Solution'),
					'generator'			=> 'Web Owls',
					'organization'	=> 'Web Owls',
					'author'				=> 'Mohsin Ahmed'
				];
				require_once $view;
			} else {
				self::_showError();
			}
		}

		protected static function _showError($error = null){
			if(is_null($error) === false){
				die($error);
			} else {
				die('Oops... Try again later!');
			}
		}
	}
?>