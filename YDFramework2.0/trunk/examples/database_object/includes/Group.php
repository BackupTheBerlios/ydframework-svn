<?php

	YDInclude('YDDatabaseObject.php');
	
	class Group extends YDDatabaseObject {
	
		function Group() {

			$this->YDDatabaseObject();

			$this->registerDatabase( YDDatabase::getInstance( 'mysql', 'test', 'root', '', 'localhost' ) );
			$this->registerTable( 'groups' );
			
			// Fields
			$this->registerNumericKey( 'id', true );
			
			$this->registerStringField( 'name' );			
			$this->registerStringField( 'updated', true );			
			$this->registerNumericField( 'date', true );
			
			// Relations
			$users = & $this->registerRelation( 'user', true, 'user', 'usergroup' );
			$users->setCrossLocalField( 'group_id' );
			$users->setCrossForeignField( 'user_id' );
		}
		
	}


?>