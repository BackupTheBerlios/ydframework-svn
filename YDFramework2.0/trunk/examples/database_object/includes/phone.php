<?php

    class phone extends YDDatabaseObject {
    
        function phone() {
        
            $this->YDDatabaseObject();
        
            $this->registerDatabase( $GLOBALS['YDDBOBJ_CONN'] );
            $this->registerTable( 'phones' );
        
            // Fields
            $this->registerKey( 'user_id', false );
            $this->registerField( 'number' );
            $this->registerField( 'ord' );
            
            // Relations
            $user = & $this->registerRelation( 'user' );
            $user->setForeignJoin( 'LEFT' );
            
        }
        
    }


?>