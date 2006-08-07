<?php

    /*

        Yellow Duck Framework version 2.0
        (c) Copyright 2002-2005 Pieter Claerhout

        This library is free software; you can redistribute it and/or
        modify it under the terms of the GNU Lesser General Public
        License as published by the Free Software Foundation; either
        version 2.1 of the License, or (at your option) any later version.

        This library is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
        Lesser General Public License for more details.

        You should have received a copy of the GNU Lesser General Public
        License along with this library; if not, write to the Free Software
        Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

    */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }


    class YDCMPermissions extends YDDatabaseObject {
    
        function YDCMPermissions() {

			// init component as non default
            $this->YDDatabaseObject();
			
			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMPermissions' );

            // register custom key
            $this->registerKey( 'permission_id', true );

			// register fields	
			$this->registerField( 'object' );
			$this->registerField( 'object_action' );
			$this->registerField( 'object_id' );
			$this->registerField( 'schedule' );
			$this->registerField( 'date_start' );
			$this->registerField( 'date_end' );

			// add static relations
			$this->registerField( 'user_id' );
       		$rel = & $this->registerRelation( 'YDCMPermissions', false, 'YDCMPermissions' );
			$rel->setLocalKey( 'user_id' );
			$rel->setForeignKey( 'user_id' );
		}


        /**
         *  This function return an array of permissions
         *
         *  @param     $user_id   User id to get permissions
		 *
         *  @returns    An array with all permission groupby object
         */
		function getPermissions( $user_id ){
		
			$this->resetValues();
			$this->user_id = intval( $user_id );

			$this->find();

			return $this->getResultsAsAssocArray( array( 'object', 'object_action' ) );
		}


        /**
         *  This function assigns an array of permissions to a user
         *
         *  @param     $user_id              User id to get permissions
         *  @param     $permissions_to_add   Array with permissions to add
         *  @param     $permissions_to_del   Array with permissions to delete
         */
		function setPermissions( $user_id, $permissions_to_add, $permissions_to_del ){

			$p_add = 0;
			$t_add = 0;

			$p_del = 0;
			$t_del = 0;

			// cycle permissions to add and insert them in db
			foreach ( $permissions_to_add as $obj => $perms )
				foreach ( array_values( $perms ) as $p ){

					$this->resetAll();
					$this->user_id        = $user_id;
					$this->object         = $obj;
					$this->object_action  = $p;

					if ($this->insert()) $p_add++;
					$t_add++;
				}

			// cycle permissions to delete and delete them from db
			foreach ( $permissions_to_del as $obj => $perms )
				foreach ( array_values( $perms ) as $p ){

					$this->resetAll();
					$this->user_id        = $user_id;
					$this->object         = $obj;
					$this->object_action  = $p;

					if ($this->delete()) $p_del++;
					$t_del++;
				}

			if ($p_add == $t_add && $p_del == $t_del ) return true;
			
			return false;
		}

}

?>