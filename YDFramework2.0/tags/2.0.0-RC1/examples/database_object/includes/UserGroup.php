<?php

YDInclude('YDDatabaseObject.php');

class UserGroup extends YDDatabaseObject {

	function UserGroup() {
		$this->YDDatabaseObject();
	}
	
	function setDatabase() {
		$this->__db = YDDatabase::getInstance(  'mysql', 'test', 'root', '', 'localhost' );
	}
	
	function setTable() {
		$this->__table = 'users_groups';
	}
	
	function setFields() {
		$this->__fields['user_id']  = array( 'type' => YD_DATABASEOBJECT_NUM  );
		$this->__fields['group_id'] = array( 'type' => YD_DATABASEOBJECT_NUM  );
		$this->__fields['joined']   = array( 'type' => YD_DATABASEOBJECT_NUM, 'null' => true );	
		$this->__fields['active']   = array( 'type' => YD_DATABASEOBJECT_NUM );
	}

	function setKeys() {
		$this->__keys = array();
	}
	
	function setRelations() {
		$this->__relations['users']   = array(  'type'  => YD_DATABASEOBJECT_ONETOMANY, 
												'foreign_dataobject' => 'User' );
		$this->__relations['groups']  = array(  'type'  => YD_DATABASEOBJECT_ONETOMANY, 
												'foreign_dataobject' => 'Group' );
	}
	
}


?>