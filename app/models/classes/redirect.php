<?php
	class Redirect {
		public static function to($path = null, $params = null){
			if(is_null($path) === false){
				// $path .= '.php';
				if(is_null($params) === false AND is_object($params) === true AND count(get_object_vars($params)) > 0){
					$path .= '?';
					$params = (object)$params;
					$count = 0;
					foreach($params as $var => $val){
						$count++;
						// die($count.','.count($params));
						$path .= $var.'='.$val;
						$path .= ($count < count(get_object_vars($params))) ? '&':'';
					}
				}
				header('location: '.SERVER_PATH.$path);
			}
		}

		public static function ifUserLoggedIn($user){
			if($user->isUserLoggedIn() === true){
				self::to('index');
			}
		}

		public static function ifAdminNotLoggedIn($admin = null){
			if(is_null($admin) === false){
				if($admin->isUserLoggedIn() === false){
					self::to('login');
				}
			} else {
				self::to('login');
			}
		}

		public static function ifAdminLoggedIn($admin = null){
			if(is_null($admin) === false){
				if($admin->isUserLoggedIn() === true){
					self::to('dashboard');
				}
			}
		}

		public static function ifInstructorNotLoggedIn($instructor = null){
			if(is_null($instructor) === false){
				if($instructor->isUserLoggedIn() === false){
					self::to('login');
				}
			} else {
				self::to('login');
			}
		}

		public static function ifInstructorLoggedIn($instructor = null){
			if(is_null($instructor) === false){
				if($instructor->isUserLoggedIn() === true){
					self::to('dashboard');
				}
			}
		}

		public static function ifStudentLoggedIn($student = null){
			if(is_null($student) === false){
				if($student->isStudentLoggedIn() === true){
					self::to('index');
				}
			}
		}

		public static function ifStudentNotLoggedIn($student = null){
			if(is_null($student) === false){
				if($student->isStudentLoggedIn() === false){
					self::to('login');
				}
			} else {
				self::to('login');
			}
		}

		public static function ifUserNotLoggedIn($user = null){
			if($user->isUserLoggedIn() !== true){
				self::to('login');
			}
		}

	}
?>