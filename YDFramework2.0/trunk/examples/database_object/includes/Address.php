<?php

YDInclude('YDDatabaseObject.php');

class Address extends YDDatabaseObject {

	function Address() {
		$this->YDDatabaseObject();
	}
	
	function setDatabase() {
		$this->__db = YDDatabase::getInstance(  'mysql', 'test', 'root', '', 'localhost' );
	}
	
	function setTable() {
		$this->__table = 'address';
	}
	
	function setFields() {
		$this->__fields['user_id'] = array( 'type' => YD_DATABASEOBJECT_NUM );
		$this->__fields['address'] = array( 'type' => YD_DATABASEOBJECT_STR );
		$this->__fields['city']    = array( 'type' => YD_DATABASEOBJECT_STR );
		$this->__fields['state']   = array( 'type' => YD_DATABASEOBJECT_STR );
		$this->__fields['country'] = array( 'type' => YD_DATABASEOBJECT_STR, 'default' => 'Brazil' );
	}

	function setKeys() {
		$this->__keys = array( 'user_id' );		
	}
	
	function setRelations() {
		$this->__relations['user'] = array(  'type'  => YD_DATABASEOBJECT_ONETOONE, 
											 'foreign_type_join'  => 'LEFT',
											 'foreign_dataobject' => 'User' );
	}

}


?>