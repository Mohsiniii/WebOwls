<?php
class Home extends Controller
{

	private static 	$root_path 	= 'client',
		$model 			= 'Client';

	public static function index($user = null)
	{
		$user = new User();
		$checkUser = $user->checkLogin();
		$directory = ROOT_PATH . 'app' . DS . 'views' . DS;
		if ($checkUser !== false) {
			if (isset($checkUser->status) === true and $checkUser->status === true and isset($checkUser->user) === true) {
				switch ($checkUser->user) {
					case 'WO_RASEED_OWNER':
						$user = new Owner();
						break;
					default:
						self::_showError();
						break;
				}
			} else {
				self::_showError();
			}
			self::$tmp_file = ROOT_PATH . 'app' . DS . 'views' . DS . 'home' . DS . 'owner' . DS . 'index.php';
			if (file_exists(self::$tmp_file) === true) {
				self::setView(self::$tmp_file, (object)[
					'status'			=> true,
					'type'				=> null,
					'data'				=> (object)[
						'user'          => (object)[
							'name'            => $user->getData('name'),
							'cnic'            => $user->getData('cnic'),
							'email'           => $user->getData('email'),
							'contact'         => $user->getData('contact')
						],
						'store'        => (object)[
							'name'            => $user->getStoreData('name')
						]
					]
				]);
			} else {
				self::_showError();
			}
		} else {
			$directory .= 'home' . DS . 'visitor' . DS;
			$file = 'index.php';
		}
		self::$tmp_file = $directory . $file;

		if (file_exists(self::$tmp_file) === true) {
			require_once self::$tmp_file;
		} else {
			die(self::$_dem);
		}
	}
}
