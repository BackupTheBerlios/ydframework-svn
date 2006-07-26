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

			// relation with lock table
            $rel = & $this->registerRelation( 'YDCMLocks', false, 'YDCMLocks' );
			$rel->setLocalKey( 'user_id' );
            $rel->setForeignKey( 'user_id' );
			$rel->setForeignJoin( 'LEFT' );

			// relation with languages table
            $rel = & $this->registerRelation( 'YDCMLanguages', false, 'YDCMLanguages' );
			$rel->setLocalKey( 'language_id' );
            $rel->setForeignKey( 'language_id' );

			// TODO: tree position is required
			$this->tree = new YDDatabaseTree( 'default', 'YDCMUsers', 'user_id', 'parent_id', 'parent_id' );

			// add tree fields
			$this->tree->addField( 'parent_id' );
			$this->tree->addField( 'type' );
			$this->tree->addField( 'state' );
			$this->tree->addField( 'name' );

			// init user form
			$this->form = new YDForm( 'YDCMUsers' );
		}


        /**
         *  This function sets the user id of this user object
         *
         *  @param $id  User id
         */
		function setId( $id ){
		
			$this->id = $id;
		}


        /**
         *  This function returns the id of the current user
         */
		function currentID(){
		
			// reset user values
			$this->resetValues();
		
			// check if user is valid and get user id ( YDCMUsers::valid does it )
			$valid = $this->valid( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] );

			// if user is not valid we should return false
			if ( $valid === false ) return false;

			// return user id found
			return $this->user_id;
		}


        /**
         *  This function returns all sub users
         *
         *  @returns    all tree elements 
         */
		function getTreeElements(){

			return $this->tree->getDescendants( $this->id, true );
		}


        /**
         *  This function checks if a username and password are valid
         *
         *  @param $username  User username
         *  @param $password  User password (if password length is smaller than 32 we must md5 it)
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
         *  This function updates the current user attributes (ignoring passwords and statistics)
         *
         *  @param $values  User attributes as array
         *
         *  @returns    true if updated, false if current user is not valid
         */
		function changeCurrentUser( $values ){
		
			$this->resetValues();

			// check if user is valid
			$valid = $this->valid( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] );

			if ( $valid === false ) return false;

			// reset values added from valid method
			$this->resetValues();

			// set new values
			$this->name        = $values['name'];
			$this->email       = $values['email'];
			$this->other       = $values['other'];
			$this->language_id = $values['language_id'];
			$this->template    = $values['template'];

			$this->where( 'username = "' .      $_SERVER['PHP_AUTH_USER'] . '"' );
			$this->where( 'password = "' . md5( $_SERVER['PHP_AUTH_PW'] ) . '"' );

			// return update result
			if ( $this->update() == 1 ) return true;
			
			return false;
		}


        /**
         *  This function updates the current user attributes (ignoring passwords and statistics) using form values
         *
         *  @param $formvalues  Array with user attributes
         *
         *  @returns    true if updated, array with form errors otherwise
         */
		function changeCurrentUserForm( $formvalues = array() ){

			// check form validation
			if ( !$this->form->validate( $formvalues ) )
				return $this->form->getErrors();

			return $this->changeCurrentUser( $this->form->getValues() );
		}
		

        /**
         *  This function creates an user details form
		 *
		 * @param $username   1: Username is a span, 2: Username is a text box
		 * @param $password   0: Password is not showed, 1: Password is showed
         */
		function addFormDetails( $username = 1, $password = 0 ){

			// add username
			if ( $username == 1 ) $this->form->addElement( 'span',     'username', t('user_username') );
			else                  $this->form->addElement( 'text',     'username', t('user_username') );

			// add password
			if ( $password == 1 ) $this->form->addElement( 'password', 'password', t('user_password') );

			// add name
            $this->form->addElement( 'text',      'name',         t('user_name'),     array('size' => 50, 'maxlength' => 255) );
			$this->form->addRule(    'name',      'maxlength',    t('name too big'),  255 );

			// add email
            $this->form->addElement( 'text',      'email',        t('user_email') );
			$this->form->addRule(    'email',     'email',        t('email not valid') );

			// add other info textare
            $this->form->addElement( 'textarea',  'other',        t('user_other'),    array('rows' => 4, 'cols' => 30) );
			$this->form->addRule(    'other',     'maxlength',    t('other too big'), 5000 );

			// add languages select box
			$languages = YDCMComponent::module( 'YDCMLanguages' );
			$this->form->addElement( 'select',      'language_id',  t('user_language'),      array(), $languages->active() );
			$this->form->addRule(    'language_id', 'in_array',     t('language not valid'), array_keys( $languages->active() ) );

			// add template select box
			$templates = YDCMComponent::module( 'YDCMTemplates' );
            $this->form->addElement( 'select',    'template',     t('user_template'),      array(), $templates->admin_templates() );
			$this->form->addRule(    'template',  'in_array',     t('template not valid'), array_keys( $templates->admin_templates() ) );

			// add user details
            $this->form->addElement( 'span', 'login_start',  t('login_start') );
            $this->form->addElement( 'span', 'login_end',    t('login_end') );
            $this->form->addElement( 'span', 'login_counter',t('login_counter') );
            $this->form->addElement( 'span', 'login_last',   t('login_last') );
            $this->form->addElement( 'span', 'login_current',t('login_current') );
            $this->form->addElement( 'span', 'created_user', t('created_user') );
            $this->form->addElement( 'span', 'created_date', t('created_date') );
		}


        /**
         *  This function creates an user form for password changing
         *
         * @param $oldpassword  0: don't include old password box; 1: include old password box
         */
		function addFormPassword( $oldpassword = 1 ){

			// add old password box
			if ( $oldpassword == 1 )
            	$this->form->addElement( 'password', 'old',         t('password_old'),         array('size' => 20, 'maxlength' => 31) );

			// add new password box and confirmation box
            $this->form->addElement( 'password', 'new',         t('password_new'),         array('size' => 30, 'maxlength' => 31) );
            $this->form->addElement( 'password', 'new_confirm', t('password_new_confirm'), array('size' => 30, 'maxlength' => 31) );

			// add rules to all passwords
			$this->form->addRule( array( 'old', 'new', 'new_confirm' ), 'required',     t('passwords are required') );
			$this->form->addRule( array( 'old', 'new', 'new_confirm' ), 'maxlength',    t('passwords too big'), 31 );
			$this->form->addRule( array( 'old', 'new', 'new_confirm' ), 'alphanumeric', t('passwords not alphanumeric') );

			// add compare rule to new password and confirmation password
			$this->form->addCompareRule( array( 'new', 'new_confirm' ), 'equal', t('passwords dont match') );
		}


        /**
         *  This returns the user form
         *
         *  @param $defaults  (Optional) default values to apply in form
         *
         *  @returns    YDForm object
         */
		function getForm( $defaults = false ){

			if ( is_array( $defaults ) ) $this->form->setDefaults( $defaults );

			return $this->form;
		}


        /**
         *  This function updates the current user password
         *
         *  @param $oldpassword  Old user password
         *  @param $newpassword  New password
         *
         *  @returns    true if updated, false when old password is incorrect or user is invalid
         */
		function changeCurrentUserPassword( $oldpassword = '', $newpassword = '' ){
		
			// check if user is valid
			if ( !$this->valid( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ) ) return false;

			// reset values added from valid method
			$this->resetAll();

			// set new password
			$this->password = md5( $newpassword );

			// change only current user
			// TODO: escape 'username'
			$this->where( 'username = "' .      $_SERVER['PHP_AUTH_USER'] . '"' );
			$this->where( 'password = "' . md5( $_SERVER['PHP_AUTH_PW'] ) . '"' );
			$this->where( 'password = "' . md5( $oldpassword )            . '"' );

			// update user and get result
			if ( $this->update() == 1 ) return true;

			return false;
		}


        /**
         *  This function updates the current user password using form values
         *
         *  @param $formvalues  Array with 3 passwords (old, new and new_confirm)
         *
         *  @returns    true if updated, array with form errors, 0 if old password is incorrect
         */
		function changeCurrentUserPasswordForm( $formvalues = array() ){

			// check form validation
			if ( !$this->form->validate( $formvalues ) )
				return $this->form->getErrors();

			return $this->changeCurrentUserPassword( $this->form->getValue( 'old' ), $this->form->getValue( 'new' ) );
		}


        /**
         *  This function return the current user attributes
         *
         *  @returns    current user id
         */
		function getCurrentUser( $translate = false ){
		
			// get current user id
			return $this->getUser( $this->currentID(), $translate );

		}


		function getUser( $id, $translate = false ){

			$this->resetValues();

			// set user id
			$this->user_id = intval( $id );

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

				// check if user is root
				if ( $this->created_user == 0 ){
					$this->created_user = t('not applicable');
					$this->created_date = t('not applicable');
				}
			}

			// return values
			return $this->getValues();
		}


        /**
         *  This function updates user login details
         *
         *  @returns    true if user login details updated, false user is not valid or details not updated
         */
		function updateLogin(){

			YDInclude( 'YDUtil.php' );

			$this->resetValues();
			
			// get current user id
			$res = $this->currentID();
			
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

		
    }
?>