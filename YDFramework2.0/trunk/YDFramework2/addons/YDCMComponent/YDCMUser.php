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

	// add translations directory
	YDLocale::addDirectory( dirname( __FILE__ ) . '/languages/' );
	YDLocale::addDirectory( dirname( __FILE__ ) . '/languages/YDCMUser/' );

	// add YDF libs needed by this class
	YDInclude( 'YDDatabaseObject.php' );
	YDInclude( 'YDValidateRules.php' );
	YDInclude( 'YDUtil.php' );
	YDInclude( 'YDResult.php' );

	// add YDCM libs
	YDInclude( 'YDCMLanguages.php' );
	YDInclude( 'YDCMTemplates.php' );
	YDInclude( 'YDCMUserobject.php' );


	// user class
    class YDCMUser extends YDDatabaseObject {
    
        function YDCMUser() {
        
			// init DB object
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMUser' );

			// register fields
			$this->registerField( 'user_id' );
			$this->registerField( 'username' );
			$this->registerField( 'password' );
			$this->registerField( 'name' );
			$this->registerField( 'email' );			
			$this->registerField( 'other' );	
			$this->registerField( 'lang_id' );
			$this->registerField( 'login_start' );
			$this->registerField( 'login_end' );
			$this->registerField( 'login_counter' );			
			$this->registerField( 'login_last' );
			$this->registerField( 'login_current' );
			$this->registerField( 'template' );

			// init relation with userobject
            $rel = & $this->registerRelation( 'ydcmuserobject', false, 'ydcmuserobject' );
			$rel->setLocalKey( 'user_id' );
            $rel->setForeignKey( 'userobject_id' );

			// init relation with languages table
            $rel = & $this->registerRelation( 'ydcmlanguages', false, 'ydcmlanguages' );
			$rel->setLocalKey( 'lang_id' );
            $rel->setForeignKey( 'language_id' );
		}


        /**
         *  This method checks if a username and password are valid
         *
         *  @param $username  User username
         *  @param $password  User password (if password length is smaller than 32 we must md5 it, password form elements must have max 31)
         *
         *  @returns    user_id if valid, false otherwise
         */
		function valid( $username = '', $password = '' ){

			// test username and password
			if ( !YDValidateRules::alphanumeric( $username ) )   return false;
			if ( !YDValidateRules::maxlength( $username, 100 ) ) return false;
			if ( !YDValidateRules::alphanumeric( $password ) )   return false;
			if ( !YDValidateRules::maxlength( $password, 32 ) )  return false;

			// reset values
			$this->resetAll();

			// set username
			$this->set( 'username', $username );

			// if password is not in md5 format we must set it
			if ( strlen( $password ) != 32 ) $password = md5( $password );

			// set password
			$this->set( 'password', $password );
			
			// check user state based on state and/or user login schedule
			$now = YDStringUtil::formatDate( time(), 'datetimesql' );

			// relation with userobject
			$this->load( 'ydcmuserobject' );
			$this->where( '(state = 1 OR (state = 2 AND login_start < "' . $now . '" AND login_end > "' . $now . '"))' );

			// test if we get just one user
			if ( $this->find( 'ydcmuserobject' ) == 1 ) return intval( $this->get( 'user_id' ) );

			return false;
		}


        /**
         *  This method checks if a user ID is valid ( will check if user_id is on its 3 tables )
         *
         *  @param $user_id        User id
         *
         *  @returns    TRUE if valid, FALSE otherwise
         */
		function validID( $user_id ){
		
			// reset values
			$this->resetAll();

			// find all rows this user id
			$this->where( 'user_id = ' . intval( $user_id ) );

			// join all tables when finding (to check if user_id really belongs to all 3 tables)
			return ( $this->findAll() == 1 );
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
		 * @param $id           User id to edit
		 * @param $noneditable  (Optional) Array with form element names that are NOT editable (are spans).
         */
		function addFormEdit( $id, $noneditable = array() ){

		 	return $this->_addFormDetails( $id, true, $noneditable );
		}


        /**
         *  This method adds form elements for addind a new user
		 *
		 * @param $id    Group id of this new user
         */
		function addFormNew( $id ){

		 	return $this->_addFormDetails( $id, false );
		}
		 
		
        /**
         *  Helper method for user management
		 *
		 * @param $id           If you will EDIT some user this is the user id to edit. On $edit (next argument) you must set TRUE
         *                      If you will ADD a new user this is the parent id of the new user. On $edit (next argument) you must set FALSE
		 * @param $edit         The edit parameter
         *
		 * @param $noneditable  Array with form element names that are NOT editable
         */
		function _addFormDetails( $id, $edit, $noneditable = array() ){

			YDInclude( 'YDForm.php' );

			// init form
			$this->_form = new YDForm( 'YDCMUsers' );

			// check arguments
			if ( !is_array( $noneditable ) ) $noneditable = array( $noneditable );

			// add username
			if ( in_array( 'username', $noneditable ) ){
				$this->_form->addElement( 'span', 'username', t( 'ydcmuser label username' ) );
			}else{
				$this->_form->addElement( 'text', 'username', t( 'ydcmuser label username' ) );
				$this->_form->addFormRule( array( & $this, '_checkuser' ), array( $edit, $id ) );
			}

			// add password box for new users or editing users
			if ( ! $edit || ( $edit && ! in_array( 'password', $noneditable ) ) )
				$this->_form->addElement( 'password', 'password', t( 'ydcmuser label password' ) );				

			// add state
			if ( in_array( 'state', $noneditable ) ){
				$this->_form->addElement( 'span',           'state',       t( 'ydcmuser label state' ) );
				$this->_form->addElement( 'span',           'login_start', t( 'ydcmuser label login_start' ) );
				$this->_form->addElement( 'span',           'login_end',   t( 'ydcmuser label login_end' ) );
			}else{
				$this->_form->addElement( 'select',         'state',       t( 'ydcmuser label state' ), array(), array(1 => t('yes'), 0 => t('no'), 2 => t('schedule')) );
				$this->_form->addElement( 'datetimeselect', 'login_start', t( 'ydcmuser label login_start' ) );
				$this->_form->addElement( 'datetimeselect', 'login_end',   t( 'ydcmuser label login_end' ) );
			}

			// add common user details
			if ( in_array( 'details', $noneditable ) ){
				$this->_form->addElement( 'span',      'name',          t( 'ydcmuser label name' ) );
				$this->_form->addElement( 'span',      'email',         t( 'ydcmuser label email' ) );
				$this->_form->addElement( 'span',      'other',         t( 'ydcmuser label other' ) );
				$this->_form->addElement( 'span',      'lang_id',       t( 'ydcmuser label language' ) );
				$this->_form->addElement( 'span',      'template',      t( 'ydcmuser label template' ) );
			}else{
				$this->_form->addElement( 'text',      'name',          t( 'ydcmuser label name' ),     array('size' => 50, 'maxlength' => 255) );
				$this->_form->addElement( 'text',      'email',         t( 'ydcmuser label email' ) );
				$this->_form->addElement( 'textarea',  'other',         t( 'ydcmuser label other' ),    array('rows' => 4, 'cols' => 30) );

				$languages = new YDCMLanguages();
				$languages = $languages->active();
				$this->_form->addElement( 'select',    'lang_id',       t( 'ydcmuser label language' ), array(), $languages );

				$templates = new YDCMTemplates();
    	        $this->_form->addElement( 'select',    'template',      t( 'ydcmuser label template' ), array(), $templates->admin_templates() );

				$this->_form->addRule( 'name',        'maxlength',      t( 'ydcmuser mess name too big' ),  255 );
				$this->_form->addRule( 'email',       'email',          t( 'ydcmuser mess email not valid' ) );
				$this->_form->addRule( 'other',       'maxlength',      t( 'ydcmuser mess other too big' ), 5000 );
				$this->_form->addRule( 'language_id', 'in_array',       t( 'ydcmuser mess language not valid' ), array_keys( $languages ) );
				$this->_form->addRule( 'template',    'in_array',       t( 'ydcmuser mess template not valid' ), array_keys( $templates->admin_templates() ) );
			}

			// add access information when editing
			if ( $edit ) {
				$this->_form->addElement( 'span', 'login_counter', t( 'ydcmuser label login_counter' ) );
   	        	$this->_form->addElement( 'span', 'login_last',    t( 'ydcmuser label login_last' ) );
   	        	$this->_form->addElement( 'span', 'login_current', t( 'ydcmuser label login_current' ) );
			}

			// if we are editing a user it's a good idea to set form defaults with user node attributes
			if ( $edit ){

				// add submit button
				$this->_form->addElement( 'submit', '_cmdSubmit', t( 'ydcmuser label save' ) );

				// set form defaults based on user attributes
				$this->_form->setDefaults( $this->getUser( $id ) );

			}else{

				// add submit button
				$this->_form->addElement( 'submit', '_cmdSubmit', t( 'ydcmuser label new' ) );

				// when adding a user, if login_end date exists we setup 7 days interval
				if ( $this->_form->isElement( 'login_end' ) )
					$this->_form->setDefault( 'login_end', time() + 7 * 24 * 3600 );
			}

		}

        /**
         *  Internal method to check if a username already exists 
         */
		function _checkuser( $values, $options ){

			$this->resetAll();
			
			$this->set( 'username', $values[ 'username' ] );

			// check if we are editing. if yes, we should check if the username is already used only by another user than current
			if ( $options[0] == true )
				$this->where( 'user_id != ' . intval( $options[1] ) );
			
			if ( $this->find() == 0 ) return true;
			
			return array( '__ALL__' => t( 'ydcmuser mess username exists' ) );
		}


        /**
         *  This method updates user attributes
         *
         *  @param $id           User id to save attributes
         *  @param $formvalues   (Optional) Custom array with user attributes
         *
         *  @returns    YDResult object. OK      - form updated
		 *                               WARNING - there are form errors
         *                               FATAL   - was not possible to update
         */
		function saveFormEdit( $id, $formvalues = null ){

			return $this->_saveFormDetails( $id, true, $formvalues );
		}


        /**
         *  This method adds a new user
         *
         *  @param $parent_id    Parent id of this new user
         *  @param $formvalues   (Optional) Custom array with user attributes
         *
         *  @returns    YDResult object. OK      - form added
		 *                               WARNING - there are form errors
         *                               FATAL   - was not possible to add
         */
		function saveFormNew( $parent_id, $formvalues = null ){

			return $this->_saveFormDetails( $parent_id, false, $formvalues );
		}


        /**
         *  This method adds/saves a user
         *
         *  @param $id           If we are editing, $id is the user id. If we are adding, $id is the parent_id
         *  @param $edit         Boolean flag that defines if we are editing $id or adding to $id
         *  @param $formvalues   (Optional) Custom array with user attributes
         *
         *  @returns    YDResult
         */
		function _saveFormDetails( $id, $edit, $formvalues = null ){

			// check form validation
			if ( !$this->_form->validate( $formvalues ) )
				return YDResult::warning( t( 'form errors' ), $this->_form->getErrors() );

			// get form values EXCLUDING spans
			$values = $this->_form->getValues();

			// check if we are editing or adding an element
			if ( $edit ){

				// create userobject node
				$userobject = array();
				$userobject['type']  = 'YDCMUser';
				if ( isset( $values[ 'name' ] ) )  $userobject['reference'] = $values[ 'name' ];
				if ( isset( $values[ 'state' ] ) ) $userobject['state']     = $values[ 'state' ];

				// update userobject
				$uobj = new YDCMUserobject();
				$res  = $uobj->updateNode( $userobject, $id );

				// create user row
				$user = array();
				if ( isset( $values[ 'password' ] ) ) $user['password'] = md5( $values[ 'password' ] );
				if ( isset( $values[ 'username' ] ) ) $user['username'] = $values[ 'username' ];
				if ( isset( $values[ 'name' ] ) )     $user['name']     = $values[ 'name' ];
				if ( isset( $values[ 'email' ] ) )    $user['email']    = $values[ 'email' ];
				if ( isset( $values[ 'other' ] ) )    $user['other']    = $values[ 'other' ];
				if ( isset( $values[ 'lang_id' ] ) )  $user['lang_id']  = $values[ 'lang_id' ];
				if ( isset( $values[ 'template' ] ) ) $user['template'] = $values[ 'template' ];

				// check login schedule dates
				if ( isset( $user['login_start'] ) ) $user[ 'login_start' ] = YDStringUtil::formatDate( $values[ 'login_start' ], 'datetimesql' );
				if ( isset( $user['login_end'] ) )   $user[ 'login_end' ]   = YDStringUtil::formatDate( $values[ 'login_end' ], 'datetimesql' );

				// update user
				$this->resetValues();
				$this->setValues( $user );
				$this->where( 'user_id = '. $id ); 

				$res = $this->update();
				
				// check update result and return
				if ( $res > 0 ) return YDResult::ok( t( 'ydcmuser mess updated' ), $res );
				else            return YDResult::warning( t( 'ydcmuser mess impossible to update' ), $res );

			}else{

				// create userobject node
				$userobject = array();
				$userobject['type']      = 'YDCMUser';
				$userobject['reference'] = $values[ 'name' ];
				$userobject['state']     = isset( $values[ 'state' ] ) ? $values[ 'state' ] : 0;
				
				// update userobject and get new id
				$uobj = new YDCMUserobject();
				$res  = $uobj->addNode( $userobject, $id );

				// create user row
				$user = array();

				// add REQUIRED values
				$user[ 'user_id' ]       = $res;
				$user[ 'password' ]      = md5( $values[ 'password' ] );
				$user[ 'username' ]      = $values[ 'username' ];
				$user[ 'lang_id' ]       = $values[ 'lang_id' ];
				$user[ 'template' ]      = $values[ 'template' ];

				$user[ 'name' ]          = isset( $values[ 'name' ] )  ? $values[ 'name' ]  : '';
				$user[ 'email' ]         = isset( $values[ 'email' ] ) ? $values[ 'email' ] : '';
				$user[ 'other' ]         = isset( $values[ 'other' ] ) ? $values[ 'other' ] : '';
				$user[ 'login_counter' ] = 0;
				$user[ 'login_start' ]   = YDStringUtil::formatDate( 0, 'datetimesql' );
				$user[ 'login_end' ]     = YDStringUtil::formatDate( 0, 'datetimesql' );
				$user[ 'login_last' ]    = YDStringUtil::formatDate( 0, 'datetimesql' );
				$user[ 'login_current' ] = YDStringUtil::formatDate( 0, 'datetimesql' );

				// reset object
				$this->resetAll();

				$this->setValues( $user );

				// insert values
				if ( $this->insert() ) return YDResult::ok( t( 'ydcmuser mess created' ), $res );
				else                   return YDResult::fatal( t( 'ydcmuser mess impossible to create' ), $res );
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

			YDInclude( 'YDForm.php' );

			// init form
			$this->_form = new YDForm( 'YDCMUsers' );

			// add old password box
			if ( $oldpassword )
            	$this->_form->addElement( 'password', 'old',          t('ydcmuser label old password'),         array('size' => 20, 'maxlength' => 31) );

			// add password boxes
            $this->_form->addElement( 'password',    'new',          t('ydcmuser label new password'),         array('size' => 30, 'maxlength' => 31) );
            $this->_form->addElement( 'password',    'new_confirm',  t('ydcmuser label confirm password'), array('size' => 30, 'maxlength' => 31) );

			$this->_form->addRule(    '__ALL__',     'required',     t('ydcmuser mess passwords are required') );
			$this->_form->addRule(    '__ALL__',     'maxlength',    t('ydcmuser mess passwords too big'), 31 );
			$this->_form->addRule(    '__ALL__',     'alphanumeric', t('ydcmuser mess passwords not alphanumeric') );

			// add compare rule to new password and confirmation password
			$this->_form->addCompareRule( array( 'new', 'new_confirm' ), 'equal', t('passwords dont match') );
		}


        /**
         *  This function updates a user password
         *
         *  @param $user_id     User id to update password
         *  @param $formvalues  (Otional) Custom array with 2 passwords (new and new_confirm)
         *
         *  @returns    YDResult object. OK      - password updated
		 *                               WARNING - there are form errors
         *                               FATAL   - was not possible to update
         */
		function saveFormPassword( $user_id, $formvalues = null ){

			// check form validation
			if ( !$this->_form->validate( $formvalues ) )
				return YDResult::warning( t( 'form errors' ), $this->_form->getErrors() );

			// reset values
			$this->resetAll();

			// set new password
			$this->set( 'password', md5( $this->_form->getValue( 'new' ) ) );

			$this->where( 'user_id = ' . intval( $user_id ) );

			// update user and get result
			if ( $this->update() == 1 ) return YDResult::ok( t('ydcmuser mess password updated') );
			else                        return YDResult::fatal( t('ydcmuser mess password not updated') );
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
			if ( is_array( $defaults ) ) $this->_form->setDefaults( $defaults );

			return $this->_form;
		}


        /**
         *  This method returns user attributes
         *
         *  @param      $user_id     User id or username
         *  @param      $translate   (Optional) Boolean that defines if result must be translated
         *
         *  @returns    FALSE if user not found, user details array otherwise
         */
		function getUser( $user_id, $translate = false ){

			$this->resetAll();

			// set user id to search for
			if ( is_numeric( $user_id ) ) $this->set( 'user_id',  intval( $user_id ) );
			else                          $this->set( 'username', strval( $user_id ) );

			// get all attributes
			if ( $this->findAll() != 1 ) return false;

			// password should be NOT visible
			$this->set( 'password', '' );

			// check if we want human readable elements
			if ( $translate ){

				// translate states
				switch( intval( $this->get( 'ydcmuserobject_state' ) ) ){
					case 0 : $this->set( 'ydcmuserobject_state', t('blocked') );
							 $this->set( 'login_start',          t('not applicable') );
							 $this->set( 'login_end',            t('not applicable') );
							 break;
					case 1 : $this->set( 'ydcmuserobject_state', t('active') );
							 $this->set( 'login_start',          t('not applicable') );
							 $this->set( 'login_end',            t('not applicable') );
							 break;
					case 2 : $this->set( 'ydcmuserobject_state', t('schedule') );
							 break;
				}

			}

			return $this->getValues();
		}


        /**
         *  This method returns a specific user attribute
         *
         *  @param      $user_id     User id to use (instead of internal)
         *  @param      $attribute   Attribute name string
         *
         *  @returns    FALSE if attribute not found, attribute string otherwise
         */
		function getUserAttribute( $user_id, $attribute ){

			$this->resetValues();

			// set user id
			$this->set( 'user_id', intval( $user_id ) );

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

			$this->resetAll();
			
			// set our user_id for search
			$this->set( 'user_id', $this->currentID() );

			// get all attributes
			$this->find();

			// increase login counter value
			$this->set( 'login_counter', 1 + $this->get( 'login_counter' ) );

			// set last login date
			$this->set( 'login_last', $this->get( 'login_current' ) );

			// set current login
			$this->set( 'login_current', YDStringUtil::formatDate( time(), 'datetimesql' ) );

			// because we don't have a primary key we must do this
			$this->where( 'user_id = ' . $this->get( 'user_id' ) );
			
			// return update result
			if ( $this->update() == 1 ) return true;
			
			return false;
		}
		
		
        /**
         *  This method returns the id of the current user if you use PHP_AUTH_USER and PHP_AUTH_PW for authentication
         */
		function currentID(){
		
			// check if user is valid and get user id
			return $this->valid( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] );
		}


    }
?>