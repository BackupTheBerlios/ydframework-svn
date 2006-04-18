<?php

YDInclude( 'YDDatabase.php' ); 

class sqlObject {
	var $db; // database connector
	
	function sqlObject() {
		$this->db = $db = YDDatabase::getInstance( 'mysql', DB_BASE, DB_USER, DB_PASS, DB_HOST ); 
		 // Fetch as an associative array
	    YDConfig::set( 'YD_DB_FETCHTYPE', YD_DB_FETCH_ASSOC );
	}
}



?>
