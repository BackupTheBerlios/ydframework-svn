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
		function create( $title, $language_id = 'all', $reference = '', $parent_id = 0 ){
		
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



    class YDCMHelpdesk_posts extends YDCMComponent {
    
        function YDCMHelpdesk_posts() {
        
			// init component
            $this->YDCMComponent( 'YDCMHelpdesk_posts', false );

			// register Key
			$this->registerKey( 'post_id', true );

			// register custom fields
			$this->registerField( 'component_id' );
			$this->registerField( 'user_id' );
			$this->registerField( 'subject' );
			$this->registerField( 'localization' );
			$this->registerField( 'urgency_id' );
			$this->registerField( 'state_id' );
			$this->registerField( 'text' );
			$this->registerField( 'created_in' );
			$this->registerField( 'reported_in' );
			$this->registerField( 'reported_by' );
			$this->registerField( 'reported_to_in' );
			$this->registerField( 'reported_to' );
			$this->registerField( 'reported_to_local' );

			// custom relation
            $rel = & $this->registerRelation( 'YDCMHelpdesk', false, 'YDCMHelpdesk' );
			$rel->setLocalKey( 'component_id' );
            $rel->setForeignKey( 'component_id' );

            $relUsers = & $this->registerRelation( 'YDCMUsers', false, 'YDCMUsers' );
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
		}

	}



    class YDCMHelpdesk_response extends YDCMComponent {
    
        function YDCMHelpdesk_response() {
        
			// init component
            $this->YDCMComponent( 'YDCMHelpdesk_response', false );

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

	}	



    class YDCMHelpdesk_urgency extends YDCMComponent {
    
        function YDCMHelpdesk_urgency() {
        
			// init component
            $this->YDCMComponent( 'YDCMHelpdesk_urgency', false );

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

	}	




    class YDCMHelpdesk_state extends YDCMComponent {
    
        function YDCMHelpdesk_state() {
        
			// init component
            $this->YDCMComponent( 'YDCMHelpdesk_state', false );

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

	}	






?>