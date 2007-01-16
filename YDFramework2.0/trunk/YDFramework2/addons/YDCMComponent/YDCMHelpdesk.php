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

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }


	YDInclude( 'YDForm.php' );
	YDInclude( 'YDCMComponent.php' );

	// load users module of YDCMComponent
	YDCMComponent::module( 'YDCMUsers' );

	// add local translation directory
	YDLocale::addDirectory( dirname( __FILE__ ) . '/languages/' );

	// set posts form name
	YDConfig::set( 'YDCMHELPDESK_FORMPOST', 'YDCMHelpdeskFormPost', false );

	// set default states
	YDConfig::set( 'YDCMHELPDESK_STATES', array( 'allstates', 'closed', 'open', 'pendent' ), false );

	// set default types
	YDConfig::set( 'YDCMHELPDESK_TYPES', array( 'email', 'telephone', 'personal' ), false );

    // set default urgencies (TODO: maybe 'priorities')
    YDConfig::set( 'YDCMHELPDESK_URGENCIES', array( 'allurgencies' => array(),
	                                                'low'          => array( 'color' => '#00FF00' ), 
                                                    'medium'       => array( 'color' => '#FF9900' ), 
													'high'         => array( 'color' => '#FF9900' ) ), false );


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


        /**
         *  This static function returns an array of possible states
         *
         *  @param $translated  (Optional) boolean that defines if result uses translation strings
         */
		function getStates( $translated = false ){
		
			return YDCMHelpdesk::getInfo( $translated, YDConfig::get( 'YDCMHELPDESK_STATES' ) );
		}


        /**
         *  This static function returns an array of possible urgencies
         *
         *  @param $translated  (Optional) boolean that defines if result uses translation strings
         */
		function getUrgencies( $translated = false ){
		
			return YDCMHelpdesk::getInfo( $translated, array_keys( YDConfig::get( 'YDCMHELPDESK_URGENCIES' ) ) );
		}
		
		
        /**
         *  This private static function returns an array/associative array based on states/urgencies
         *
         *  @param  $translated Indicates if we need translations or not.
         *  @param  $arr        Array with the states.
         *
         *  @param $translated  (Optional) boolean that defines if result uses translation strings
         */
		function getInfo( $translated, $arr ){
			
			// if we don't want translation, just return
			if ( $translated != false ) return $arr;
			
			// compute associative array
			$tmp = array();
			foreach( $states as $s )
				$tmp[ $s ] = t( $s );

			return $tmp;
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
			$this->registerField( 'creation_date' );
			$this->registerField( 'reportedby_user' );
			$this->registerField( 'reportedby_type' );
			$this->registerField( 'reportedby_local' );
			$this->registerField( 'reportedby_date' );
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

			// set default columns information
			$this->columns = array(	'post_id'       => array('sortable' => true), 
									'subject'       => array('sortable' => true),
									'name'          => array('sortable' => true), 
									'creation_date' => array('sortable' => true) );
		}


        /**
         *  This function defines columns to use in recordsets/ajaxgrids
         *
         *  @param $columns  Helpdesk post columns for recorsets/ajaxgrids
         */
		function setColumns( $columns ){
		
			$this->columns = $columns;
		}


        /**
         *  This function returns a form for posts
         */
		function getFormPost(){
		
			// create a form for new posts
            $form = new YDForm( YDConfig::get( 'YDCMHELPDESK_FORMPOST' ) );

			// get urgencies, states and types
			$urgencies = array_keys( YDConfig::get( 'YDCMHELPDESK_URGENCIES' ) );
			$states    = YDConfig::get( 'YDCMHELPDESK_STATES' );
			$types     = YDConfig::get( 'YDCMHELPDESK_TYPES' );

			// delete 'allstates' and 'allurgencies' from states and types
			array_shift( $urgencies );
			array_shift( $states );

			// add new form elements
            $form->addElement( 'text',			'subject',			 t('ticket_subject'),           array('size' => 50) );
            $form->addElement( 'select', 		'urgency_id',		 t('ticket_urgency_id'),        array(), $urgencies );
            $form->addElement( 'select',		'state_id',			 t('ticket_state_id'),          array(), $states );
            $form->addElement( 'textarea',		'text',              t('ticket_text'),              array('cols' => 80, 'rows' => 15) );

            $form->addElement( 'span',			'creation_user',	 t('ticket_creation_user') );
            $form->addElement( 'datetimeselect','creation_date',     t('ticket_creation_date') );

            $form->addElement( 'text',			'reportedby_user',	 t('ticket_reportedby_user'),  array('size' => 50) );
            $form->addElement( 'select', 		'reportedby_type',	 t('ticket_reportedby_type'),  array(), $types );
            $form->addElement( 'text',			'reportedby_local',  t('ticket_reportedby_local') );
            $form->addElement( 'datetimeselect','reportedby_date',	 t('ticket_reportedby_date') );
  
            $form->addElement( 'text',          'assignedto_user',	 t('ticket_assignedto_user'),  array('size' => 50) );
            $form->addElement( 'select',		'assignedto_type',	 t('ticket_assignedto_type'),  array(), $types  );
            $form->addElement( 'text', 		    'assignedto_local',	 t('ticket_assignedto_local') );
            $form->addElement( 'datetimeselect','assignedto_date',   t('ticket_assignedto_date') );

			$form->setDefault( 'urgency_id', 1 );
			$form->setDefault( 'state_id',   1 );

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

			// get YDForm object
			$form = $this->getFormPost();

			// check form validation
			if ( !$form->validate( $values ) )
				return $form->getErrors();

			$this->resetValues();
		
			// add required fields
			// TODO: check if user, urgency and state exist
			$this->setValues( $form->getValues() );

			$this->component_id = $helpdesk_id;
			$this->user_id      = $user_id;

			// parse dates (convert ugly array in a date time sqlformat )
			$this->creation_date   = YDStringUtil::formatDate( $this->creation_date,   'datetimesql' );
			$this->reportedby_date = YDStringUtil::formatDate( $this->reportedby_date, 'datetimesql' );
			$this->assignedto_date = YDStringUtil::formatDate( $this->assignedto_date, 'datetimesql' );

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
         *  This function returns a recordset with posts
         *
         *  @param $fields  Array of fields
         */
		function getRecordSet( $fields ){

			// delete previous values
			$this->resetValues();

			// check column name. TODO: check if column is a valid helpdesk or user column
			if ( isset( $fields[ 'grid_column' ] ) ) $column = $fields[ 'grid_column' ];
			else                                     $column = 'creation_date';

			// check direction
			if ( isset( $fields[ 'grid_direction' ] ) && $fields[ 'grid_direction' ] == 'asc' ) $desc = false;
			else                                                                                $desc = true;

			// apply column order
            $this->order( $column . ' ' . $fields['g'] );

			// apply state
			if ( isset( $fields[ 'grid_state' ] ) && in_array( $fields[ 'grid_state' ], array_keys( YDConfig::get( 'YDCMHELPDESK_STATES' ) ) ) )
				$this->state_id = $fields[ 'grid_state' ];

			// apply urgency
			if ( isset( $fields[ 'grid_urgency' ] ) && in_array( $fields[ 'grid_urgency' ], YDConfig::get( 'YDCMHELPDESK_URGENCIES' ) ) )
				$this->urgency_id = $fields[ 'grid_urgency' ];

			// get information of users too
			$this->find( 'users' );

			// check page to show
			if ( isset( $fields[ 'grid_page' ] ) && is_numeric( $fields[ 'grid_page' ] ) ) $page = intval( $fields[ 'grid_page' ] );
			else                                                                           $page = 1;

			// create a recordset
			return new YDRecordSet( $this->getResultsAsAssocArray( 'post_id', array_keys( $this->columns ), false, false, false, false ), $page, 3 );
		}


        /**
         *  This function returns a ajax grid
         *
         *  @param $fields  Array of fields
         */
		function getAjaxGrid( $fields = array() ){
		
			// include grid lib
			YDInclude( 'YDFormElement_Grid.php' );

			// init grid
			$grid = new YDFormElement_Grid();

			// add recordset to grid
			$grid->setValue( $this->getRecordSet( $fields ) );
			
			// add columns information to grid
			$grid->setColumns( $this->columns );

			return $grid;
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


?>