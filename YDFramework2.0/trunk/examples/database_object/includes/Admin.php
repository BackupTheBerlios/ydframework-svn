<?php

	require_once 'User.php';
	
	class Admin extends User {
	
		function Admin() {	
			$this->User();
			$this->registerProtected( 'is_admin', 1 );
		}
		
	}


?>