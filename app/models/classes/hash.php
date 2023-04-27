<?php
	Class Hash {
		public static function make($string, $key = ''){
			return hash('sha256', $string . $key);
		}

		public static function key($length){
			$key = md5(microtime().rand());
			return substr($key, 0, $length);
		}

		public static function unique(){
			return md5(microtime(true).mt_rand());
		}

		public static function rand($length = null){
			if(is_null($length) === true OR (int)$length < 8){
				$length = 8;
			}
			return str_pad(mt_rand(1, 99999999), $length, '0' ,STR_PAD_LEFT);
		}
	}
?>