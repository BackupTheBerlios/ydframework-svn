<?php

YDInclude('YDDatabaseObject.php');

class User extends YDDatabaseObject {

	function User() {	
		$this->YDDatabaseObject();
	}
	
	function setDatabase() {
		$this->__db = YDDatabase::getInstance(  'mysql', 'test', 'root', '', 'localhost' );
	}
	
	function setTable() {
		$this->__table = 'users';
	}
	
	function setFields() {
		$this->__fields['id']         = array( 'type' => YD_DATABASEOBJECT_NUM, 'auto' => true );
		$this->__fields['name']       = array( 'type' => YD_DATABASEOBJECT_STR, 'default' => 'John Doe' );
		$this->__fields['email']      = array( 'type' => YD_DATABASEOBJECT_STR );
		$this->__fields['is_admin']   = array( 'type' => YD_DATABASEOBJECT_NUM, 'column' => 'admin' );	
		$this->__fields['birthday']   = array( 'type' => YD_DATABASEOBJECT_NUM, 'null' => true, 'callback' => 'getAge' );
											   
		$this->registerSelect( 'birth_year', 'YEAR( users.birthday )' );
	}

	function setKeys() {
		$this->__keys = array( 'id' );		
	}
	
	function setRelations() {
		$this->__relations['address'] = array(  'type'  => YD_DATABASEOBJECT_ONETOONE, 
												'foreign_type_join'  => 'LEFT',
												'foreign_dataobject' => 'Address' );

		$this->__relations['groups']  = array(  'type'  => YD_DATABASEOBJECT_MANYTOMANY, 
												'join_local_key'     => 'user_id',
												'join_foreign_key'   => 'group_id',
												'foreign_dataobject' => 'Group',
												'join_dataobject'    => 'UserGroup' );
	}
	
	function getAge( $birthday ) {
	
		if ( ! $birthday ) {
			unset( $this->age );
			return;
		}
		
		$year  = substr( $birthday, 0, 4 );
		
		// not really correct, but just to get the idea
		$this->set( 'age', date('Y') - $year );
		
	}
}


?>