<?php
	
	YDInclude('YDDatabaseObject.php');
	
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
			$groups = & $this->registerRelation( 'groups', false, 'group' );
			$groups->setLocalField( 'group_id' );

			$users = & $this->registerRelation( 'users', false, 'user' );
			$users->setLocalField( 'user_id' );
			
		}

	}

?>