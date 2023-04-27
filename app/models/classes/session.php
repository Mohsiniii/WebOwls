<?php
	class Session {
		public static function exists($name){
			return (isset($_SESSION[$name])) ? true : false;
		}

		public static function put($name = null, $value = null){
			if(is_null($name) === false AND is_null($value) === false){
				$_SESSION[$name] = $value;
				return true;
			} else {
				return false;
			}
		}

		public static function get($name){
			return (self::exists($name)) ? $_SESSION[$name] : null;
		}

		public static function delete($name){
			if(self::exists($name))
				unset($_SESSION[$name]);
		}

		public static function destroy(){
			session_destroy();
		}

		public static function flash($name, $string = ''){
			if(self::exists($name)){
				$session = self::get($name);
				self::delete($name);
				return $session;
			} else {
				self::put($name, $string);
			}
		}
	}
?>