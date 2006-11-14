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
	YDInclude( 'YDResult.php' );

	// add translations directory for generic translations
	YDLocale::addDirectory( dirname( __FILE__ ) . '/languages/' );


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

			// init action titles and class titles
			$this->_titles_actions = array();
			$this->_titles_classes = array();

			// init form name
			$this->_form_name = 'permissions';
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
         *  This function returns all permissions
		 *
         *  @returns    An array with all permissions
         */
		function getAllPermissions( $txt = false ){

			$this->resetAll();
			$this->orderBy( 'permission_id', 'desc' );
			$this->findAll();
			
			if ( $txt == false ) return $this->getResults();

			$export = "\nID \tCLASS \t\tACTION \n";
			foreach( $this->getResults() as $res )
				$export .= $res[ 'permission_id'] . "\t" . $res[ 'class'] . "\t" . $res[ 'action'] . "\n";
				
			return $export;			
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
         *  @param     $title	 (Optional) Action title ( custom action description )
         *
         *  @returns   TRUE if added, FALSE is already exists
         */
		function registerAction( $class, $action, $title = null ){

			// check if class and action are registed
			if ( $this->actionIsRegistered( $class, $action ) ) return false;

			// add action to class
			$this->_actions[ $class ][] = $action;

			// add title if defined
			if ( is_string( $title ) ) $this->_titles_actions[ $class ][ $action ] = $title;

			return true;
		}


        /**
         *  This function returns a action description
         *
         *  @param     $class    Class name
         *  @param     $action   Action name
         *
         *  @returns   description STRING
         */
		function getActionTitle( $class, $action ){
		
			// if a custom title was defined, just return it
			if ( isset( $this->_titles_actions[ $class ][ $action ] ) ) return $this->_titles_actions[ $class ][ $action ];

			// return default 
			return t( $class . ' perm ' . $action );
		}


        /**
         *  This function returns a class description
         *
         *  @param     $class    Class name
         *
         *  @returns   description STRING
         */
		function setClassTitle( $class, $title ){

			// add description if not null
			if ( is_string( $title ) ) $this->_titles_classes[ $class ] = $title;
		}


        /**
         *  This function returns a class description
         *
         *  @param     $class    Class name
         *
         *  @returns   description STRING
         */
		function getClassTitle( $class ){
		
			// if a custom title was defined, just return it
			if ( isset( $this->_titles_classes[ $class ] ) ) return $this->_titles_classes[ $class ];

			// return default 
			return t( $class . ' perm' );
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
         *  This function returns all registered actions
         *
         *  @param     $name    Form name
         */
		function setFormName( $name ){

			$this->_form_name = $name;
		}



        /**
         *  This method adds form elements for permission editing
		 *
		 * @param $id           Group id to edit
         *
		 * @returns    YDForm object pointer         
         */
		function & addFormEdit( $id ){

			// store edition id (used later when saving details)
			$this->editing_ID = $id;

		 	return $this->_addFormDetails( $id, true );
		}


        /**
         *  This method adds form elements for addind a new permission system
		 *
		 * @param $id    User id of this new group
         *
		 * @returns    YDForm object pointer         
         */
		function & addFormNew( $id ){

		 	return $this->_addFormDetails( $id, false );
		}


        /**
         *  This function returns an associative array with checkboxgroups
         *
		 * @param $id    	User id (when adding new group), Group id (when editing group)
		 * @param $edit   	We are adding or editing
		 *
		 * @returns    YDForm object pointer         
         */
		function & _addFormDetails( $id, $edit ){

			// init form
			$this->_form = new YDForm( $this->_form_name );

			// if this group is not a root group we must get the parent group permissions to check the ones we can use
			$userobject = new YDCMUserobject();
			$groups     = $userobject->getElements( array( 'ydcmgroup', 'ydcmuser' ) );
			$parent_id  = $groups[ $id ][ 'parent_id' ];

			// when adding a new group, parent of $id is the master group. when editing a group, parent is a user.. so the master group is the parent of the parent
			if ( $edit == true ) $parent_id = $groups[ $parent_id ][ 'parent_id' ];

			// if parent of this group is root, parentgroup permissions are ALL (read: null), otherwise we must get permissions of that parent
			if ( $parent_id == 1 ) $parentgroup_perms = null;
			else                   $parentgroup_perms = $this->getPermissions( $parent_id );

			// if we are editing, we must get the current group permissions
			if ( $edit == true ) $perms = $this->getPermissions( $id );

			// init form defaults array
			$form_defaults = array();

			// get all possible actions to compute checkboxgroups for each class
			foreach( $this->getRegisteredActions() as $class => $actions ){
			
				// get permission translations for each component
				YDLocale::addDirectory( YD_DIR_HOME_ADD . '/YDCMComponent/languages/' . $class );

				// init checkboxgroup options, disabled options and default values
				$chk_options                         = array();
				$chk_disable                         = array();
				$form_defaults[ 'pclass_' . $class ] = array();

				// cycle all actions of this class to get translations and default values
				foreach( $actions as $action ){
				
					// get actions labels
					$chk_options[ $action ] = $this->getActionTitle( $class, $action );
					
					// if parentgroup is the root (id 1) or the parent group has the correspondent action, this action must be set based on current group db values
					if ( is_null( $parentgroup_perms ) || isset( $parentgroup_perms[ $class ][ $action ] ) ){
						
						// if we are editing and the current group has this permission we must check it. if we are adding just uncheck it
						if ( $edit && isset( $perms[ $class ][ $action ] ) ) $form_defaults[ 'pclass_' . $class ][ $action ] = 1;

					// otherwise the action must be unset and disabled (because, if the parent group cannot do something, this group cannot do too)
					}else{
						$form_defaults[ 'pclass_'. $class ][ $action ] = 0;
						$chk_disable[] = $action;
					}
				}

				// add checkboxgroup to form
				$checkboxgroup = & $this->_form->addElement( 'checkboxgroup', 'pclass_'. $class, $this->getClassTitle( $class ), array(), $chk_options );
				$checkboxgroup->addSelectAll( true, array( 'class' => 'ydcmpermission_checkbox_selall' ) );
				$checkboxgroup->setAttribute( 'class', 'ydcmpermission_checkbox' );
				$checkboxgroup->setLabelAttribute( 'class', 'ydcmpermission_checkbox_label' );

				// disable checkboxgroup options that are not valid for this group
				if ( ! empty( $chk_disable ) ) $this->_form->disable( 'pclass_' . $class, $chk_disable );
			}

			// add submit button
			$this->_form->addElement( 'submit', '_cmdSubmit', t( 'save' ) );

			// set form defaults
			$this->_form->setDefaults( $form_defaults );

			return $this->_form;
		}




        /**
         *  This method updates group permissions
         *
         *  @param $group_id     (Optional) Group id to update
         *  @param $formvalues   (Optional) Custom array with user attributes
         *
         *  @returns    INT: total of rows affected
         */
		function saveFormEdit( $group_id = null, $formvalues = null ){
		
			if ( is_null( $group_id ) ) $group_id = $this->editing_ID;

			return $this->_saveFormDetails( $group_id, true, $formvalues );
		}


        /**
         *  This method adds a new permission system
         *
         *  @param $user_id      (Optional) Parent of this permission system. Must be a user id
         *  @param $formvalues   (Optional) Custom array with user attributes
         *
         *  @returns    INT: total of rows affected
         */
		function saveFormNew( $user_id = null, $formvalues = null ){

			return $this->_saveFormDetails( $user_id, false, $formvalues );
		}



        /**
         *  This method adds/saves a permission system
         *
         *  @param $id           If we are editing, $id is the group id. If we are adding, $id is the user id
         *  @param $edit         Boolean flag that defines if we are editing $id or adding to $id
         *  @param $formvalues   (Optional) Custom array with user attributes
         *
         *  @returns    INT: total of rows affected
         */
		function _saveFormDetails( $id, $edit, $formvalues = null ){

			// validate with custom values
			if ( ! is_null( $formvalues ) ) $this->_form->validate( $formvalues );

			// get form values EXCLUDING spans
			$values = $this->_form->getValues();

			// if this group is not a root group we must get the parent group permissions to check the ones we can use
			$userobject = new YDCMUserobject();
			$groups     = $userobject->getElements( array( 'ydcmgroup', 'ydcmuser' ) );
			$parent_id  = $groups[ $id ][ 'parent_id' ];

			// when adding a new group, parent of $id is the master group. when editing a group, parent is a user.. so the master group is the parent of the parent
			if ( $edit == true ) $parent_id = $groups[ $parent_id ][ 'parent_id' ];

			// if parent of this group is root, parentgroup permissions are ALL (read: null), otherwise we must get permissions of that parent
			if ( $parent_id == 1 ) $parentgroup_perms = null;
			else                   $parentgroup_perms = $this->getPermissions( $parent_id );

			// if we are editing, we must get the current group permissions. if we are adding, current permissions are empty
			if ( $edit == true ) $perms = $this->getPermissions( $id );
			else                 $perms = array();

			$actions_to_add = array();
			$actions_to_del = array();

			// get all possible actions to compute actions we must add and actions we must delete
			foreach( $this->getRegisteredActions() as $class => $actions ){
				foreach( $actions as $action ){

					// if action is selected by the user AND
					// this is a root group OR the action belogs to the parent group
					// we can add it
					if ( isset( $values[ 'pclass_' . $class ][ $action ] ) && ( $values[ 'pclass_' . $class ][ $action ] == 1 || $values[ 'pclass_' . $class ][ $action ] == 'on' ) ){


						// check if action is valid:
						// if parent group is a root a group OR the parent group has this action
						if ( is_null( $parentgroup_perms ) || isset( $parentgroup_perms[ $class ][ $action ] ) ){

							// if action is valid we must check if we must add it or the user already has it
							if ( ! isset( $perms[ $class ][ $action ] ) ) $actions_to_add[] = array( $class, $action );
							
							continue;
						}

						// if action selected is not valid we must delete it
						$actions_to_del[] = array( $class, $action );
					}
					
					// if action is not set, we will always delete it (even if is not in bd)
					$actions_to_del[] = array( $class, $action );
				}
			}


			$rows_deleted = 0;

			// delete actions and count rows affected
			foreach( $actions_to_del as $ac ){
				$this->resetValues();
				$this->set( 'permission_id', $id );
				$this->set( 'class', $ac[0] );
				$this->set( 'action', $ac[1] );
				$rows_deleted += $this->delete();
			}

			$rows_added = 0;

			// add actions and count total of action added
			foreach( $actions_to_add as $ac ){
				$this->resetValues();
				$this->set( 'permission_id', $id );
				$this->set( 'class', $ac[0] );
				$this->set( 'action', $ac[1] );
				$this->insert();
				$rows_added ++;
			}

			// TODO: currently YDDatabaseObject don't have a mechanism to control the above deletes and inserts
			return $rows_deleted + $rows_added;
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