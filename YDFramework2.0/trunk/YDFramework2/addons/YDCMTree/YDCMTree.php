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

	YDInclude( 'YDDatabaseTree.php' );
    YDInclude( 'YDDatabaseObject.php' );

	// dbobject will delete records even if there are NO conditions ('where' sql conditions)
	YDConfig::set( 'YD_DBOBJECT_DELETE', true );

    class YDCMTree extends YDDatabaseObject {
    
        function YDCMTree() {
        
			// init component as non default
            $this->YDDatabaseObject();
			
			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMTree' );

			// set component details
            $this->_author = 'Francisco Azevedo';
            $this->_version = '0.1';
            $this->_copyright = '(c) Copyright 2006 Francisco Azevedo';
            $this->_description = 'This CM component manages the content tree';

            // register custom key
            $this->registerKey( 'content_id', true );

			// register fields	
			$this->registerField( 'parent_id' );
			$this->registerField( 'nleft' );
			$this->registerField( 'nright' );
			$this->registerField( 'nlevel' );
			$this->registerField( 'position' );
			$this->registerField( 'type' );
			$this->registerField( 'reference' );
			$this->registerField( 'state' );
			$this->registerField( 'access' );			
			$this->registerField( 'searcheable' );			
			$this->registerField( 'published_date_start' );
			$this->registerField( 'published_date_end' );			

			// setup tree
			// TODO: position is required and we must change order from 'parent_id' to 'parent_id ASC, position ASC'
			$this->tree = new YDDatabaseTree( 'default', 'YDCMTree', 'content_id', 'parent_id', 'parent_id' );
		
			// add tree fields
			$this->tree->addField( 'type' );
			$this->tree->addField( 'reference' );
			$this->tree->addField( 'state' );

			// we don't have relations from here (just TO here, and we don't need to define that)
		}


        /**
         *  This function deletes a tree node
         *
         *  @param $elementID  The node id
         */
		function deleteNode( $elementID ){

			// delete node and all children of this node
			// TODO: maybe we could return the nodes deleted
			return $this->tree->deleteNode( intval( $elementID ) );
		}
		
		
        /**
         *  This function return tree element (except the root)
         *
         *  @returns    Array of elements
         */
		function getElements(){
		
			return $this->tree->getDescendants( 1 );
		}
		

        /**
         *  This function inverts a state
         *
         *  @param $id  The node id
         *
         *  @returns    true if state changed, false otherwise
         */
		function toogleState( $id ){
		
			$this->resetValues();

			// sets element id
			$this->content_id = intval( $id );

			// search element id and assign values
			$this->find();

			// change state attribute
			if ( $this->state == 0 ) $this->state = 1;
			else                     $this->state = 0;

			// update id with new values an return
			if ( $this->update() == 1 ) return true;
			
			return false;
		}


        /**
         *  This function sets a node state
         *
         *  @param $elementID  The node id
         *  @param $state      The state code
         *
         *  @returns    1 if state changed, 0 otherwise
         */
		function setState( $elementID, $state ){
		
			$this->resetValues();

			// set id and state
			$this->content_id = intval( $elementID );
			$this->state = intval( $state );

			// update values
			return $this->update();
		}
		
    }
?>