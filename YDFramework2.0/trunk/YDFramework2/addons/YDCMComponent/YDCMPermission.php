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

	// set components path
	YDConfig::set( 'YD_DBOBJECT_PATH', YD_DIR_HOME_ADD . '/YDCMComponent', false );

	// include YDF libs
	YDInclude( 'YDDatabaseObject.php' );



    class YDCMPermission extends YDDatabaseObject {
    
        function YDCMPermission() {

			// init component as non default
            $this->YDDatabaseObject();
			
			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMPermission' );

			// register fields	
			$this->registerField( 'permission_id' );
			$this->registerField( 'class' );
			$this->registerField( 'action' );

			// init relation with userobject
            $rel = & $this->registerRelation( 'ydcmuserobject', false, 'ydcmuserobject' );
			$rel->setLocalKey( 'permission_id' );
            $rel->setForeignKey( 'userobject_id' );

			// init valid actions
			$this->_actions = array();
		}


        /**
         *  This function return an array of permissions
         *
         *  @param     $userobject_id    Userobject id
         *  @param     $translate        (Optional) Translate permissions
		 *
         *  @returns    An array with all permissions
         */
		function getPermissions( $userobject_id, $translate = false ){
		
			$this->resetAll();
			$this->set( 'permission_id', intval( $userobject_id ) );
			$this->findAll();

			$permissions = $this->getResultsAsAssocArray( array( 'class', 'action' ) );

			// filter permissions
			foreach ( $permissions as $class => $actions )
				foreach( $actions as $action => $info )
					if ( ! $this->actionIsRegistered( $class, $action ) ) unset( $permissions[ $class ][ $action ] );

			// cycle all classes to see if there are empty classes. on the above unset some classes could be without actions
			foreach ( $permissions as $class => $actions )
				if ( empty( $actions ) ) unset( $permissions[ $class ] );

			// if we don't want to translated permissions, just return
			if ( !$translate ) return $permissions;

			$arr_translated = array();

			// cycle classes and compute translated actions
			foreach( $permissions as $class => $actions ){

				// get translations of this component
				YDLocale::addDirectory( YD_DIR_HOME_ADD . '/YDCMComponent/languages/' . $class );

				// add class to new array
				if ( !isset( $arr_translated[ $class ] ) ) $arr_translated[] = $class;

				// cycle actions to compute translated string
				foreach( $actions as $action => $info )
					$arr_translated[ $class ][ t( "permission $class $action" ) ] = $info;
			}
			
			return $arr_translated;
		}


        /**
         *  This function returns all permissions of a userobject
         *
         *  @param     $userobject_id   Userobject id (or array of userobjects ) to delete
         *
         *  @returns   Boolean TRUE if deleted, FALSE otherwise
         */
		function deletePermissions( $userobject_id ){

			// check if argument is array
			if ( !is_array( $userobject_id ) ) $userobject_id = array( $userobject_id );

			$this->resetAll();

			// delete all permissions of correspondent userobjects
			$this->where( 'permission_id IN ("' . implode( '","', $userobject_id ) . '")' );

			return ( $this->delete() > 0 );
		}



        /**
         *  This function added a new action to database
         *
         *  @param     $userobject_id   Userobject id
         *  @param     $class           Class name
         *  @param     $action          Action name
         *
         *  @returns   Boolean TRUE if added, FALSE otherwise
         */
		function addPermission( $userobject_id, $class, $action ){

			if ( ! $this->actionIsRegistered( $class, $action ) ) return false;

			// reset old stuff
			$this->resetAll();

			// TODO: check if userobject exists
			$this->set( 'permission_id', intval( $userobject_id ) );
			$this->set( 'class', $class );
			$this->set( 'action', $action );

			// return insert result
			return ( $this->insert() == 1 );
		}


        /**
         *  This function registers a valid system permission
         *
         *  @param     $class    Class name
         *  @param     $action   Action name
         *
         *  @returns   TRUE if added, FALSE is already exists
         */
		function registerAction( $class, $action ){

			$this->_actions[ $class ][] = $action;

			return true;
		}


        /**
         *  This function checks if a class+action is registered
         *
         *  @param     $class    Class name
         *  @param     $action   Action name
         *
         *  @returns   TRUE if registered, FALSE otherwise
         */
		function actionIsRegistered( $class, $action ){

			if ( isset( $this->_actions[ $class ] ) && in_array( $action, $this->_actions[ $class ] ) ) return true;

			return false;
		}


        /**
         *  This function returns all registered actions
         *
         *  @param     $class    (Optional) Class name. default NULL to get actions from all classes
         *
         *  @returns   Associative array:  array( CLASS => array( ACTION, ACTION, .. ) ), or array when class argument is defined
         */
		function getRegisteredActions( $class = null ){

			// check if we want actions from all classes
			if ( is_null( $class ) ) return $this->_actions;
			
			// check if action exists
			if ( isset( $this->_actions[ $class ] ) ) return $this->_actions[ $class ];

			return array();
		}


        /**
         *  This function checks if a userobject if can do some action
         *
         *  @param     $userobject_id     Userobject id
         *  @param     $class             The class name
         *  @param     $action            The action name
         *
         *  @returns   Boolean TRUE or FALSE
         */
		function can( $userobject_id, $class, $action ){

			// check if class and action are registed
			if ( ! isset( $this->_actions[ $class ] ) || ! in_array( $action, $this->_actions[ $class ] ) ) return false;

			$this->resetAll();
			
			// try to get a row joined with userobject
			$this->set( 'permission_id', $userobject_id );
			$this->set( 'class', $class );
			$this->set( 'action', $action );

			return ( $this->find( 'ydcmuserobject' ) == 1 );
		}

}

?>