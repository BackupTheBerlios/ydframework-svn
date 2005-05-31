<?php
    
    class user extends YDDatabaseObject {
    
        function user() {    

            $this->YDDatabaseObject();

            $this->registerDatabase( $GLOBALS['YDDBOBJ_CONN'] );
            $this->registerTable( 'users' );
            
            // Fields
            $this->registerKey( 'id', true );

            $name = & $this->registerField( 'name' );
            $name->setDefault( 'John Doe' );
            
            $this->registerField( 'email' );
            $this->registerField( 'country' );

            $is_admin = & $this->registerField( 'is_admin' );
            $is_admin->setColumn( 'admin' );
            
            $birthday = & $this->registerField( 'birthday', true );
            $birthday->setCallback( 'getAge' );
            
            $this->registerSelect( 'birth_year', 'YEAR( ' . $this->getTable() . '.' . $birthday->getColumn() . ' )' );
            
            // Relations
            $rel = & $this->registerRelation( 'group', true, 'group', 'user_group' );
            $rel->setCrossLocalField( 'user_id' );
            $rel->setCrossForeignField( 'group_id' );
            
            $rel = & $this->registerRelation( 'phone', false, 'phone' );
            $rel->setForeignJoin( 'LEFT' );
            
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