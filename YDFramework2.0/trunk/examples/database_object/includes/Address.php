<?php

	class Address extends YDDatabaseObject {
	
		function Address() {
		
			$this->YDDatabaseObject();
		
			$this->registerDatabase( YDDatabase::getInstance( 'mysql', 'test', 'root', '', 'localhost' ) );
			$this->registerTable( 'address' );
		
			// Fields
			$this->registerNumericKey( 'user_id', false );
			
			$this->registerStringField( 'address' );
			$this->registerStringField( 'city' );
			$this->registerStringField( 'state' );
			
			$country = & $this->registerStringField( 'country' );
			$country->setDefault( 'Brazil' );
	
			// Relations
			$user = & $this->registerRelation( 'user', false, 'User' );
			$user->setForeignJoin( 'LEFT' );
			$user->setForeignVar( 'user' );
			
		}
		
	}


?>