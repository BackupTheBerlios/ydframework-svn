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

    YDInclude( 'YDDatabaseObject.php' );


    class YDCMComponent extends YDDatabaseObject {

        function YDCMComponent( $name, $standardComponent = true ) {

			// init DB object
            $this->YDDatabaseObject();

            $this->_author = 'unknown author';
            $this->_version = 'unknown version';
            $this->_copyright = 'no copyright';
            $this->_description = 'no description';

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( $name );

			// set standard component
			$this->standardComponent = $standardComponent;

			// if this is a standard component has primary key 'component_id' and 2 standard relations
			if ( $standardComponent ){

				// register standard Key
				$this->registerKey( 'component_id', true );

				// register tree field and relation
				$this->registerField( 'content_id' );
	            $relTree = & $this->registerRelation( 'YDCMTree', false, 'YDCMTree' );
				$relTree->setLocalKey( 'content_id' );
            	$relTree->setForeignKey( 'content_id' );

				// register language field and relation
				$this->registerField( 'language_id' );
         		$relLanguage = & $this->registerRelation( 'YDCMLanguages', false, 'YDCMLanguages' );
				$relLanguage->setLocalKey( 'language_id' );
				$relLanguage->setForeignKey( 'language_id' );
			}
				
			// a standard component has tree operations
			// TODO: position is required and we must change order from 'parent_id' to 'parent_id ASC, position ASC'
			$this->tree = new YDDatabaseTree( 'default', 'YDCMTree', 'content_id', 'parent_id', 'parent_id' );

			// TODO: set sort by parent & position
			// $this->tree->setSortField( 'parent_id ASC, position ASC' );

			// add tree fields
			$this->tree->addField( 'type' );
			$this->tree->addField( 'state' );
			$this->tree->addField( 'reference' );
			$this->tree->addField( 'access' );
			$this->tree->addField( 'searcheable' );
			$this->tree->addField( 'published_date_start' );
			$this->tree->addField( 'published_date_end' );
			$this->tree->addField( 'candrag' );
			$this->tree->addField( 'candrop' );
		}


        /**
         *  This function returns all elements (except root)
         *
         *  @returns    all tree elements 
         */
		function getTreeElements(){
			return $this->tree->getDescendants( 1 );
		}


        /**
         *  This function checks if elements are valid drag&dropable
         *
         *  @param $x  Id of dragable node
         *
         *  @param $y  Id of dropable node

         *  @returns    false if elements are invalid, an associative array with node types (eg: array( $x => 'PHCMPage', $y => 'PHCMRootmenu ))'
         */
		function getDragDropElements( $x, $y ){
		
			$treeObj = new YDCMTree();
			return $treeObj->getDragDropElements( $x, $y );
		}


        /**
         *  This function returns the component author
         *
         *  @returns    component author
         */
		function getAuthor(){
			return $this->_author;
		}


        /**
         *  This function returns the component version
         *
         *  @returns    component version
         */
		function getVersion(){
			return $this->_version;
		}


        /**
         *  This function returns the component copyright
         *
         *  @returns    component copyright
         */
		function getCopyright(){
			return $this->_copyright;
		}


        /**
         *  This function returns the component description
         *
         *  @returns    component description
         */
		function getDescription(){
			return $this->_description;
		}


        /**
         *  This function changes component state
         *
         *  @param $id  The node id
         *
         *  @returns    true if state changed, false otherwise
         *
         */
		function toogleState( $id ){

			// get node values
			$old_node = $this->tree->getNode( $id );

			// change state attribute
			if ( $old_node[ 'state' ] == 0 ) return $this->setState( $id, 1 );
			else                             return $this->setState( $id, 0 );
		}


        /**
         *  This function sets a node state
         *
         *  @param $id         The node id
         *  @param $state      The state code
         *
         *  @returns    1 if state changed, 0 otherwise
         */
		function setState( $id, $state ){
		
			return $this->tree->updateNode( array( 'state' => $state ), $id );
		}


        /**
         *  This function deletes the tree part of a component only
         *
         *  @param $id  The node id
         *
         */
		function deleteNode( $id ){
		
			return $this->tree->deleteNode( intval( $id ) );
		}


        /**
         *  This function adds a standard node in the tree. Experimental
         *
         *  @param $values  Node values
         */
		function addNode( $values, $parent_id = null ){

			// user YDDatabasetree method
			return $this->tree->addNode( $values, $parent_id );
		}


        /**
         *  This function updates a node
         *
         *  @param $values  Node values
         */
		function updateNode( $values, $node_id = null ){

			// user YDDatabasetree method
			return $this->tree->updateNode( $values, $node_id );
		}


        /**
         *  This function moves a node
         *
         *  @param $values  Node values
         */
		function moveNode( $x, $y ){

			// use YDDatabasetree method
			return $this->tree->moveNode( $x, $y );
		}


    }




    class YDCMTree extends YDDatabaseObject {
    
        function YDCMTree() {
        
			// init component as non default
            $this->YDDatabaseObject();
			
			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMTree' );

            // register custom key
            $this->registerKey( 'content_id', true );

			// register fields	
			$this->registerField( 'parent_id' );
			$this->registerField( 'type' );
			$this->registerField( 'state' );
			$this->registerField( 'reference' );
			$this->registerField( 'access' );			
			$this->registerField( 'searcheable' );			
			$this->registerField( 'published_date_start' );
			$this->registerField( 'published_date_end' );			
			$this->registerField( 'candrag' );
			$this->registerField( 'candrop' );
		}


        /**
         *  This function checks if elements are valid drag&dropable
         *
         *  @param $x  Id of dragable node
         *
         *  @param $y  Id of dropable node

         *  @returns    false if elements are invalid, an associative array with node information
         */
		function getDragDropElements( $x, $y ){
		
			// check if elements are numeric
			if ( !is_numeric( $x ) || !is_numeric( $y ) ) return false;
		
			// reset old values and get current table	
			$this->resetValues();

			// set custom where to get those 2 elements
			$this->where( '((content_id = ' . intval( $x ) . ' AND candrag = 1) OR (content_id = ' . intval( $y ) . ' AND candrop = 1))' );

			// we must get always 2 elements otherwise they were not valid
			if ( $this->find() != 2 ) return false;

			// return associative array
			return $this->getResultsAsAssocArray( 'content_id' );
		}


	}

?>