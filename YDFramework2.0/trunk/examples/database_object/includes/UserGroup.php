<?php
	
	class UserGroup extends YDDatabaseObject {
	
		function UserGroup() {

			$this->YDDatabaseObject();

			$this->registerDatabase( YDDatabase::getInstance( 'mysql', 'test', 'root', '', 'localhost' ) );
			$this->registerTable( 'users_groups' );
			
			// Fields
			$this->registerNumericKey( 'user_id' );
			$this->registerNumericKey( 'group_id' );			
			$this->registerNumericField( 'joined', true );
			$this->registerNumericField( 'active' );

			// Relations
			$groups = & $this->registerRelation( 'groups', false, 'Group' );
			$groups->setLocalField( 'group_id' );
			$groups->setForeignVar( 'groups' );

			$users = & $this->registerRelation( 'users', false, 'User' );
			$users->setLocalField( 'user_id' );
			$users->setForeignVar( 'users' );
			
		}

	}

?>