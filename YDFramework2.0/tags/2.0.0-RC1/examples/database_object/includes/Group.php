<?php

YDInclude('YDDatabaseObject.php');

class Group extends YDDatabaseObject {

	function Group() {
		$this->YDDatabaseObject();
	}
	
	function setDatabase() {
		$this->__db = YDDatabase::getInstance(  'mysql', 'test', 'root', '', 'localhost' );
	}
	
	function setTable() {
		$this->__table = 'groups';
	}
	
	function setFields() {
		$this->__fields['id']      = array( 'type' => YD_DATABASEOBJECT_NUM, 'auto' => true );
		$this->__fields['name']    = array( 'type' => YD_DATABASEOBJECT_STR );
		$this->__fields['updated'] = array( 'type' => YD_DATABASEOBJECT_NUM, 'null' => true );	
		$this->__fields['date']    = array( 'type' => YD_DATABASEOBJECT_NUM, 'null' => true );		
	}

	function setKeys() {
		$this->__keys = array( 'id' );		
	}
	
	function setRelations() {
		$this->__relations['users']  = array(   'type'  => YD_DATABASEOBJECT_MANYTOMANY, 
												'join_local_key'     => 'group_id',
												'join_foreign_key'   => 'user_id',
												'foreign_dataobject' => 'User',
												'join_dataobject'    => 'UserGroup',
												'join_var'           => 'user_group' );
	}
	
}


?>