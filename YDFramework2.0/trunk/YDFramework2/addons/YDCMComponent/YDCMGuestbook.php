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

	// include YDF libs
    YDInclude( 'YDCMComponent.php' );


    class YDCMGuestbook extends YDCMComponent{
    
        function YDCMGuestbook() {

			// init parent
            $this->YDCMComponent( 'YDCMGuestbook' );

            // define fields
			$this->registerField( 'guestbook_datecreation',  false );
			$this->registerField( 'guestbook_description',   false, 'text',     _('Description') );
			$this->registerField( 'guestbook_email_notify',  false, 'checkbox', _('Notify by email on new posts') );
			$this->registerField( 'guestbook_email_from',    false, 'text',     _('Email from') );
			$this->registerField( 'guestbook_email_subject', false, 'text',     _('Email subject') );
			$this->registerField( 'guestbook_max_words',     false, 'text',     _('Maximum words') );
			$this->registerField( 'guestbook_max_lines',     false, 'text',     _('Maximum lines') );
			$this->registerField( 'guestbook_max_chars',     false, 'text',     _('Maximum caracters') );

			$this->registerField( 'guestbook_use_name',          false, 'checkbox', _('Show name') );
			$this->registerField( 'guestbook_use_email',         false, 'checkbox', _('Show email') );
			$this->registerField( 'guestbook_use_website',       false, 'checkbox', _('Show website') );
			$this->registerField( 'guestbook_use_country',       false, 'checkbox', _('Show country') );
			$this->registerField( 'guestbook_use_websiterating', false, 'checkbox', _('Show website ranking') );

			$this->registerField( 'guestbook_akismet',   false, 'checkbox', _('Use akismet engine') );
			$this->registerField( 'guestbook_moderated', false, 'checkbox', _('Posts are moderated') );
		}

/*		
		function getLatest(){
		
			$this->resetAll();
	
			$this->limit( 5 );
			$this->findAll();

			return $this->getResults( false, false, false, false );

		}


		function getElementsAsRecordSet( $id, $rows = 5 ){

			$page = isset( $_GET['page'] ) ? intval( $_GET['page'] ) : 1;

			return new YDRecordSet( $this->getElements( $id ), $page, $rows );
		}
*/

		function getPostsAsRecordSet( $tree_id, $rows = 1 ){
		
			$posts = new YDCMGuestbook_posts();

			return $posts->getElementsAsRecordSet( $tree_id, $rows );
		}


		function getPostForm( $tree_id ){
		
			// init posts object
			$posts = new YDCMGuestbook_posts();
			
			// get the standard form without title and node elements
			$form = & $posts->getForm( false, false );
		
			// get guestbook configuration
			$gbook = $this->getElementById( $tree_id );

			// remove fields
			if ( $gbook[ 'guestbook_use_name' ] == 0 )          $form->removeElement( 'post_name' );
			if ( $gbook[ 'guestbook_use_email' ] == 0 )         $form->removeElement( 'post_email' );
			if ( $gbook[ 'guestbook_use_website' ] == 0 )       $form->removeElement( 'post_website' );
			if ( $gbook[ 'guestbook_use_country' ] == 0 )       $form->removeElement( 'post_country' );
			if ( $gbook[ 'guestbook_use_websiterating' ] == 0 ) $form->removeElement( 'post_websiterating' );

			// delete 'active' element. this is for administration only
			$form->removeElement( 'post_active' );

			// add captcha element
			$form->addElement( 'captcha', 'secur', _('Security code') );

			// add send button
			$form->addElement( 'button', 'send', _('Send') );

			// set default
			$form->setDefault( 'post_website', 'http://' );

			return $form;
		}
    }




    class YDCMGuestbook_posts extends YDCMComponent{
    
        function YDCMGuestbook_posts() {

			// init parent
            $this->YDCMComponent( 'YDCMGuestbook_posts' );

            // define custom key
			$this->registerKey( 'post_id', false );

            // define fields
			$this->registerField( 'post_name',          false, 'text',     _('Name') );
			$this->registerField( 'post_email',         false, 'text',     _('Email') );
			$this->registerField( 'post_message',       false, 'bbtextarea', _('Message') );
			$this->registerField( 'post_active',        false, 'checkbox', _('Active') );
			$this->registerField( 'post_website',       false, 'text',     _('Website') );
			$this->registerField( 'post_ip',            false );
			$this->registerField( 'post_country',       false, 'select',   _('Country') );
			$this->registerField( 'post_websiterating', false, 'select',   _('Website rating') );
		}

		
		function getLatest(){
		
			$this->resetAll();
	
			$this->limit( 5 );
			$this->findAll();

			return $this->getResults( false, false, false, false );

		}


		function getElementsAsRecordSet( $id, $rows ){

			$page = isset( $_GET['page'] ) ? intval( $_GET['page'] ) : 1;

			return new YDRecordSet( $this->getElements( $id ), $page, $rows );
		}

    }





?>