<?php
  class Error {
    private $_error       = null,
            $_errorStatus = false;

    protected function setError($error = null){
      $this->setErrorStatus(true);
			if($error){
				$this->_error = $error;
			} else {
				$this->_error = 'Oops... An unknown error occurred!';
			}
		}

		public function getError(){
			if(!is_null($this->_error)){
				return $this->_error;
			} else {
				return 'Oops... An unknown error occurred!';
			}
		}

		protected function setErrorStatus($errorStatus = true){
			$this->_errorStatus = $errorStatus;
		}

		protected function errorStatus(){
			return $this->_errorStatus;
		}
  }
?>