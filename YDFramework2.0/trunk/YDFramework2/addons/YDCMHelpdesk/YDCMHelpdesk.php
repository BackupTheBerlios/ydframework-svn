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


	YDInclude( 'YDForm.php' );
	YDInclude( 'YDCMComponent.php' );

	// load master component modules
	YDCMComponent::module( 'YDCMUsers' );

	// add local translation directory
	YDLocale::addDirectory( dirname( __FILE__ ) . '/languages/' );

	// set posts form name
	YDConfig::set( 'YDCMHELPDESK_FORMPOST', 'YDCMHelpdeskFormPost', false );


    class YDCMHelpdesk extends YDCMComponent {
    
        function YDCMHelpdesk() {
        
			// init component
            $this->YDCMComponent( 'YDCMHelpdesk' );

            $this->_author = 'Francisco Azevedo';
            $this->_version = '0.1';
            $this->_copyright = '(c) Copyright 2006 Francisco Azevedo';
            $this->_description = 'This is a simple helpdesk component';

			// register custom fields
			$this->registerField( 'title' );
						
			// custom relation
            $rel = & $this->registerRelation( 'YDCMHelpdesk_posts', false, 'YDCMHelpdesk_posts' );
			$rel->setLocalKey( 'component_id' );
            $rel->setForeignKey( 'component_id' );
			$rel->setForeignJoin( 'LEFT' );
		}


        /**
         *  This function creates a new helpdesk
         *
         *  @param $title  Helpdesk title
         *  @param $language_id  (Optional) Language code of the heldesk
         *  @param $reference  (Optional) Node reference
         *  @param $parent_id  (Optional) Id of the parent help desk
         */
		function addHelpdesk( $title, $language_id = 'all', $reference = '', $parent_id = 0 ){
		
			// add node to content tree
			$node = array();
			$node[ 'type' ]        = 'YDCMHelpdesk';
			$node[ 'reference' ]   = $reference;
			$node[ 'state' ]       = 1;
			$node[ 'access' ]      = 1;
			$node[ 'searcheable' ] = 0;

			// add tree node
			// TODO: check if parent_id exist
			$id = $this->addNode( $node, intval( $parent_id ) );
		
			// add a standard title
			return $this->addTitle( $id, $title, $language_id );
		}

		
		function addTitle( $id, $title, $language_id ){
		
			// reset values
			$this->resetValues();

			// add a default helpdesk title
			// TODO: check if language code exist
			$this->content_id  = $id;
			$this->title       = $title;
			$this->language_id = $language_id;

			// insert values
			return $this->insert();
		}

    }



    class YDCMHelpdesk_posts extends YDDatabaseObject {
    
        function YDCMHelpdesk_posts() {
        
			// init component
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMHelpdesk_posts' );

			// register Key
			$this->registerKey( 'post_id', true );

			// register custom fields
			$this->registerField( 'component_id' );
			$this->registerField( 'user_id' );
			$this->registerField( 'subject' );
			$this->registerField( 'urgency_id' );
			$this->registerField( 'state_id' );
			$this->registerField( 'text' );
			$this->registerField( 'creation_user' );
			$this->registerField( 'creation_date' );
			$this->registerField( 'reported_by_user' );
			$this->registerField( 'reported_by_type' );
			$this->registerField( 'reported_by_local' );
			$this->registerField( 'reported_by_date' );
			$this->registerField( 'assignedto_user' );
			$this->registerField( 'assignedto_type' );
			$this->registerField( 'assignedto_local' );
			$this->registerField( 'assignedto_date' );

			// custom relation
            $rel = & $this->registerRelation( 'YDCMHelpdesk', false, 'YDCMHelpdesk' );
			$rel->setLocalKey( 'component_id' );
            $rel->setForeignKey( 'component_id' );

            $relUsers = & $this->registerRelation( 'users', false, 'YDCMUsers' );
			$relUsers->setLocalKey( 'user_id' );
            $relUsers->setForeignKey( 'user_id' );

            $relResponse = & $this->registerRelation( 'YDCMHelpdesk_response', false, 'YDCMHelpdesk_response' );
			$relResponse->setLocalKey( 'post_id' );
            $relResponse->setForeignKey( 'post_id' );
			$relResponse->setForeignJoin( 'LEFT' );

            $relUrgency = & $this->registerRelation( 'YDCMHelpdesk_urgency', false, 'YDCMHelpdesk_urgency' );
			$relUrgency->setLocalKey( 'urgency_id' );
            $relUrgency->setForeignKey( 'urgency_id' );

            $relState = & $this->registerRelation( 'YDCMHelpdesk_state', false, 'YDCMHelpdesk_state' );
			$relState->setLocalKey( 'state_id' );
            $relState->setForeignKey( 'state_id' );

            $relAss = & $this->registerRelation( 'YDCMHelpdesk_type', false, 'YDCMHelpdesk_type' );
			$relAss->setLocalKey( 'reported_by_type' );
            $relAss->setForeignKey( 'type_id' );
		}


        /**
         *  This function returns a form for posts
         */
		function getFormPost(){
		
			// create a form for new posts
            $form = new YDForm( YDConfig::get( 'YDCMHELPDESK_FORMPOST' ) );

			// get urgencies
			$urgencies = new YDCMHelpdesk_urgency();
			$urgencies = $urgencies->getUrgencies();
			
			// get states
			$states    = new YDCMHelpdesk_state();
			$states    = $states->getStates();

			// get types
			$types     = new YDCMHelpdesk_type();
			$types     = $types->getTypes();

			// add new form elements
            $form->addElement( 'text',			'subject',			 t('ticket_subject'),           array('size' => 50) );
            $form->addElement( 'select', 		'urgency_id',		 t('ticket_urgency_id'),        array(), $urgencies );
            $form->addElement( 'select',		'state_id',			 t('ticket_state_id'),          array(), $states );
            $form->addElement( 'textarea',		'text',              t('ticket_text'),              array('cols' => 60, 'rows' => 12) );

            $form->addElement( 'span',			'creation_user',	 t('ticket_creation_user') );
            $form->addElement( 'datetimeselect','creation_date',     t('ticket_creation_date') );

            $form->addElement( 'span',			'reported_by_user',	 t('ticket_reported_by_user'),  array('size' => 50) );
            $form->addElement( 'select', 		'reported_by_type',	 t('ticket_reported_by_type'),  array(), $types );
            $form->addElement( 'text',			'reported_by_local', t('ticket_reported_by_local') );
            $form->addElement( 'datetimeselect','reported_by_date',	 t('ticket_reported_by_date') );
  
            $form->addElement( 'span',          'assignedto_user',	 t('ticket_assignedto_user') );
            $form->addElement( 'text',			'assignedto_type',	 t('ticket_assignedto_type'),   array('size' => 50) );
            $form->addElement( 'select', 		'assignedto_local',	 t('ticket_assignedto_local'),  array(), $types );
            $form->addElement( 'text',			'assignedto_date',   t('ticket_assignedto_date') );
		
			return $form;
		}


        /**
         *  This function adds a new post
         *
         *  @param $helpdesk_id  helpdesk id of this post
         *  @param $user_id  User id
         *  @param $values  Array of values
         */
		function addPost( $helpdesk_id, $user_id, $values ){
		
			$this->resetValues();
		
			// add required fields
			// TODO: check if user, urgency and state exist
			$this->component_id = $helpdesk_id;
			$this->user_id      = $user_id;
			$this->urgency_id   = $values['urgency_id'];
			$this->state_id     = $values['state_id'];


			// add optional fields
			if ( isset( $values['subject'] ) )           $this->subject           = $values['subject'];
			if ( isset( $values['localization'] ) )      $this->localization      = $values['localization'];
			if ( isset( $values['text'] ) )              $this->text              = $values['text'];
			if ( isset( $values['created_in'] ) )        $this->created_in        = $values['created_in'];
			if ( isset( $values['reported_in'] ) )       $this->reported_in       = $values['reported_in'];
			if ( isset( $values['reported_by'] ) )       $this->reported_by       = $values['reported_by'];
			if ( isset( $values['reported_to_in'] ) )    $this->reported_to_in    = $values['reported_to_in'];
			if ( isset( $values['reported_to'] ) )       $this->reported_to       = $values['reported_to'];
			if ( isset( $values['reported_to_local'] ) ) $this->reported_to_local = $values['reported_to_local'];
			
			return $this->insert();
		}
		

        /**
         *  This function just deletes a post given its id
         *
         *  @param $post_id  Post id to delete
         */
		function deletePost( $post_id ){
		
			$this->resetValues();
			
			$this->post_id = intval( $post_id );
			
			return $this->delete();
		}


        /**
         *  This function deletes all posts of a helpdesk
         *
         *  @param $helpdesk_id  Helpdesk id
         */
		function deleteAll( $helpdesk_id ){
		
			$this->resetValues();
			
			$this->component_id = intval( $helpdesk_id );
			
			return $this->delete();
		}


        /**
         *  This function deletes all posts using a parent id
         *
         *  @param $parent_id  parent id
         */
		function deleteParent( $parent_id ){
		
			$this->resetValues();
			
			$this->parent_id = intval( $parent_id );
			
			return $this->delete();
		}


        /**
         *  This function returns a special recordset with posts
         *
         *  @param $field_order  Field to sort
         *  @param $field_order_direction  Sort direction
         *  @param $field_page  Page to retrieve
         */
		function getRS( $field_order = 'creation_date', $field_order_direction = 'DESC', $field_page = 1 ){

			// delete previous values
			$this->resetValues();

			// set current order
			$this->order( $this->getTable() . '.' . $field_order . ' ' . $field_order_direction . ', creation_date DESC' );

			// get only posts with state 1
			$this->state_id = 1;

			// get information of users too
			$this->find( 'users' );

			// define columns we want
			$fields = array( 'post_id', 'subject', 'name', 'creation_date' );

			// create a recordset
			$recordset = new YDRecordSet( $this->getResultsAsAssocArray( 'post_id', $fields, false, false, false, false ), $field_page, 9 );
			$recordset->setFields( $fields );
			$recordset->setCurrentField( $field_order );

			return $recordset;
		}

	}



    class YDCMHelpdesk_response extends YDDatabaseObject {
    
        function YDCMHelpdesk_response() {
        
			// init component
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMHelpdesk_response' );

			// register Key
			$this->registerKey( 'response_id', true );

			// register custom fields
			$this->registerField( 'post_id' );
			$this->registerField( 'user_id' );
			$this->registerField( 'date' );
			$this->registerField( 'description' );

			// custom relations
            $rel = & $this->registerRelation( 'YDCMHelpdesk_posts', false, 'YDCMHelpdesk_posts' );
			$rel->setLocalKey( 'post_id' );
            $rel->setForeignKey( 'post_id' );

            $relUsers = & $this->registerRelation( 'YDCMUsers', false, 'YDCMUsers' );
			$relUsers->setLocalKey( 'user_id' );
            $relUsers->setForeignKey( 'user_id' );
		}



		function addResponse( $post_id, $user_id, $message ){
		
			YDInclude( 'YDUtil.php' );

			$this->resetValues();
		
			// create response
			// TODO: check if post_id and user_id are valid
			$this->post_id     = intval( $post_id );
			$this->user_id     = intval( $user_id );
			$this->date        = YDStringUtil::formatDate( time(), 'datetimesql' );
			$this->description = $message;

			return $this->insert();
		}
		
		
	}	



    class YDCMHelpdesk_urgency extends YDDatabaseObject {
    
        function YDCMHelpdesk_urgency() {
        
			// init component
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMHelpdesk_urgency' );

			// register Key
			$this->registerKey( 'urgency_id', true );

			// register custom fields
			$this->registerField( 'description' );
			$this->registerField( 'color' );
						
			// custom relations
            $rel = & $this->registerRelation( 'YDCMHelpdesk_posts', false, 'YDCMHelpdesk_posts' );
			$rel->setLocalKey( 'urgency_id' );
            $rel->setForeignKey( 'urgency_id' );
			$rel->setForeignJoin( 'LEFT' );
		}


		function addUrgency( $description, $color = '#FF0000' ){
		
			$this->resetValues();

			// create urgency
			$this->description  = $description;
			$this->color        = $color;

			return $this->insert();
		}


		function getUrgencies(){
		
			$this->resetValues();
			
			// find all urgencies
			$this->find();

			// get associative array
			$res = $this->getResultsAsAssocArray( 'urgency_id' );

			// translate urgency description
			foreach( $res as $id => $arr )
				$res[ $id ][ 'description'] = t( $res[ $id ][ 'description'] );

			// return urgencies
			return $res;
		}

	}	




    class YDCMHelpdesk_state extends YDDatabaseObject {
    
        function YDCMHelpdesk_state() {
        
			// init component
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMHelpdesk_state' );

			// register Key
			$this->registerKey( 'state_id', true );

			// register custom fields
			$this->registerField( 'description' );
						
			// custom relations
            $rel = & $this->registerRelation( 'YDCMHelpdesk_posts', false, 'YDCMHelpdesk_posts' );
			$rel->setLocalKey( 'state_id' );
            $rel->setForeignKey( 'state_id' );
			$rel->setForeignJoin( 'LEFT' );
		}


		function addState( $description ){
		
			$this->resetValues();

			// create state
			$this->description  = $description;

			return $this->insert();
		}


		function getStates(){
		
			$this->resetValues();
			
			// find all states
			$this->find();

			// get associative array
			$res = $this->getResultsAsAssocArray( 'state_id' );

			// translate state description
			foreach( $res as $id => $arr )
				$res[ $id ][ 'description'] = t( $res[ $id ][ 'description'] );

			// return urgencies
			return $res;
		}


	}	





    class YDCMHelpdesk_type extends YDDatabaseObject {
    
        function YDCMHelpdesk_type() {
        
			// init component
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMHelpdesk_type' );

			// register Key
			$this->registerKey( 'type_id', true );

			// register custom fields
			$this->registerField( 'description' );
						
			// custom relations
		}

	}	


?>