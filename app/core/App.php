<?php
	class App {
		private 		$_dem = 'Oops... An unknown error occurred!';
		protected 	$_controller 	= null,
								$params 			= [],
								$errors 			= array();

		public function __construct($user = null){
			$this->_controller = (object)[
				'file'		=> (object)[
					'found'			=> false
				],
				'class'		=> (object)[
					'found'			=> false,
					'name'			=> 'Home',
					'method'		=> (object)[
						'found'				=> false,
						'name'				=> 'index'
					]
				]
			];
			$url = $this->parseUrl();
			
			// set controller
			if(is_array($url) === true AND count($url) > 0){
				// params passed in $url
				if(isset($url[0]) === true){
					// param $url[0] exists
					if(file_exists(ROOT_PATH.'app'.DS.'controllers'.DS.strtolower($url[0]).'.php') === true){
						// controller file found, include it
						require_once ROOT_PATH.'app'.DS.'controllers'.DS.strtolower($url[0]).'.php';
						$this->_controller->file->found = true;
						if(class_exists(ucfirst($url[0])) === true){
							// controller class found
							$this->_controller->class->found = true;
							$this->_controller->class->name = ucfirst($url[0]);
							unset($url[0]);
						}
					}
				}
				if(isset($url[0]) === true OR isset($url[1]) === true){
					// follow next param
					if(isset($url[0]) === true){
						// class not found yet
						if($this->_controller->file->found === false){
							// controller file not found yet, load default one
							if(file_exists(ROOT_PATH.'app'.DS.'controllers'.DS.strtolower($this->_controller->class->name).'.php') === true){
								// default controller file found, load it
								require_once ROOT_PATH.'app'.DS.'controllers'.DS.strtolower($this->_controller->class->name).'.php';
								$this->_controller->file->found = true;
								if(class_exists(ucfirst($this->_controller->class->name)) === true){
									// default controller class found
									$this->_controller->class->found = true;
								}
							}
						}
					}
					$tmp = (isset($url[0]) === true) ? $url[0]: $url[1];
					if($this->_controller->class->found === true AND method_exists(ucfirst($this->_controller->class->name), strtolower($tmp)) === true){
						// method found inside controller class
						$this->_controller->class->method->found = true;
						$this->_controller->class->method->name = strtolower($tmp);
						if(isset($url[0]) === true){unset($url[0]);}else{unset($url[1]);}
					} else {
						// check and load default method
						if($this->_controller->class->found === true AND method_exists(ucfirst($this->_controller->class->name), strtolower($this->_controller->class->method->name)) === true){
							$this->_controller->class->method->found = true;
						}
					}
				}
			}
			// validate controller
			if(file_exists(ROOT_PATH.'app'.DS.'controllers'.DS.strtolower($this->_controller->class->name).'.php') === true){
				// file found, check either it is already included or not
				if(in_array(ROOT_PATH.'app'.DS.'controllers'.DS.strtolower($this->_controller->class->name).'.php', get_included_files()) === false){
					// not included, include it
					require_once ROOT_PATH.'app'.DS.'controllers'.DS.strtolower($this->_controller->class->name).'.php';
				}
			}
			// set controller class found and method found
			$this->_controller->class->found = (class_exists(ucfirst($this->_controller->class->name)) === true) ? true: false;
			$this->_controller->class->method->found = (method_exists(ucfirst($this->_controller->class->name), strtolower($this->_controller->class->method->name)) === true) ? true: false;
			// check if controller class and method found or not
			if($this->_controller->class->found === true AND $this->_controller->class->method->found == true){
				$this->params = $url ? array_values($url) : [];
				// $user = 'visitor';
				// array_unshift($this->params, $user);
				try {
					call_user_func_array([$this->_controller->class->name, $this->_controller->class->method->name], $this->params);
				} catch (Exception $e){
					die($this->_dem);
				}
			} else {
				// error
				die($this->_dem);
			}
		}
		
		

		public function parseUrl(){
			if(isset($_GET['url'])){
				$url = rtrim($_GET['url'], '/');
				$url = filter_var($url, FILTER_SANITIZE_URL);
				$url = explode('/', $url);
				if(is_array($url) === true AND count($url) > 0){
					$tmpUrl = array();
					foreach($url as $u){
						$tmp = explode('-', $u);
						$new = $tmp[0];
						unset($tmp[0]);
						if(count($tmp) > 0){
							foreach($tmp as $t){
								$new .= ucfirst($t);
							}
						}
						array_push($tmpUrl, $new);
					}
					return $tmpUrl;
				}
				return $url;
			} else {
				return [];
			}
		}
	}
?>