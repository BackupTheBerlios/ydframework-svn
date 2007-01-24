<?php

    /*

        Yellow Duck Framework version 2.1
        (c) Copyright 2002-2007 Pieter Claerhout

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

    /*
     *  @addtogroup YDCMComponent Addons - CMComponent
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

	YDConfig::set( 'YDCMTEMPLATES_ADMIN_EXT', '/shot.png', false );

	// set user logged and user details
	YDConfig::set( 'YD_USER_LOGGED',  false,   false );

	// add YDF libs needed by this class
	YDInclude( 'YDDatabaseObject.php' );
	YDInclude( 'YDValidateRules.php' );
	YDInclude( 'YDUtil.php' );
	YDInclude( 'YDResult.php' );

	// add YDCM libs
	YDInclude( 'YDCMLanguages.php' );
	YDInclude( 'YDCMTemplates.php' );
	YDInclude( 'YDCMUserobject.php' );

	// define forum levels
	define( 'YD_FORUM_PERMISSION_1', 'Registered' );
	define( 'YD_FORUM_PERMISSION_2', 'Moderator' );
	define( 'YD_FORUM_PERMISSION_3', 'Administrator' );


	// user class
    /**
     *  @ingroup YDCMComponent
     */
    class YDCMUser extends YDDatabaseObject {
    
        function YDCMUser() {
        
			// init DB object
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMUser' );

			// register fields
			$this->registerKey( 'userobject_id' );
			$this->registerKey( 'language_id', false );

			$this->registerField( 'user_username' );
			$this->registerField( 'user_password' );
			$this->registerField( 'user_name' );
			$this->registerField( 'user_email' );			
			$this->registerField( 'user_other' );	
			$this->registerField( 'user_loginstart' );
			$this->registerField( 'user_loginend' );
			$this->registerField( 'user_logincounter' );			
			$this->registerField( 'user_loginlast' );
			$this->registerField( 'user_logincurrent' );
			$this->registerField( 'user_template' );
			$this->registerField( 'user_creationdate' );
			$this->registerField( 'user_refreshdate' );
			$this->registerField( 'user_country' );
			$this->registerField( 'user_interests' );
			$this->registerField( 'user_bio' );
			$this->registerField( 'user_photo' );
			$this->registerField( 'user_birthdate' );
			$this->registerField( 'user_website' );
			$this->registerField( 'user_im_icq' );
			$this->registerField( 'user_im_msn' );
			$this->registerField( 'user_im_yahoo' );
			$this->registerField( 'user_im_aol' );

			// forum extra fields
			$this->registerField( 'user_forum_permission' );
			$this->registerField( 'user_forum_signature' );
			$this->registerField( 'user_forum_totalposts' );
			$this->registerField( 'user_forum_totaltopics' );
			$this->registerField( 'user_forum_avatar' );

			// init relation with userobject
            $rel = & $this->registerRelation( 'ydcmuserobject', false, 'ydcmuserobject' );
			$rel->setLocalKey( 'ydcmuser.userobject_id' );
            $rel->setForeignKey( 'ydcmuserobject.userobject_id' );

			// init relation with languages table
            $rel = & $this->registerRelation( 'ydcmlanguages', false, 'ydcmlanguages' );
			$rel->setLocalKey( 'ydcmuser.language_id' );
            $rel->setForeignKey( 'ydcmlanguages.language_id' );

			// init results cache
			$this->_cache_activeusers = null;
		}


        /**
         *  This method checks if a username and password are valid
         *
         *  @param $username  User username
         *  @param $password  User password (if password length is smaller than 32 we must md5 it, password form elements must have max 31)
         *
         *  @returns    ARRAY with user attributes, FALSE otherwise
         */
		function valid( $username = '', $password = '', $force_md5 = false ){

			// test username and password
			if ( !YDValidateRules::alphanumeric( $username ) )   return false;
			if ( !YDValidateRules::maxlength( $username, 100 ) ) return false;
			if ( !YDValidateRules::alphanumeric( $password ) )   return false;
			if ( !YDValidateRules::maxlength( $password, 32 ) )  return false;

			// reset values
			$this->resetAll();

			// set username
			$this->set( 'user_username', $username );

			// if password is not in md5 format we must set it
			if ( $force_md5 ) $password = md5( $password );

			// set password
			$this->set( 'user_password', $password );

			// check user state based on state and/or user login schedule
			$now = YDStringUtil::formatDate( time(), 'datetimesql' );

			// relation with userobject
			$this->load( 'ydcmuserobject' );
			$this->where( '(ydcmuserobject.userobject_state = 1 OR (ydcmuserobject.userobject_state = 2 AND ydcmuser.user_loginstart < "' . $now . '" AND ydcmuser.user_loginend > "' . $now . '"))' );

			// test if we get just one user
			if ( $this->find( 'ydcmuserobject' ) != 1 ) return false;
			
			return $this->getValues();
		}


        /**
         *  This method checks if a user ID is valid ( will check if user_id is on its 3 tables )
         *
         *  @param $userobject_id        User id
         *
         *  @returns    TRUE if valid, FALSE otherwise
         */
		function validID( $userobject_id ){
		
			// reset values
			$this->resetAll();

			// find all rows this user id
			$this->where( 'userobject_id = ' . intval( $userobject_id ) );

			// join all tables when finding (to check if userobject_id really belongs to all 3 tables)
			return ( $this->findAll() == 1 );
		}


        /**
         *  This method deletes a user (and all children) or just the children
         *
         *  @param $userobject_id       Userobject id
         *  @param $deleteAll     (Optional) Delete id and all children (true by default. if false, deletes children only)
         *
         *  @returns    YDResult object. OK      - user deleted
		 *                               WARNING - no deletes where made
         */
		function deleteUser( $userobject_id, $deleteAll ){
		
			$obj = new YDCMUserobject();
			
			// delete user and get result
			if ( $obj->deleteNode( $userobject_id, $deleteAll ) ) return YDResult::ok( t('ydcmuser mess delete ok') );
			else                                            return YDResult::fatal( t('ydcmuser mess delete empty') );
		}


        /**
         *  This method adds form elements for user editing
		 *
		 * @param $id           User id to edit
		 * @param $noneditable  (Optional) Array with form element names that are NOT editable (are spans).
         *
		 * @returns    YDForm object pointer         
         */
		function & addFormEdit( $id, $noneditable = array() ){

			$this->editing_ID = $id;

		 	return $this->_addFormDetails( $id, true, $noneditable );
		}


        /**
         *  This method adds form elements for addind a new user
		 *
		 * @param $id    (Optional) Predefined-Group_id of this new user
         *
		 * @returns    YDForm object pointer         
         */
		function & addFormNew( $id = null ){

			$this->editing_ID = $id;

		 	return $this->_addFormDetails( $id, false, array() );
		}
		 
		
        /**
         *  Helper method for user management
		 *
		 * @param $id           If you will EDIT some user this is the user id to edit.
         *                      If you will ADD a new user this is the group id (parent_id) of the new user.
		 * @param $edit         The edit parameter. TRUE if form is form editing, FALSE if form is for a new user creation
		 * @param $noneditable  Array with form element names that are NOT editable
         *
		 * @returns    YDForm object pointer         
		 */
		function & _addFormDetails( $id, $edit, $noneditable ){

			YDInclude( 'YDForm.php' );

			// init form
			$this->_form = new YDForm( 'YDCMUsers' );

			// check arguments
			if ( !is_array( $noneditable ) ) $noneditable = array( $noneditable );

			// add username
			if ( in_array( 'user_username', $noneditable ) ){
				$this->_form->addElement( 'span', 'user_username', t( 'ydcmuser label username' ) );
			}else{
				$this->_form->addElement( 'text', 'user_username', t( 'ydcmuser label username' ) );
				$this->_form->addFormRule( array( & $this, '_checkuser' ), array( $edit, $id ) );
			}

			// add password box for new users
			if ( $edit == false ){
				$this->_form->addElement( 'password', 'user_password', t( 'ydcmuser label password' ) );
				$this->_form->addElement( 'password', 'user_password2', t( 'ydcmuser label password2' ) );
			}

			// add state
			if ( in_array( 'state', $noneditable ) ){
				$this->_form->addElement( 'span',           'state',       t( 'ydcmuser label state' ) );
				$this->_form->addElement( 'span',           'user_loginstart', t( 'ydcmuser label login_start' ) );
				$this->_form->addElement( 'span',           'user_loginend',   t( 'ydcmuser label login_end' ) );
			}else{
				$this->_form->addElement( 'select',         'state',       t( 'ydcmuser label state' ), array(), array(1 => t('yes'), 0 => t('no'), 2 => t('schedule')) );
				$this->_form->addElement( 'datetimeselect', 'user_loginstart', t( 'ydcmuser label login_start' ) );
				$this->_form->addElement( 'datetimeselect', 'user_loginend',   t( 'ydcmuser label login_end' ) );
			}

			// add common user details
			if ( in_array( 'details', $noneditable ) ){
				$this->_form->addElement( 'span',      'user_name',          t( 'ydcmuser label name' ) );
				$this->_form->addElement( 'span',      'user_email',         t( 'ydcmuser label email' ) );
				$this->_form->addElement( 'span',      'user_other',         t( 'ydcmuser label other' ) );
				$this->_form->addElement( 'span',      'user_language_id',       t( 'ydcmuser label language' ) );
				$this->_form->addElement( 'span',      'user_template',      t( 'ydcmuser label template' ) );
			}else{
				$this->_form->addElement( 'text',      'user_name',          t( 'ydcmuser label name' ),     array('size' => 50, 'maxlength' => 255) );
				$this->_form->addElement( 'text',      'user_email',         t( 'ydcmuser label email' ) );
				$this->_form->addElement( 'textarea',  'user_other',         t( 'ydcmuser label other' ),    array('rows' => 15, 'cols' => 40) );

				$languages = new YDCMLanguages();
				$languages = $languages->active();
				$this->_form->addElement( 'select',    'user_language_id',       t( 'ydcmuser label language' ), array(), $languages );

				$templates = YDCMTemplates::getNames();

				// get url to templates and set shot.png as filename
				$attributes = array( 'border' => 1, 'src' => YDConfig::get( 'YDCMTEMPLATES_ADMIN_URL' ), 'ext' => 	YDConfig::get( 'YDCMTEMPLATES_ADMIN_EXT' ) );

    	        $this->_form->addElement( 'selectimage',    'user_template', t( 'ydcmuser label template' ), $attributes, $templates );

				$this->_form->addRule( 'user_name',        'maxlength',      t( 'ydcmuser mess name too big' ),  255 );
				$this->_form->addRule( 'user_email',       'email',          t( 'ydcmuser mess email not valid' ) );
				$this->_form->addRule( 'user_other',       'maxlength',      t( 'ydcmuser mess other too big' ), 5000 );
				$this->_form->addRule( 'user_language_id', 'in_array',       t( 'ydcmuser mess language not valid' ), array_keys( $languages ) );
				$this->_form->addRule( 'user_template',    'in_array',       t( 'ydcmuser mess template not valid' ), array_keys( $templates ) );
			}

			// add access information when editing
			if ( $edit ) {
				$this->_form->addElement( 'span', 'user_logincounter', t( 'ydcmuser label login_counter' ) );
   	        	$this->_form->addElement( 'span', 'user_loginlast',    t( 'ydcmuser label login_last' ) );
   	        	$this->_form->addElement( 'span', 'user_logincurrent', t( 'ydcmuser label login_current' ) );
			}

			// if we are editing a user it's a good idea to set form defaults with user node attributes
			if ( $edit ){

				// add submit button
				$this->_form->addElement( 'submit', '_cmdSubmit', t( 'ydcmuser label save' ) );

				// set form defaults based on user attributes
				$this->_form->setDefaults( $this->getUser( $id ) );

			}else{

				// if id argument is not set its because we want a select box (with groups) so choose
				if ( is_null( $id ) ){
				
					// get groups from userobject
					$obj = new YDCMUserobject();
					$this->_form->addElement( 'select', 'group', t( 'ydcmuser label group' ), array(), $obj->getElements( 'YDCMGroup', 'reference' ) );
				}					

				// add submit button
				$this->_form->addElement( 'submit', '_cmdSubmit', t( 'ydcmuser label new' ) );

				// when adding a user, if login_end date exists we setup 7 days interval
				if ( $this->_form->isElement( 'user_loginend' ) )
					$this->_form->setDefault( 'user_loginend', time() + 7 * 24 * 3600 );
			}


			return $this->_form;
		}

        /**
         *  Internal method to check if a username already exists 
         */
		function _checkuser( $values, $options ){

			$this->resetAll();
			
			$this->set( 'user_username', $values[ 'username' ] );

			// check if we are editing. if yes, we should check if the username is already used only by another user than current
			if ( $options[0] == true )
				$this->where( 'userobject_id != ' . intval( $options[1] ) );
			
			if ( $this->find() == 0 ) return true;
			
			return array( '__ALL__' => t( 'ydcmuser mess username exists' ) );
		}


        /**
         *  This method updates user attributes
         *
         *  @param $formvalues   (Optional) Custom array with user attributes
         *
         *  @returns    YDResult object. OK      - form updated
		 *                               WARNING - there are form errors
         *                               FATAL   - was not possible to update
         */
		function saveFormEdit( $formvalues = null ){

			return $this->_saveFormDetails( $this->editing_ID, true, $formvalues );
		}


        /**
         *  This method adds a new user
         *
         *  @param $formvalues   (Optional) Custom array with user attributes
         *
         *  @returns    YDResult object. OK      - form added
		 *                               WARNING - there are form errors
         *                               FATAL   - was not possible to add
         */
		function saveFormNew( $formvalues = null ){

			return $this->_saveFormDetails( $this->editing_ID, false, $formvalues );
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
				if ( isset( $values[ 'user_name' ] ) )  $userobject['reference'] = $values[ 'user_name' ];
				if ( isset( $values[ 'state' ] ) ) $userobject['state']     = $values[ 'state' ];

				// update userobject
				$uobj = new YDCMUserobject();
				$res  = $uobj->updateNode( $userobject, $id );

				// create user row
				$user = array();
				if ( isset( $values[ 'user_password' ] ) ) $user['user_password'] = md5( $values[ 'user_password' ] );
				if ( isset( $values[ 'user_username' ] ) ) $user['user_username'] = $values[ 'user_username' ];
				if ( isset( $values[ 'user_name' ] ) )     $user['user_name']     = $values[ 'user_name' ];
				if ( isset( $values[ 'user_email' ] ) )    $user['user_email']    = $values[ 'user_email' ];
				if ( isset( $values[ 'user_other' ] ) )    $user['user_other']    = $values[ 'user_other' ];
				if ( isset( $values[ 'user_language_id' ] ) )  $user['user_language_id']  = $values[ 'user_language_id' ];
				if ( isset( $values[ 'user_template' ] ) ) $user['user_template'] = $values[ 'user_template' ];

				// check login schedule dates
				if ( isset( $user['user_loginstart'] ) ) $user[ 'user_loginstart' ] = YDStringUtil::formatDate( $values[ 'user_loginstart' ], 'datetimesql' );
				if ( isset( $user['user_loginend'] ) )   $user[ 'user_loginend' ]   = YDStringUtil::formatDate( $values[ 'user_loginend' ], 'datetimesql' );

				// update user
				$this->resetValues();
				$this->setValues( $user );
				$this->where( 'userobject_id = '. $id ); 

				$res = $this->update();
				
				// check update result and return
				if ( $res > 0 ) return YDResult::ok( t( 'ydcmuser mess updated' ), $res );
				else            return YDResult::warning( t( 'ydcmuser mess impossible to update' ), $res );

			}else{

				// check if parent it is set in argument or was choosen in the group selectbox
				if ( is_null( $id ) ) $id = $values[ 'group' ];

				// create userobject node
				$userobject = array();
				$userobject['type']      = 'YDCMUser';
				$userobject['reference'] = $values[ 'name' ];
				$userobject['state']     = isset( $values[ 'state' ] ) ? $values[ 'state' ] : 0;

				// insert a new node in userobject and get the new id for user row creation
				$uobj = new YDCMUserobject();
				$res = $uobj->addNode( $userobject, intval( $id ) );

				// create user row
				$user = array();

				// add REQUIRED values
				$user[ 'userobject_id' ]       = $res;
				$user[ 'user_password' ]      = md5( $values[ 'user_password' ] );
				$user[ 'user_username' ]      = $values[ 'user_username' ];
				$user[ 'user_language_id' ]       = $values[ 'user_language_id' ];
				$user[ 'user_template' ]      = $values[ 'user_template' ];

				$user[ 'user_name' ]          = isset( $values[ 'user_name' ] )  ? $values[ 'user_name' ]  : '';
				$user[ 'user_email' ]         = isset( $values[ 'user_email' ] ) ? $values[ 'user_email' ] : '';
				$user[ 'user_other' ]         = isset( $values[ 'user_other' ] ) ? $values[ 'user_other' ] : '';
				$user[ 'user_logincounter' ] = 0;
				$user[ 'user_loginstart' ]   = YDStringUtil::formatDate( 0, 'datetimesql' );
				$user[ 'user_loginend' ]     = YDStringUtil::formatDate( 0, 'datetimesql' );
				$user[ 'user_loginlast' ]    = YDStringUtil::formatDate( 0, 'datetimesql' );
				$user[ 'user_logincurrent' ] = YDStringUtil::formatDate( 0, 'datetimesql' );

				// reset object
				$this->resetValues();

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
         *
         * @returns YDForm object pointer
         */
		function & addFormPassword( $oldpassword = true ){

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

			return $this->_form;
		}


        /**
         *  This function updates a user password
         *
         *  @param $userobject_id     User id to update password
         *  @param $formvalues  (Otional) Custom array with 2 passwords (new and new_confirm)
         *
         *  @returns    YDResult object. OK      - password updated
		 *                               WARNING - there are form errors
         *                               FATAL   - was not possible to update
         */
		function saveFormPassword( $userobject_id, $formvalues = null ){

			// check form validation
			if ( !$this->_form->validate( $formvalues ) )
				return YDResult::warning( t( 'form errors' ), $this->_form->getErrors() );

			// reset values
			$this->resetAll();

			// set new password
			$this->set( 'user_password', md5( $this->_form->getValue( 'new' ) ) );

			$this->where( 'userobject_id = ' . intval( $userobject_id ) );

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
         *  This method returns all user information
         *  @returns    Array with users
         */
		function getUsers(){

			$this->reset();
			$this->findAll();
			return $this->getResults();
		}


		function getUsersAsRecordSet( $page = 1 ){
			return new YDRecordSet( $this->getUsers(), $page, 2 );
		}


        /**
         *  This method returns user attributes
         *
         *  @param      $userobject_id     User id or username
         *  @param      $translate   (Optional) Boolean that defines if result must be translated
         *
         *  @returns    FALSE if user not found, user details array otherwise
         */
		function getUser( $userobject_id, $translate = false ){

			$this->resetAll();

			// set user id to search for
			if ( is_numeric( $userobject_id ) ) $this->set( 'userobject_id',  intval( $userobject_id ) );
			else                          $this->set( 'user_username', strval( $userobject_id ) );

			// get all attributes
			if ( $this->findAll() != 1 ) return false;

			// password should be NOT visible
			$this->set( 'user_password', '' );

			// check if we want human readable elements
			if ( $translate ){

				// translate states
				switch( intval( $this->get( 'ydcmuserobject_state' ) ) ){
					case 0 : $this->set( 'ydcmuserobject_state', t('blocked') );
							 $this->set( 'user_loginstart',          t('not applicable') );
							 $this->set( 'user_loginend',            t('not applicable') );
							 break;
					case 1 : $this->set( 'ydcmuserobject_state', t('active') );
							 $this->set( 'user_loginstart',          t('not applicable') );
							 $this->set( 'user_loginend',            t('not applicable') );
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
         *  @param      $userobject_id     User id to use (instead of internal)
         *  @param      $attribute   Attribute name string
         *
         *  @returns    FALSE if attribute not found, attribute string otherwise
         */
		function getUserAttribute( $userobject_id, $attribute ){

			$this->resetValues();

			// set user id
			$this->set( 'userobject_id', intval( $userobject_id ) );

			// get all attributes
			if ( $this->find() != 1 ) return false;

			return $this->get( $attribute );
		}



        /**
         *  This method returns the newest users created
         *
         *  @returns    Array with latest users
         */
		function getNewestUsers(){

			$this->reset();
			
			$this->limit( 5 );
			$this->orderby( 'userobject_id DESC' );
			$this->find();
			
			return $this->getResults();
		}


        /**
         *  This method returns the latest active users
         *
         *  @returns    Array with latest users
         */
		function getActiveUsers( $minutes = 15, $use_cache = true ){

			if ( $use_cache ){

				if ( is_null( $this->_cache_activeusers ) ) $this->_cache_activeusers = $this->getActiveUsers( $minutes, false );

				return $this->_cache_activeusers;
			}

			$this->reset();
			
			$this->limit( 100 );
			$this->where( 'user_logincurrent > "' . YDStringUtil::formatDate( time() - 60 * intval( $minutes ), 'datetimesql' ) . '"' );
			$this->orderby( 'user_logincurrent DESC' );
			$this->find();
			
			return $this->getResults();
		}


        /**
         *  This method returns the total of latest active users
         *
         *  @returns    Total of active users
         */
		function getTotalActiveUsers(){

			if ( is_null( $this->_cache_activeusers ) ) $this->_cache_activeusers = $this->getActiveUsers( $minutes, false );

			return count( $this->_cache_activeusers );
		}


        /**
         *  This method returns the total of users
         *
         *  @returns    Total of users
         */
		function getTotals(){
		
			$this->reset();
			return $this->_db->getValue( 'SELECT COUNT(*) as total FROM ' . $this->getTable() );
		}


        /**
         *  This function updates current user login details
         *
         *  @returns    true if user login details updated, false if user is not valid or details not updated
         */
		function updateLogin( $userobject_id = null ){

			$this->resetAll();
			
			// set our userobject_id for search
			if ( is_numeric( $userobject_id ) ) $this->set( 'userobject_id', intval( $userobject_id ) );
			else                          $this->set( 'userobject_id', $this->currentID() );

			// get all attributes
			$this->find();

			// increase login counter value
			$this->set( 'user_logincounter', 1 + $this->get( 'user_logincounter' ) );

			// set last login date
			$this->set( 'user_loginlast', $this->get( 'user_logincurrent' ) );

			// set current login
			$this->set( 'user_logincurrent', YDStringUtil::formatDate( time(), 'datetimesql' ) );

			// because we don't have a primary key we must do this
			$this->where( 'userobject_id = ' . $this->get( 'userobject_id' ) );
			
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


/*

CREATE TABLE YDCMUser_guests (
   guest_id mediumint(8) UNSIGNED NOT NULL auto_increment,
   guest_ip varchar(15),
   guest_date datetime NOT NULL,
   PRIMARY KEY (guest_id)
);

INSERT INTO YDCMUser_guests VALUES ( 1, '127.0.0.1', '2006-12-14 22:20' );
INSERT INTO YDCMUser_guests VALUES ( 2, '127.0.0.2', '2006-12-14 22:20' );
INSERT INTO YDCMUser_guests VALUES ( 3, '127.0.0.3', '2006-12-14 22:20' );



*/


	// user class
    class YDCMUser_guests extends YDDatabaseObject {
    
        function YDCMUser_guests() {
        
			// init DB object
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMUser_guests' );

			// register fields
			$this->registerKey( 'guest_id' );
			$this->registerField( 'guest_ip' );
			$this->registerField( 'guest_date' );
		}


        /**
         *  This method adds a new guest entry
         */
		function addEntry(){
		
			// delete old and possible duplicated entries
			$this->reset();
			$this->where( $this->getTable() . '.guest_date < "' . YDStringUtil::formatDate( time() - 15 * 60, 'datetimesql' ) . '" OR ' . $this->getTable() . '.guest_ip = "' . $_SERVER['SERVER_ADDR'] . '"' );
			$this->delete();

			// add entry and get id added
			$this->reset();
			$this->set( 'guest_ip', $_SERVER['SERVER_ADDR'] );
			$this->set( 'guest_date', YDStringUtil::formatDate( time(), 'datetimesql' ) );
			return $this->insert();
		}


        /**
         *  This method return total guests
         *
         *  @returns    Numeric value
         */
		function getTotal(){

			$this->reset();
			$this->where( $this->getTable() . '.guest_date > "' . YDStringUtil::formatDate( time() - 15 * 60, 'datetimesql' ) . '"' );
			return $this->find();
		}


}


?>
