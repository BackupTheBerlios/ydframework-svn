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
            $groups = & $this->registerRelation( 'groups', false, 'group' );
            $groups->setLocalField( 'group_id' );
            $groups->setForeignVar( 'groups' );

            $users = & $this->registerRelation( 'users', false, 'user' );
            $users->setLocalField( 'user_id' );
            $users->setForeignVar( 'users' );
            
        }

    }

?>