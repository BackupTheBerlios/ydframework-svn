<?php
    
    class user extends YDDatabaseObject {
    
        function user() {    

            $this->YDDatabaseObject();

            $this->registerDatabase( $GLOBALS['YDDBOBJ_CONN'] );
            $this->registerTable( 'users' );
            
            // Fields
            $this->registerNumericKey( 'id', true );

            $name = & $this->registerStringField( 'name' );
            $name->setDefault( 'John Doe' );
            
            $this->registerStringField( 'email' );
            $this->registerStringField( 'country' );

            $is_admin = & $this->registerNumericField( 'is_admin' );
            $is_admin->setColumn( 'admin' );
            
            $birthday = & $this->registerNumericField( 'birthday', true );
            $birthday->setCallback( 'getAge' );
            
            $this->registerSelect( 'birth_year', 'YEAR( ' . $this->getTable() . '.' . $birthday->getColumn() . ' )' );
            
            // Relations
            $group = & $this->registerRelation( 'group', true, 'group', 'user_group' );
            $group->setCrossLocalField( 'user_id' );
            $group->setCrossForeignField( 'group_id' );
            
            $phone = & $this->registerRelation( 'phone', false, 'phone' );
            $phone->setForeignJoin( 'LEFT' );
            
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