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


	// add permissions lib
	require_once( dirname( __FILE__ ) . '/YDCMPermissions.php' );

	YDInclude( 'YDForm.php' );

	// add local translation directory
	YDLocale::addDirectory( dirname( __FILE__ ) . '/languages/YDCMUsers/' );


    class YDCMUsers extends YDDatabaseObject {
    
        function YDCMUsers() {
        
			// init DB object
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMUsers' );

            // register custom key
            $this->registerKey( 'user_id', true );

			// register custom fields
			$this->registerField( 'parent_id' );
			$this->registerField( 'nleft' );
			$this->registerField( 'nright' );
			$this->registerField( 'nlevel' );
			$this->registerField( 'username' );
			$this->registerField( 'password' );
			$this->registerField( 'name' );
			$this->registerField( 'email' );			
			$this->registerField( 'other' );			
			$this->registerField( 'language_id' );
			$this->registerField( 'state' );
			$this->registerField( 'login_start' );
			$this->registerField( 'login_end' );
			$this->registerField( 'login_counter' );			
			$this->registerField( 'login_last' );
			$this->registerField( 'login_current' );
			$this->registerField( 'created_user' );
			$this->registerField( 'created_date' );
			$this->registerField( 'template' );

			// relation with languages table
            $rel = & $this->registerRelation( 'YDCMLanguages', false, 'YDCMLanguages' );
			$rel->setLocalKey( 'language_id' );
            $rel->setForeignKey( 'language_id' );

			// create tree object
			$this->tree = new YDDatabaseTree( 'default', 'YDCMUsers', 'user_id', 'parent_id', 'parent_id' );

			// add tree fields
			$this->tree->addField( 'parent_id' );
			$this->tree->addField( 'username' );
			$this->tree->addField( 'password' );
			$this->tree->addField( 'name' );
			$this->tree->addField( 'email' );			
			$this->tree->addField( 'other' );			
			$this->tree->addField( 'language_id' );
			$this->tree->addField( 'state' );			
			$this->tree->addField( 'login_start' );
			$this->tree->addField( 'login_end' );
			$this->tree->addField( 'login_counter' );			
			$this->tree->addField( 'login_last' );
			$this->tree->addField( 'login_current' );
			$this->tree->addField( 'type' );
			$this->tree->addField( 'created_user' );
			$this->tree->addField( 'created_date' );
			$this->tree->addField( 'template' );

			// init permissions
			$this->perm = new YDCMPermissions();
			
			// action_name: 'editing', 'adding';
//			$this->_action_name      = null;
//			$this->_action_id        = null;
//			$this->_action_parent_id = null;
		}


        /**
         *  This method returns all sub users of a user
         *
         *  @param  $parent_id   Parent id
         *
         *  @returns    Array with user and children nodes
         */
		function getTreeElements( $parent_id ){

			return $this->tree->getDescendants( $parent_id, true );
		}


        /**
         *  This method checks if a user is descendant of another user
         *
         *  @param      $id          User id to test if id descendant
         *  @param      $parent_id   Parent id
         *
         *  @returns    boolean. TRUE if is descendant, FALSE if not descendant
         */
		function isDescendantOf( $user_id, $parent_id ){

			return $this->tree->isDescendantOf( intval( $user_id ), intval( $parent_id ) );
		}


        /**
         *  This method checks if a username and password are valid
         *
         *  @param $username  User username
         *  @param $password  User password (if password length is smaller than 32 we must md5 it, password form elements must have max 31)
         *
         *  @returns    true if valid, false otherwise
         */
		function valid( $username = '', $password = '' ){

			// teste username and password rules
			if (!YDValidateRules::alphanumeric(	$username))			return false;
			if (!YDValidateRules::maxlength(	$username, 100))	return false;
			if (!YDValidateRules::alphanumeric(	$password))			return false;
			if (!YDValidateRules::maxlength(	$password, 32))		return false;

			// reset values
			$this->resetValues();

			// set username
			$this->username = $username;

			// if password is not in md5 format we must set it
			if ( strlen( $password ) != 32 ) $password = md5( $password );

			// set password
			$this->password = $password;
			
			// check user state based on state and/or user login schedule
			$now = YDStringUtil::formatDate( time(), 'datetimesql' );

			$this->where( '(state = 1 OR (state = 2 AND login_start < "' . $now . '" AND login_end > "' . $now . '"))' );

			// test if we get just one user
			if ( $this->find() == 1 ) return true;

			return false;
		}


        /**
         *  This method deletes a user (and all children) or just the children
         *
         *  @param $user_id        User id
         *  @param $includeParent  (Optional) Boolean TRUE (default) deletes user id and children, FALSE deletes children only
         *
         *  @returns    TRUE if deleted, ARRAY with form errors otherwise
         */
		function deleteUser( $user_id, $includeParent = true ){
		
			// get ids to delete
			$ids = $this->tree->getDescendants( intval( $user_id ), $includeParent, false, null, null, 'user_id' );

			// delete all permissions of this users
			$this->perm->deletePermissions( $ids );

			// delete node from users table
			$this->tree->deleteNode( $user_id, $includeParent );
		}


        /**
         *  This method adds form elements for user editing
		 *
		 * @param $user_id      User id to edit
		 *
		 * @param $username     (Optional) Defines if the username is a box (editable), a span for information only or inexistent
		 *                                 TRUE: is a box (editable); FALSE: is a span; NULL: inexistent
		 * 
		 * @param $password     (Optional) Defines if the password is a box (editable) or inexistent
		 *                                 TRUE: is a box (editable); NULL: inexistent
		 * 
		 * @param $logindetails (Optional) Defines if login details are editable (eg: login schedule dates)
		 *                                 TRUE: login details are editable; FALSE: login details are spans (just information); NULL: login details are inexistent
		 * 
		 * @param $userdetails  (Optional) Defines if user details are editable (eg: name, email, other info, template, language)
		 *                                 TRUE: user details are editable; FALSE: user details are spans (just information); NULL: inexistent
		 *
		 * @param $accessinfo   (Optional) Defines if we want to include access information
		 *                                 TRUE: include information (spans); FALSE or NULL: don't include
		 *
		 * @param $permissionediting  (Optional) Defines if permissions are editable
		 *                                       TRUE: permissions are checkboxgroups; FALSE or NULL: inexistent
         */
		function addFormEdit( $id, $username = null, $password = null, $logindetails = null, $userdetails = null, $accessinfo = null, $permissionediting = null ){

		 	return $this->_addFormDetails( $id, true, $username, $password, $logindetails, $userdetails, $accessinfo, $permissionediting );
		}


        /**
         *  This method adds form elements for addind a new user
		 *
		 * @param $parent_id    Parent id of this new user
         *
		 * @param $username     (Optional) Defines if the username is a box (editable), a span for information only or inexistent
		 *                                 TRUE: is a box (editable); FALSE: is a span; NULL: inexistent
		 * 
		 * @param $password     (Optional) Defines if the password is a box (editable) or inexistent
		 *                                 TRUE: is a box (editable); NULL: inexistent
		 * 
		 * @param $logindetails (Optional) Defines if login details are editable (eg: login schedule dates)
		 *                                 TRUE: login details are editable; FALSE: login details are spans (just information); NULL: login details are inexistent
		 * 
		 * @param $userdetails  (Optional) Defines if user details are editable (eg: name, email, other info, template, language)
		 *                                 TRUE: user details are editable; FALSE: user details are spans (just information); NULL: inexistent
		 *
		 * @param $accessinfo   (Optional) Defines if we want to include access information
		 *                                 TRUE: include information (spans); FALSE or NULL: don't include
		 *
		 * @param $permissionediting  (Optional) Defines if permissions are editable
		 *                                       TRUE: permissions are checkboxgroups; FALSE or NULL: inexistent
         */
		function addFormNew( $id, $username = null, $password = null, $logindetails = null, $userdetails = null, $accessinfo = null, $permissionediting = null ){

		 	return $this->_addFormDetails( $id, false, $username, $password, $logindetails, $userdetails, $accessinfo, $permissionediting );
		}
		 
		
        /**
         *  Helper method for user management
		 *
		 * @param $id           If you will EDIT some user this is the user id to edit. On $edit (next argument) you must set TRUE
         *                      If you will ADD a new user this is the parent id of the new user. On $edit (next argument) you must set FALSE
         *
		 * @param $edit         If you are editing a user set TRUE. If you are adding a new user set FALSE
		 *
		 * @param $username     (Optional) Defines if the username is a box (editable), a span for information only or inexistent
		 *                                 TRUE: is a box (editable); FALSE: is a span; NULL: inexistent
		 * 
		 * @param $password     (Optional) Defines if the password is a box (editable) or inexistent
		 *                                 TRUE: is a box (editable); NULL: inexistent
		 * 
		 * @param $logindetails (Optional) Defines if login details are editable (eg: login schedule dates)
		 *                                 TRUE: login details are editable; FALSE: login details are spans (just information); NULL: login details are inexistent
		 * 
		 * @param $userdetails  (Optional) Defines if user details are editable (eg: name, email, other info, template, language)
		 *                                 TRUE: user details are editable; FALSE: user details are spans (just information); NULL: inexistent
		 *
		 * @param $accessinfo   (Optional) Defines if we want to include access information
		 *                                 TRUE: include information (spans); FALSE or NULL: don't include
		 *
		 * @param $permissionediting  (Optional) Defines if permissions are editable
		 *                                       TRUE: permissions are checkboxgroups; FALSE or NULL: inexistent
         */
		function _addFormDetails( $id, $edit, $username = null, $password = null, $logindetails = null, $userdetails = null, $accessinfo = null, $permissionediting = null ){

			// init form
			$this->form = new YDForm( 'YDCMUsers' );

			// add username
			if ( $username === true )       $this->form->addElement( 'text', 'username', t('user_username') );
			else if ( $username === false ) $this->form->addElement( 'span', 'username', t('user_username') );

			// add password
			if ( $password === true ) $this->form->addElement( 'password', 'password', t('user_password') );

			// add login details
			if ( $logindetails === true ){
				$this->form->addElement( 'select',         'state',       t('login_state'), array(), array(1 => t('yes'), 0 => t('no'), 2 => t('schedule')) );
				$this->form->addElement( 'datetimeselect', 'login_start', t('login_start') );
				$this->form->addElement( 'datetimeselect', 'login_end',   t('login_end'));

			}else if ( $logindetails === false ){
				$this->form->addElement( 'span',           'state',       t('login_state') );
				$this->form->addElement( 'span',           'login_start', t('login_start') );
				$this->form->addElement( 'span',           'login_end',   t('login_end') );
			}

			// add common user details
            if ( $userdetails === true ){
				$this->form->addElement( 'text',      'name',          t('user_name'),     array('size' => 50, 'maxlength' => 255) );
				$this->form->addElement( 'text',      'email',         t('user_email') );
				$this->form->addElement( 'textarea',  'other',         t('user_other'),    array('rows' => 4, 'cols' => 30) );

				$languages = YDCMComponent::module( 'YDCMLanguages' );
				$languages = $languages->active();
				$this->form->addElement( 'select',    'language_id',   t('user_language'),      array(), $languages );

				$templates = YDCMComponent::module( 'YDCMTemplates' );
    	        $this->form->addElement( 'select',    'template',      t('user_template'),      array(), $templates->admin_templates() );

				$this->form->addRule( 'name',        'maxlength',      t('name too big'),  255 );
				$this->form->addRule( 'email',       'email',          t('email not valid') );
				$this->form->addRule( 'other',       'maxlength',      t('other too big'), 5000 );
				$this->form->addRule( 'language_id', 'in_array',       t('language not valid'), array_keys( $languages ) );
				$this->form->addRule( 'template',    'in_array',       t('template not valid'), array_keys( $templates->admin_templates() ) );

			}else if ( $userdetails === false ){
				$this->form->addElement( 'span',      'name',          t('user_name') );
				$this->form->addElement( 'span',      'email',         t('user_email') );
				$this->form->addElement( 'span',      'other',         t('user_other') );
				$this->form->addElement( 'span',      'language_id',   t('user_language') );
				$this->form->addElement( 'span',      'template',      t('user_template') );
			}

			// add access information
			if ( $accessinfo === true ){
				$this->form->addElement( 'span', 'login_counter', t('login_counter') );
    	        $this->form->addElement( 'span', 'login_last',    t('login_last') );
    	        $this->form->addElement( 'span', 'login_current', t('login_current') );
    	        $this->form->addElement( 'span', 'created_user',  t('created_user') );
    	        $this->form->addElement( 'span', 'created_date',  t('created_date') );
			}

			// add submit button
			if ( $edit ) $this->form->addElement( 'submit', '_cmdSubmit', t( 'save' ) );
			else         $this->form->addElement( 'submit', '_cmdSubmit', t( 'add' ) );

			// if we are editing a user it's a good idea to set form defaults with user node attributes
			if ( $edit ){

				// get user attributes
				$node = $this->getUser( $id );

				// set form defaults based on user attributes
				$this->form->setDefaults( $node );
			}


			// PERMISSION CHECKBOXGROUPS
			if ( $permissionediting !== true ) return;

			// check if we are editing or creating
			if ( $edit ){

				// on editing, permissions avaiable are the parent permissions ( parent of $id )
				$permissions_avaiable = $this->perm->getPermissions( $node['parent_id'] );

				// get current user permissions. We will need them to compare with parent permissions
				$permissions = $node[ 'permissions' ];

			// on adding, permissions avaiable are the parent permissions ( $id permissions )
			}else{
				$permissions_avaiable = $this->perm->getPermissions( $id );
			}

			// cycle parent permissions to create form checkboxgroup for this user
			foreach( $permissions_avaiable as $obj => $perm ){

				// get permission translations of this component
				YDLocale::addDirectory( YD_DIR_HOME_ADD . '/' . $obj . '/languages/' );

				// init checkboxgroup options
				$options  = array();
				$selected = array();

				// cycle all permissions of this object to create checkboxgroup options array
				foreach( $perm as $p ){
				
					// add option with translated string
					// eg, if we are in 'YDCMPage' object and action is 'delete' we will add ( YDCMPage => array( 'delete' => t('YDCMPage_delete') ) )
					$options[ $p['object_action' ] ] = t( $obj . '_' . $p['object_action' ] );

					// on editing we must check if this action is select of not ( checking if this action is in parent actions)
					if ( $edit && isset( $permissions[ $obj ] ) && in_array( $p['object_action' ], array_keys( $permissions[ $obj ] ) )){
						$selected[ $p['object_action' ] ] = 1;
					}
				}

				// add object checkbox or spans
				$this->form->addElement( 'checkboxgroup', $obj, $obj, array(), $options );
				
				// add default for this checkboxgroup if we are editing
				if ( $edit ) $this->form->setDefault( $obj, $selected );

				// this will store the checkboxgroups in a variable for better templating placement
				$el = & $this->form->getElement( $obj );
				$this->permissions_html[ $obj ][] = $el->toHtml();
			}

		}


        /**
         *  This method updates user attributes
         *
         *  @param $id           User id to save attributes
         *  @param $created_user User id that wants to create this new user
         *  @param $formvalues   (Optional) Custom array with user attributes
         *
         *  @returns    TRUE if updated, ARRAY with form errors otherwise
         */
		function saveFormEdit( $id, $created_user, $formvalues = null ){

			return $this->_saveFormDetails( $id, true, $created_user, $formvalues );
		}


        /**
         *  This method adds a new user
         *
         *  @param $parent_id    Parent id of this new user
         *  @param $created_user User id that wants to create this new user
         *  @param $formvalues   (Optional) Custom array with user attributes
         *
         *  @returns    TRUE if added, ARRAY with form errors otherwise
         */
		function saveFormNew( $parent_id, $created_user, $formvalues = null ){

			return $this->_saveFormDetails( $parent_id, false, $created_user, $formvalues );
		}


        /**
         *  This method adds/saves a user
         *
         *  @param $id           If we are editing $id is the user id. If we are adding, $id is the parent_id
         *  @param $edit         Boolean flag that defines we we are editing $id or adding to $id
         *  @param $created_user User id that wants to create this new user
         *  @param $formvalues   (Optional) Custom array with user attributes
         *
         *  @returns    TRUE if updated, ARRAY with form errors otherwise
         */
		function _saveFormDetails( $id, $edit, $created_user, $formvalues = null ){

			// check form validation
			if ( !$this->form->validate( $formvalues ) )
				return $this->form->getErrors();

			// get form values avaiable
			$values = $this->form->getValues();
		
			// parse fixed values
			$values['type']          = 'YDCMUseradministrator';
			$values['nlevel']        = 1;
			$values['created_user']  = intval( $created_user );
			$values['created_date']  = YDStringUtil::formatDate( time(), 'datetimesql' );
			$values['login_counter'] = 0;

			// parse special elements
			if ( $this->form->isElement( 'login_start' ) ) $values['login_start'] = YDStringUtil::formatDate( $values['login_start'], 'datetimesql' );
			if ( $this->form->isElement( 'login_start' ) ) $values['login_end']   = YDStringUtil::formatDate( $values['login_end'],   'datetimesql' );
			if ( $this->form->isElement( 'password' ) )    $values['password']    = md5( $values['password'] );

			// check if we are editing or adding an element
			if ( $edit ){

				// update node to YDUsers table
				$res = $this->tree->updateNode( $values, $id );
	
				// change user permissions in YDCMPermissions
				return $this->perm->setPermissions( $id, $this->getUserAttribute( $id, 'parent_id' ), $values );
			}else{
			
				// add node to YDUsers table
				$newID = $this->tree->addNode( $values, $id );

				// change user permissions in YDCMPermissions
				return $this->perm->setPermissions( $newID, $id, $values );
			}

		}


        /**
         *  This function adds form elements for password changing
         *
         * @param $oldpassword  (Optional) Flag the defines if we should include a box with old password confirmation.
         *                                 This box is used when the user wants to change its pass and not when we want to change another user pass
         *                                 TRUE: include box; FALSE: don't include
         */
		function addFormPassword( $oldpassword = true ){

			// init form
			$this->form = new YDForm( 'YDCMUsers' );

			// add new password box
            $this->form->addElement( 'password',    'new',          t('password_new'),         array('size' => 30, 'maxlength' => 31) );
			$this->form->addRule(    'new',         'required',     t('passwords are required') );
			$this->form->addRule(    'new',         'maxlength',    t('passwords too big'), 31 );
			$this->form->addRule(    'new',         'alphanumeric', t('passwords not alphanumeric') );

			// add new_confirm password box
            $this->form->addElement( 'password',    'new_confirm',  t('password_new_confirm'), array('size' => 30, 'maxlength' => 31) );
			$this->form->addRule(    'new_confirm', 'required',     t('passwords are required') );
			$this->form->addRule(    'new_confirm', 'maxlength',    t('passwords too big'), 31 );
			$this->form->addRule(    'new_confirm', 'alphanumeric', t('passwords not alphanumeric') );

			// add compare rule to new password and confirmation password
			$this->form->addCompareRule( array( 'new', 'new_confirm' ), 'equal', t('passwords dont match') );

			// add old password box
			if ( $oldpassword ){
            	$this->form->addElement( 'password', 'old',          t('password_old'),         array('size' => 20, 'maxlength' => 31) );
				$this->form->addRule(    'old',      'required',     t('passwords are required') );
				$this->form->addRule(    'old',      'maxlength',    t('passwords too big'), 31 );
				$this->form->addRule(    'old',      'alphanumeric', t('passwords not alphanumeric') );
			}
		}


        /**
         *  This function updates a user password
         *
         *  @param $user_id     User id to update password
         *  @param $formvalues  (Otional) Custom array with 2 passwords (new and new_confirm)
         *
         *  @returns    true if updated or array with form errors otherwise
         */
		function saveFormPassword( $user_id, $formvalues = null ){

			// check form validation
			if ( !$this->form->validate( $formvalues ) )
				return $this->form->getErrors();

			// reset values
			$this->resetAll();

			// set new password
			$this->user_id  = intval( $user_id );
			$this->password = md5( $this->form->getValue( 'new' ) );

			// update user and get result
			if ( $this->update() == 1 ) return true;

			return false;
		}


        /**
         *  This returns the user form
         *
         *  @param $defaults  (Optional) default values array to apply in form
         *
         *  @returns    YDForm object
         */
		function & getForm( $defaults = false ){

			// check if we have form defaults and apply them
			if ( is_array( $defaults ) ) $this->form->setDefaults( $defaults );

			return $this->form;
		}


        /**
         *  This method returns user attributes
         *
         *  @param      $user_id     User id to use (instead of internal)
         *  @param      $translate   (Optional) Boolean that defines if result must be translated
         *
         *  @returns    User details
         */
		function getUser( $user_id, $translate = false ){

			$this->resetValues();

			// set user id
			$this->user_id = intval( $user_id );

			// get all attributes
			if ( $this->find() != 1 ) return false;

			// password should always empty
			$this->password = '';

			// check if we want human readable elements
			if ( $translate ){

				// check if state not schedule
				if ( $this->state != 2 ){
					$this->login_start = t('not applicable');
					$this->login_end   = t('not applicable');
				}

				if ( $this->state == 0 )      $this->state = t('blocked');
				else if ( $this->state == 1 ) $this->state = t('active');
				else                          $this->state = t('schedule');

				// check if user is root
				if ( $this->created_user == 0 ){
					$this->created_user = t('not applicable');
					$this->created_date = t('not applicable');
				}
			}

			// add permissions
			$values = $this->getValues();
			$values[ 'permissions' ] = $this->perm->getPermissions( intval( $user_id ) );
			
			return $values;
		}


        /**
         *  This method returns a specific user attribute
         *
         *  @param      $user_id     User id to use (instead of internal)
         *  @param      $attribute   Attribute string
         *
         *  @returns    User details
         */
		function getUserAttribute( $user_id, $attribute ){

			$this->resetValues();

			// set user id
			$this->user_id = intval( $user_id );

			// get all attributes
			if ( $this->find() != 1 ) return false;

			return $this->get( $attribute );
		}


        /**
         *  This function updates current user login details
         *
         *  @returns    true if user login details updated, false if user is not valid or details not updated
         */
		function updateLogin(){

			YDInclude( 'YDUtil.php' );

			$this->resetValues();
			
			// get current user id
			$res = $this->currentID();

			// check if user exists
			if ( $res == false ) return false;
			
			// get all attributes
			$this->find();

			// increase login value
			$this->login_counter ++;

			// set last login date
			$this->login_last = $this->login_current;

			// set current login
			$this->login_current = YDStringUtil::formatDate( time(), 'datetimesql' );

			// return update result
			if ( $this->update() == 1 ) return true;
			
			return false;
		}
		
		
        /**
         *  This method returns the id of the current user if you use PHP_AUTH_USER and PHP_AUTH_PW for authentication
         */
		function currentID(){
		
			// reset user values
			$this->resetValues();
		
			// check if user is valid and get user id ( YDCMUsers::valid does it )
			$valid = $this->valid( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] );

			// if user is not valid we should return false
			if ( $valid === false ) return false;

			// return user id found by $this->valid
			return $this->user_id;
		}


    }
?>