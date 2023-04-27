<?php
class Account extends Controller
{

	private static 	$view_dir 	= ROOT_PATH . 'app' . DS . 'views' . DS . 'home' . DS . 'visitor' . DS;

	public static function index($action = null)
	{
		$user = new User();
		if ($user->checkLogin() !== false) {
			Redirect::to('./home/');
			exit();
		}
		self::$tmp_file = self::$view_dir . 'login.php';
		if (file_exists(self::$tmp_file) === true) {
			require_once self::$tmp_file;
		} else {
			self::_showError();
		}
	}

	public static function join($action = null)
	{
		$response = (object)[
			'status'				=> null,
			'message'				=> (object)[
				'type'						=> null,
				'body'						=> null
			],
			'data'					=> null
		];
		$user = new User();
		if ($user->checkLogin() !== false) {
			Redirect::to('home/');
			exit();
		}
		if (Session::exists('errorMessage') === true) {
			$response->message->type = 'ERROR';
			$response->message->body = Session::flash('errorMessage');
		}
		self::$tmp_file = self::$view_dir . 'join.php';
		if (file_exists(self::$tmp_file) === true) {
			self::setView(self::$tmp_file, $response);
		} else {
			self::_showError();
		}
	}

	public static function register($action = null)
	{
		$response = (object)[
			'status'				=> null,
			'message'				=> (object)[
				'type'						=> null,
				'body'						=> null
			],
			'data'					=> null
		];
		$user = new Owner();
		if ($user->isLoggedIn() === true) {
			Redirect::to('./home/');
			exit();
		}
		if (Input::postExists(array('storeName', 'ownerEmail', 'ownerPassword')) === true) {
			$user = new Owner();
			$form = new Form('registerStore');
			$form->validate();
			if ($form->errorStatus() === false) {
				$userRegistration = $user->register($form->getInputs());
				if ($userRegistration !== false) {
					// registered successfully
					if (is_null($userRegistration) === false and is_object($userRegistration) === true) {
						$userRegistration = (object)$userRegistration;
						if (count(get_object_vars($userRegistration)) > 0 and isset($userRegistration->status) === true and isset($userRegistration->type) === true and isset($userRegistration->message) === true) {
							// response is ok
							if ($userRegistration->status === true) {
								switch ($userRegistration->type) {
									case 1:
										// registered and logged in
										Redirect::to('dashboard/', (object)[
											'registration'		=> 'success',
										]);
										exit();
										break;
									case 2:
										// registered, but not logged in
										Redirect::to('account/login/', (object)[
											'registration'		=> 'success',
										]);
										exit();
										break;
									default:
										// some unknown error
										$response->message->type = 'ERROR';
										$response->message->body = 'You might have been registered, but we encountered an unknown error. Try to login or contact us immediately, thanks!';
										break;
								}
							} else {
								// some unknown error occurred
								$response->message->type = 'ERROR';
								$response->message->body = 'You might have been registered, but we encountered an unknown error. Try to login or contact us immediately, thanks!';
							}
						} else {
							// some unknown error occurred
							$response->message->type = 'ERROR';
							$response->message->body = 'You might have been registered, but we encountered an unknown error. Try to login or contact us immediately, thanks!';
						}
					} else {
						$response->message->type = 'ERROR';
						$response->message->body = 'You might have been registered, but we encountered an unknown error. Try to login or contact us immediately, thanks!';
					}
				} else {
					// registration failed
					$response->message->type = 'ERROR';
					$response->message->body = $user->getError();
				}
			} else {
				$response->message->type = 'ERROR';
				$response->message->body = array();
				foreach ($form->getErrors() as $error) {
					array_push($response->message->body, $error);
				}
			}
		} else {
			// All required inputs are not set.
		}
		$response->message->type = 'ERROR';
		$response->message->body = 'Hi';
		self::$tmp_file = self::$view_dir . 'join.php';
		if (file_exists(self::$tmp_file) === true) {
			self::setView(self::$tmp_file, $response);
		} else {
			self::_showError();
		}
	}

	public static function login($action = null)
	{
		$response = (object)[
			'status'			=> null,
			'type'				=> null,
			'message'     => (object)[
				'error'         => array(),
				'success'       => null
			],
			'data'				=> null
		];
		$user = new User();
		$checkLogin = $user->checkLogin();
		if ($checkLogin !== false) {
			Redirect::to('home/');
			exit();
		}
		if (Input::postExists() === true) {
			if (Input::postExists(array('ownerEmail', 'ownerPassword')) === true) {
				// $user = new Owner();
				$login = $user->login(Input::getPost('ownerEmail'), Input::getPost('ownerPassword'));
				if ($login === true) {
					// logged in
					Redirect::to('dashboard/');
				} else {
					// failed to login
					// some unknown error
					self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'home' . DS . 'visitor' . DS . 'login.php';
					if (file_exists(self::$tmp_file) === true) {
						array_push($response->message->error, $user->getError());
						self::setView(self::$tmp_file, $response);
					} else {
						self::_showError();
					}
				}
			} else {
				self::_showError('All required inputs are not set.');
			}
		} else {
			self::$tmp_file = self::$view_dir . 'login.php';
			if (file_exists(self::$tmp_file) === true) {
				require_once self::$tmp_file;
			} else {
				self::_showError();
			}
		}
	}

	public static function logout($action = null)
	{
		$user = new User();
		if ($user->logout() === true) {
			Redirect::to('./home/');
		} else {
			session_destroy();
			Redirect::to('./home/');
		}
	}
}
