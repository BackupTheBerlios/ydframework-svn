<?php

require_once 'User.php';

class Admin extends User {

	function Admin() {	
		$this->User();
	}
	
	function setProtected() {
		$this->__protected['is_admin'] = 1;
	}

}


?>