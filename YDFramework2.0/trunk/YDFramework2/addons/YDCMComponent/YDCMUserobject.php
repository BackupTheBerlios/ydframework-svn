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


	// add YD libs
//	YDInclude( 'YDForm.php' );
	YDInclude( 'YDDatabaseTree.php' );
//	YDInclude( 'YDDatabaseObject.php' );
	// add YDCM libs
//	YDInclude( 'YDCMPermissions.php' );

	// add local translation directory
//	YDLocale::addDirectory( dirname( __FILE__ ) . '/languages/YDCMUsers/' );


    class YDCMUserobject extends YDDatabaseObject {
    
        function YDCMUserobject() {
        
			// init DB object
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMUserobject' );

            // register custom key
            $this->registerKey( 'userobject_id', true );

			// register custom fields
			$this->registerField( 'parent_id' );
			$this->registerField( 'nleft' );
			$this->registerField( 'nright' );
			$this->registerField( 'nlevel' );
			$this->registerField( 'position' );
			$this->registerField( 'type' );			
			$this->registerField( 'reference' );			
			$this->registerField( 'state' );

			// create tree object
			$this->tree = new YDDatabaseTree( 'default', 'YDCMUserobject', 'userobject_id', 'parent_id', 'parent_id' );

			// add tree fields
			$this->tree->addField( 'parent_id' );
			$this->tree->addField( 'type' );
			$this->tree->addField( 'reference' );			
			$this->tree->addField( 'state' );			
		}


        /**
         *  This method returns all sub users of a userobject
         *
         *  @param  $parent_id      Parent id
         *  @param  $includeNode    (optional) Includes the node or not. Defaults to true.
         *
         *  @returns    Array with user and children nodes
         */
		function getTreeElements( $parent_id, $includeNode = true ){

			return $this->tree->getDescendants( $parent_id, $includeNode );
		}


        /**
         *  This method checks if a userobject is descendant of another
         *
         *  @param      $userobject_id User id to test if id descendant
         *  @param      $parent_id     Parent id
         *
         *  @returns    boolean. TRUE if is descendant, FALSE if not descendant
         */
		function isDescendantOf( $userobject_id, $parent_id ){

			return $this->tree->isDescendantOf( intval( $userobject_id ), intval( $parent_id ) );
		}


        /**
         *  This method deletes a userobject (and all children) or just the children
         *
         *  @param $userobject_id  Userobject id
         *  @param $includeParent  (Optional) Boolean TRUE (default) deletes userobject id and children, FALSE deletes children only
         *
         *  @returns    TRUE if deleted, ARRAY with form errors otherwise
         */
		function deleteUser( $userobject_id, $includeParent = true ){
		
			// delete node from users table
			$this->tree->deleteNode( $userobject_id, $includeParent );
		}


        /**
         *  This method adds a userobject
         *
         *  @param $values     Array with values
         *  @param $parent_id  Parent id
         *
         *  @returns    node id
         */
		function addNode( $values, $parent_id ){
		
			// add node
			return $this->tree->addNode( $values, $parent_id );
		}


        /**
         *  This method updates a userobject
         *
         *  @param $values  Array with values
         *  @param $id      Userobject id
         *
         *  @returns    $values
         */
		function updateNode( $values, $id ){
		
			// update node
			return $this->tree->updateNode( $values, $id );
		}


        /**
         *  This method moves all direct children of a node up
         *
         *  @param $userobject_id  Userobject id
         *
         *  @returns    TRUE, FALSE  TODO: convert to YDResult
         */
		function moveChildrenUp( $userobject_id ){
		
			// reset possible old stuff
			$this->resetAll();

			// get parent_id of this userobject
			$this->set( 'userobject_id', intval( $userobject_id ) );

			// check if user is valid
			if ( $this->find() != 1 ) return false;

			// save parent_id for future use
			$parent_id = $this->get( 'parent_id ' );

			// reset all previous class values
			$this->resetAll();

			// update all children to new parent_id
			$this->set( 'parent_id', intval( $parent_id ) );
			$this->where( 'parent_id', $userobject_id );

			// update node
			return $this->update();
		}


        /**
         *  This method returns an associative array 
         *
         *  @param $type       Node type
         *  @param $attribute  (Optional) Attribute name or (array of attributes) to get only
         *
         *  @returns    Associative array
         */
		function getElements( $type, $attributes = array() ){

			$this->resetAll();

			// set user id
			$this->set( 'type', $type );

			// get all attributes
			$this->find();

			return $this->getResultsAsAssocArray( 'userobject_id', $attributes );
		}


    }
?>