<?php

    class group extends YDDatabaseObject {
    
        function group() {

            $this->YDDatabaseObject();

            $this->registerDatabase( $GLOBALS['YDDBOBJ_CONN'] );
            $this->registerTable( 'groups' );
            
            // Fields
            $this->registerNumericKey( 'id', true );
            $this->registerStringField( 'name' );
            $this->registerNumericField( 'active' );
            
            // Relations
            $users = & $this->registerRelation( 'user', true, 'user', 'user_group' );
            $users->setCrossLocalField( 'group_id' );
            $users->setCrossForeignField( 'user_id' );
        }
        
    }


?>