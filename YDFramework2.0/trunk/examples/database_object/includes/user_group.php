<?php
    
    class user_group extends YDDatabaseObject {
    
        function user_group() {

            $this->YDDatabaseObject();

            $this->registerDatabase( $GLOBALS['YDDBOBJ_CONN'] );
            $this->registerTable( 'users_groups' );
            
            // Fields
            $this->registerKey( 'user_id' );
            $this->registerKey( 'group_id' );
            $this->registerField( 'joined', true );
            $this->registerField( 'active' );

            // Relations
            $rel = & $this->registerRelation( 'groups', false, 'group' );
            $rel->setLocalField( 'group_id' );
            $rel->setForeignVar( 'groups' );

            $rel = & $this->registerRelation( 'users', false, 'user' );
            $rel->setLocalField( 'user_id' );
            $rel->setForeignVar( 'users' );
            
        }

    }

?>