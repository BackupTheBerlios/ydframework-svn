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

	YDInclude( 'YDCMComponent.php' );
	YDInclude( 'YDDatabaseTree.php' );

	// add local translation directory
	YDLocale::addDirectory( dirname( __FILE__ ) . '/languages/' );

	// set user form name
	YDConfig::set( 'YDCMUSERS_FORMUSER', 'YDCMUsersForm', false );

	// set password form name
	YDConfig::set( 'YDCMUSERS_FORMPASSWORD', 'YDCMUsersFormPassword', false );


    class YDCMUsers extends YDCMComponent {
    
        function YDCMUsers() {
        
			// init component as non default component
            $this->YDCMComponent( 'YDCMUsers', false );

            $this->_author = 'Francisco Azevedo';
            $this->_version = '0.1';
            $this->_copyright = '(c) Copyright 2006 Francisco Azevedo';
            $this->_description = 'This is a CM component for user management';

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

			// custom relations
            $relLocks = & $this->registerRelation( 'YDCMLocks', false, 'YDCMLocks' );
			$relLocks->setLocalKey( 'user_id' );
            $relLocks->setForeignKey( 'user_id' );
			$relLocks->setForeignJoin( 'LEFT' );
			
            $relLanguages = & $this->registerRelation( 'YDCMLanguages', false, 'YDCMLanguages' );
			$relLanguages->setLocalKey( 'language_id' );
            $relLanguages->setForeignKey( 'language_id' );

			// setup tree (NOT USED YET)
			$this->tree = new YDDatabaseTree( 'default', 'YDCMUsers', 'content_id', 'parent_id', 'parent_id' );
		}


        /**
         *  This function return the id of the current user
         *
         *  @returns    user id
         */
		function currentID(){
		
			// reset user values
			$this->reset();
		
			// set username and password. TODO: parse PHP_AUTH_USER
			$this->username =      $_SERVER['PHP_AUTH_USER'];
			$this->password = md5( $_SERVER['PHP_AUTH_PW'] );
			
			// get user
			$this->findAll();

			// return user id found
			return $this->user_id;
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
			
			// check user state
			// TODO: format 'time()' to a 'datetimesql'
			$this->where( '(state = 1 OR (state = 2 AND login_start < ' . time() . ' AND login_end > ' . time() . '))' );

			// test if we get just one user
			if ( $this->find() == 1 ) return true;

			return false;
		}
		
		
        /**
         *  This function updates the current user attributes (ignoring passwords and statistics)
         *
         *  @param $values  User attributes as array
         *
         *  @returns    true if updated, false otherwise
         */
		function changeCurrentUser( $values ){
		
			$this->resetValues();

			$this->name        = $values['name'];
			$this->email       = $values['email'];
			$this->other       = $values['other'];
			$this->language_id = $values['language_id'];
			$this->template    = $values['template'];

			// TODO: parse PHP_AUTH_USER string
			$this->where( 'username = "' .      $_SERVER['PHP_AUTH_USER'] . '"' );
			$this->where( 'password = "' . md5( $_SERVER['PHP_AUTH_PW'] ) . '"' );

			// return updated rows
			return $this->update();
		}


        /**
         *  This function updates the current user attributes (ignoring passwords and statistics) using form values
         *
         *  @param $formvalues  Array with user attributes
         *
         *  @returns    true if updated, array with form errors otherwise
         */
		function changeCurrentUserForm( $formvalues = array() ){

			// get user YDForm object
			$form = $this->getFormUser();

			// check form validation
			if ( !$form->validate( $formvalues ) )
				return $form->getErrors();

			return $this->changeCurrentUser( $form->getValues() );
		}
		

        /**
         *  This function return the user form
         *
         *  @returns    form object
         */
		function getFormUser(){

			// include languages and templates lib
			YDInclude( 'YDCMLanguages.php' );
			YDInclude( 'YDCMTemplates.php' );

			// create languages and templates object
			$languages = new YDCMLanguages();
			$templates = new YDCMTemplates();

			// create form object
            $form = new YDForm( YDConfig::get( 'YDCMUSERS_FORMUSER' ) );

	        $form->addElement( 'span',      'username',     t('user_username') );
            $form->addElement( 'text',      'name',         t('user_name'),     array('size' => 50, 'maxlength' => 255) );
            $form->addElement( 'text',      'email',        t('user_email') );
            $form->addElement( 'textarea',  'other',        t('user_other'),    array('rows' => 4, 'cols' => 30) );
            $form->addElement( 'select',    'language_id',  t('user_language'), array(), $languages->active() );
            $form->addElement( 'select',    'template',     t('user_template'), array(), $templates->forAdministrators() );
            $form->addElement( 'span',      'login_start',  t('login_start') );
            $form->addElement( 'span',      'login_end',    t('login_end') );
            $form->addElement( 'span',      'login_counter',t('login_counter') );
            $form->addElement( 'span',      'login_last',   t('login_last') );
            $form->addElement( 'span',      'login_current',t('login_current') );
            $form->addElement( 'span',      'created_user', t('created_user') );
            $form->addElement( 'span',      'created_date', t('created_date') );

			// add rules
			$form->addRule( 'name',        'maxlength', t('name too big'),       255 );
			$form->addRule( 'email',       'email',     t('email not valid') );
			$form->addRule( 'other',       'maxlength', t('other too big'),      5000 );
			$form->addRule( 'language_id', 'in_array',  t('language not valid'), array_keys( $languages->active() ) );
			$form->addRule( 'template',    'in_array',  t('template not valid'), array_keys( $templates->forAdministrators() ) );

			return $form;
		}


        /**
         *  This function updates the current user password
         *
         *  @param $oldpassword  Old user password
         *  @param $newpassword  New password
         *
         *  @returns    true if updated, false when old password is incorrect
         */
		function changeCurrentUserPassword( $oldpassword = '', $newpassword = '' ){
		
			$this->resetValues();

			$this->password = md5( $newpassword );

			// TODO: parse PHP_AUTH_USER string
			$this->where( 'username = "' .      $_SERVER['PHP_AUTH_USER'] . '"' );
			$this->where( 'password = "' . md5( $_SERVER['PHP_AUTH_PW'] ) . '"' );
			$this->where( 'password = "' . md5( $oldpassword )            . '"' );

			return $this->update();
		}


        /**
         *  This function updates the current user password using form values
         *
         *  @param $formvalues  Array with 3 passwords (old, new and new_confirm)
         *
         *  @returns    true if updated, array with form errors, 0 if old password is incorrect
         */
		function changeCurrentUserPasswordForm( $formvalues = array() ){

			// get password form
			$form = $this->getFormPassword();

			// check form validation
			if ( !$form->validate( $formvalues ) )
				return $form->getErrors();

			return $this->changeCurrentUserPassword( $form->getValue( 'old' ), $form->getValue( 'new' ) );
		}


        /**
         *  This function return the form of password changing
         *
         *  @returns    form object
         */
		function getFormPassword(){

			// create form object
            $form = new YDForm( YDConfig::get( 'YDCMUSERS_FORMPASSWORD' ) );

			// add form elements
            $form->addElement( 'password', 'old',         t('password_old'),         array('size' => 20, 'maxlength' => 31) );
            $form->addElement( 'password', 'new',         t('password_new'),         array('size' => 30, 'maxlength' => 31) );
            $form->addElement( 'password', 'new_confirm', t('password_new_confirm'), array('size' => 30, 'maxlength' => 31) );

			// add rules
			$form->addRule( array( 'old', 'new', 'new_confirm' ), 'required',     t('passwords are required') );
			$form->addRule( array( 'old', 'new', 'new_confirm' ), 'maxlength',    t('passwords too big'), 31 );
			$form->addRule( array( 'old', 'new', 'new_confirm' ), 'alphanumeric', t('passwords not alphanumeric') );

			// add compare rule
			$form->addCompareRule( array( 'new', 'new_confirm' ), 'equal', t('passwords dont match') );

			return $form;
		}


        /**
         *  This function return the current user attributes
         *
         *  @returns    current user id
         */
		function getCurrentUser(){
		
			$this->resetValues();
			
			// get current user id
			$this->user_id = $this->currentID();
			
			// get all attributes
			$this->find();

			// return values
			return $this->getValues();
		}


        /**
         *  This function updates user login details
         *
         *  @returns    updated result
         */
		function updateLogin(){

			YDInclude( 'YDUtil.php' );

			$this->resetValues();
			
			// get current user id
			$this->user_id = $this->currentID();
			
			// get all attributes
			$this->find();

			// increase login value
			$this->login_counter ++;

			// set last login date
			$this->login_last = $this->login_current;

			// set current login
			$this->login_current = YDStringUtil::formatDate( time(), 'datetimesql' );

			// return values
			return $this->update();
		}

		
    }
?>