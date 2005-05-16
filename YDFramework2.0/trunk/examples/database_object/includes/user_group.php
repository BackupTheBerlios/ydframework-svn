<?php
    
    class user_group extends YDDatabaseObject {
    
        function user_group() {

            $this->YDDatabaseObject();

            $this->registerDatabase( $GLOBALS['YDDBOBJ_CONN'] );
            $this->registerTable( 'users_groups' );
            
            // Fields
            $this->registerNumericKey( 'user_id' );
            $this->registerNumericKey( 'group_id' );
            $this->registerNumericField( 'joined', true );
            $this->registerNumericField( 'active' );

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