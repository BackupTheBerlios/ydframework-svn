<?php

    class phone extends YDDatabaseObject {
    
        function phone() {
        
            $this->YDDatabaseObject();
        
            $this->registerDatabase( $GLOBALS['YDDBOBJ_CONN'] );
            $this->registerTable( 'phones' );
        
            // Fields
            $this->registerNumericKey( 'user_id', false );
            $this->registerStringField( 'number' );
            $this->registerNumericField( 'ord' );
            
            // Relations
            $user = & $this->registerRelation( 'user' );
            $user->setForeignJoin( 'LEFT' );
            
        }
        
    }


?>