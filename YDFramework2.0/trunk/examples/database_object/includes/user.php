<?php
    
    class user extends YDDatabaseObject {
    
        function user() {    

            $this->YDDatabaseObject();

            $this->registerDatabase( $GLOBALS['YDDBOBJ_CONN'] );
            $this->registerTable( 'users' );
            
            // Fields
            $this->registerKey( 'id', true );

            $name = & $this->registerField( 'name' );
            
            $this->registerField( 'email' );
            $this->registerField( 'country' );

            $is_admin = & $this->registerField( 'is_admin' );
            $is_admin->setColumn( 'admin' );
            
            $birthday = & $this->registerField( 'birthday', true );
            $birthday->setCallback( 'getAge' );
            
            $this->registerSelect( 'birth_year', 'YEAR( ' . $this->getTable() . '.' . $birthday->getColumn() . ' )' );
            
            // Relations
            $rel = & $this->registerRelation( 'group', true, 'group', 'user_group' );
            $rel->setCrossLocalKey( 'user_id' );
            $rel->setCrossForeignKey( 'group_id' );
            
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