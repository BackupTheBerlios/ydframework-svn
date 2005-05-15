<?php
	
	class User extends YDDatabaseObject {
	
		function User() {	

			$this->YDDatabaseObject();

			$this->registerDatabase( YDDatabase::getInstance( 'mysql', 'test', 'root', '', 'localhost' ) );
			$this->registerTable( 'users' );
			
			// Fields
			$this->registerNumericKey( 'id', true );		

			$name = & $this->registerStringField( 'name' );
			$name->setDefault( 'John Doe' );
			
			$this->registerStringField( 'email' );

			$is_admin = & $this->registerNumericField( 'is_admin' );
			$is_admin->setColumn( 'admin' );
			
			$birthday = & $this->registerNumericField( 'birthday', true );
			$birthday->setCallback( 'getAge' );
			
			$this->registerSelect( 'birth_year', 'YEAR( users.birthday )' );
			
			// Relations
			$group = & $this->registerRelation( 'group', true, 'Group', 'UserGroup' );
			$group->setCrossLocalField( 'user_id' );
			$group->setCrossForeignField( 'group_id' );
			$group->setForeignVar( 'group' );
			$group->setCrossVar( 'usergroup' );
			
			$address = & $this->registerRelation( 'address', false, 'Address' );
			$address->setForeignJoin( 'LEFT' );
			$address->setForeignVar( 'address' );
			
			
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