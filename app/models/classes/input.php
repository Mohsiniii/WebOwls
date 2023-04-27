<?php
	
	class Input {
		
		public static function getExists($input = null){
			if(!is_null($input)){
				if(isset($_GET[$input]) === true){
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public static function postExists($input = null){
			if(is_null($input) === false){
				if(is_string($input) === true){
					if(isset($_POST[$input]) === true){
						return true;
					} else {
						return false;
					}
				} elseif(is_array($input) === true OR is_object($input) === true){
					if(is_object($input) === true){
						$input = (array)$input;
					}
					if(count($input) > 0){
						foreach($input as $inp){
							if(isset($_POST[$inp]) === true){
								continue;
							} else {
								return false;
							}
						} return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				if(isset($_POST) === true AND count($_POST) > 0){
					return true;
				} else {
					return false;
				}
			}
		}

		public static function fileExists($file = null){
			if(is_null($file) === false){
				 if (isset($_FILES[$file]['name']) === true AND empty($_FILES[$file]['name']) === false){
					 return true;
				 } else {
					 return false;
				 }
			} else {
				return false;
			}
		}
		
		public static function get($var){
			if($var){
				return (isset($_GET[$var])) ? trim($_GET[$var]) : null;
			} else {
				return null;
			}
		}

		public static function getPost($var = null){
			if(is_null($var) === false){
				if(isset($_POST[$var]) === true){
					if(is_array($_POST[$var]) === false){
						return trim($_POST[$var]);
					} else {
						return $_POST[$var];
					}
				} else {
					return null;
				}
			} else {
				return null;
			}
		}

		public static function getPostArrayIndex($array = null, $index = null){
			if(is_null($array) === false AND is_null($index) === false){
				if(self::postExists($array) === true){
					return (isset($_POST[$array][$index])) ? $_POST[$array][$index]: null;
				} else {
					return null;
				}
			} else {
				return null;
			}
		}

		public static function getFile($file = null){
			if(is_null($file) === false){
				if(self::fileExists($file) === true){
					return $_FILES[$file];
				} else {
					return null;
				}
			} else {
				return null;
			}
		}
		
		public static function reset($type = null){
			if($type){
				switch ($type){
					case 'post':
						if(isset($_POST)){
							unset($_POST);
						}
					break;
						
					case 'get':
						if(isset($_GET)){
							unset($_GET);
						}
					break;
						
					default:
						// do nothing
					break;
				}
			}
		}

		static function sanitizeData($var = null){
			if(is_null($var) === true OR empty($var) === true){
				return null;
			} else {
				$sanitizedVar = trim($var);
				$sanitizedVar = htmlspecialchars($sanitizedVar);
				return $sanitizedVar;
			}
		}

		public static function roundMoney($amount = null){
			if(is_null($amount) === false){
				return round($amount, 2, PHP_ROUND_HALF_UP);
			} else {
				return null;
			}
		}

		public static function formatMoney($amount = null){
			if(is_null($amount) === false){
				return number_format(self::roundMoney($amount), 2);
			} else {
				return null;
			}
		}
		
	}

?>