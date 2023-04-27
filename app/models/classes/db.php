<?php
	class DB {
		private static $_instance = null;
		private $_pdo					= null,
						$_query 			= null,
						$_errorStatus = false,
						$_error 			= null,
						$_results			= null,
						$_count 			= 0;

		public function __construct(){
			try{
				$this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'));
				$this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch(PDOException $e){
				die($e->getMessage());
			}
		}

		public static function getInstance(){
			if(!isset(self::$_instance)){
				self::$_instance = new DB();

			}
			return self::$_instance;
		}

		public function get($fields = null, $table = null, $conditions = null, $params = array()){
			if($fields AND $table){
				$sqlStatement = "SELECT {$fields} FROM {$table}";
				if($conditions AND is_array($params) AND count($params) > 0){
					$sqlStatement .= " WHERE {$conditions}";
					return $this->query(1, $sqlStatement, $params);
				} else {
					return $this->query(1, $sqlStatement);
				}
			}
		}

		public function insert($table, $data){
			if($table AND is_array($data) AND count($data) > 0){
				$sqlStatement = "INSERT INTO `{$table}` (`".implode('`, `', array_keys($data))."`) VALUES (";
				for($i = 0; $i < count($data); $i++){
					$sqlStatement .= "?";
					if($i < count($data)-1){
						$sqlStatement .= ', ';
					}
				}
				$sqlStatement .= ")";
				return $this->query(2, $sqlStatement, $data);
			}
		}

		public function update($table = null, $updateFields = null, $conditionalFields = null, $params = array()){
			if(!is_null($table) AND !is_null($updateFields) AND !is_null($conditionalFields) AND !is_null($params) AND is_array($params)){
				$sqlStatement = "UPDATE `{$table}` SET {$updateFields} WHERE {$conditionalFields}";
				return $this->query(0, $sqlStatement, $params);
			}
		}

		public function delete($table = null, $conditionalFields = null, $params = array()){
			if(!is_null($table) AND !is_null($conditionalFields) AND !is_null($params) AND is_array($params)){
				$sqlStatement = "DELETE FROM `{$table}` WHERE {$conditionalFields}";
				return $this->query(0, $sqlStatement, $params);
			}
		}
		
		
		public function query($type = 0, $sqlStatement = null, $params = array()){
			$this->_errorStatus = true;
			if($sqlStatement){
				if($this->_query = $this->_pdo->prepare($sqlStatement)){
					if(count($params) > 0){
						$x = 1;
						foreach($params as $key => $value) {
					    $this->_query->bindValue($x, $value);
					    $x++;
					  }
					}
					try {
						$this->_query->execute();
						if($type == 1){
							$this->_results 		= $this->_query->fetchAll(PDO::FETCH_OBJ);
						}
						$this->_count 				= $this->_query->rowCount();
						$this->_errorStatus 	= false;
					} catch (Exception $e) {
						$this->setError($e->getMessage());
					}
				}
			} return $this;
		}

		public function setError($error){
			if($error){
				$this->_error = $error;
			}
		}

		public function getError(){
			return $this->_error;
		}

		public function errorStatus(){
			return $this->_errorStatus;
		}

		public function dataCount(){
			return $this->_count;
		}
		
		public function countData(){
			return $this->dataCount();
		}

		public function getResults(){
			return $this->_results;
		}

		public function getFirstResult(){
			return $this->getResults()[0];
		}

	}
