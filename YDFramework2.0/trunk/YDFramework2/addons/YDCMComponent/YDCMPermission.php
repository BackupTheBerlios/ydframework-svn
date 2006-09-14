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
			
			// init alias
			$this->_actions_alias = array();
			
			// init always-valid actions
			$this->_always_valid = array();
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
					$arr_translated[ $class ][ t( "$class perm $action" ) ] = $info;
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
			$this->where( 'permission_id IN ("' . implode( '","', array_map( "intval", $userobject_id ) ) . '")' );

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

			// check if class and action are registed
			if ( $this->actionIsRegistered( $class, $action ) ) return false;

			// add action to class
			$this->_actions[ $class ][] = $action;

			return true;
		}


        /**
         *  This function registers an always-valid action
         *
         *  @param     $class    Class name
         *  @param     $action   Action name
         *
         *  @returns   TRUE if added, FALSE is already exists
         */
		function registerAlwaysValidAction( $class, $action ){

			// check if class and action are registed
			if ( $this->actionIsRegistered( $class, $action ) ) return false;

			// add action to class
			$this->_always_valid[ $class ][] = $action;

			return true;
		}


        /**
         *  This function returns all always valid actions
         *
         *  @param     $class    (Optional) Class name. default NULL to get alias from all classes
         *
         *  @returns   Associative array:  array( CLASS => array( ACTION => array( ALIAS, ALIAS, .. ) ), or array with actions and correspondent alias when class argument is defined
         */
		function getRegisterAlwaysValidActions( $class = null ){

			// check if we want actions from all classes
			if ( is_null( $class ) ) return $this->_always_valid;
			
			// check if class exists
			if ( isset( $this->_always_valid[ $class ] ) ) return $this->_always_valid[ $class ];

			return array();
		}


        /**
         *  This function registers a alias to a action
         *
         *  @param     $alias    Action alias
         *  @param     $class    Class name
         *  @param     $action   Action name (or another alias name)
         *
         *  @returns   TRUE if added, FALSE if original class or action not valid
         */
		function registerAlias( $alias, $class, $action ){

			// check if class exists
			if ( ! isset( $this->_actions[ $class ] ) ) return false;

			// check if action really exists and belong to the specified class
			if ( in_array( $action, $this->_actions[ $class ] ) ){

				// check if class is already created in alias array
				if ( ! isset( $this->_actions_alias[ $class ] ) ) $this->_actions_alias[ $class ] = array();

				// add alias
				$this->_actions_alias[ $class ][ $action ][] = $alias;
			
				return true;

			}else{

				// check if $action is another alias. if yes, replace $action with original name
				foreach( $this->_actions_alias[ $class ] as $_originalAction => $_alias )
					if ( in_array( $action, $_alias ) ){
				
						// check if class is already created in alias array
						if ( ! isset( $this->_actions_alias[ $class ] ) ) $this->_actions_alias[ $class ] = array();
				
						// add alias
						$this->_actions_alias[ $class ][ $_originalAction ][] = $alias;
					
						return true;
					}

				return false;
				}
		}


        /**
         *  This function returns all registered alias
         *
         *  @param     $class    (Optional) Class name. default NULL to get alias from all classes
         *
         *  @returns   Associative array:  array( CLASS => array( ACTION => array( ALIAS, ALIAS, .. ) ), or array with actions and correspondent alias when class argument is defined
         */
		function getRegisteredAlias( $class = null ){

			// check if we want actions from all classes
			if ( is_null( $class ) ) return $this->_actions_alias;
			
			// check if class exists
			if ( isset( $this->_actions_alias[ $class ] ) ) return $this->_actions_alias[ $class ];

			return array();
		}


        /**
         *  This function checks if a class+action is registered (searching alias too)
         *
         *  @param     $class    Class name
         *  @param     $action   Action name ( or alias name)
         *
         *  @returns   TRUE if registered, FALSE otherwise
         */
		function actionIsRegistered( $class, $action ){

			// check always valid action
			if ( isset( $this->_always_valid[ $class ] ) && in_array( $action, $this->_always_valid ) ) return true;

			// check original action ( searching original actions and alias )
			if ( $this->getOriginalAction( $class, $action ) == false ) return false;

			return true;
		}


        /**
         *  This function checks if a class+action is registered and returns the original action
         *
         *  @param     $class    Class name
         *  @param     $action   Action name ( or alias name)
         *
         *  @returns   STRING name of real action, FALSE otherwise
         */
		function getOriginalAction( $class, $action ){

			// check if is a registered action
			if ( isset( $this->_actions[ $class ] ) && in_array( $action, $this->_actions[ $class ] ) ) return $action;

			// check if is a registered alias
			if ( isset( $this->_actions_alias[ $class ] ) )
				foreach( $this->_actions_alias[ $class ] as $originalAction => $alias )
					if ( in_array( $action, $alias ) ) return $originalAction;

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
			
			// check if class exists
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

			// check if class is an 'always registered'
			if ( isset( $this->_always_valid[ $class ] ) && in_array( $action, $this->_always_valid[ $class ] ) ) return true;

			// check if class and action are registed
			$action = $this->getOriginalAction( $class, $action );

			if ( $action == false ) return false;

			$this->resetAll();

			// try to get a row joined with userobject
			$this->set( 'permission_id', $userobject_id );
			$this->set( 'class', $class );
			$this->set( 'action', $action );

			return ( $this->find( 'ydcmuserobject' ) >= 1 );
		}

}

?>