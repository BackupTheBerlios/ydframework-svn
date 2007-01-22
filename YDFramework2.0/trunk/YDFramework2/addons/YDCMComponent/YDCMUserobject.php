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


	// add YD libs
	YDInclude( 'YDDatabaseTree3.php' );

    /**
     *  @ingroup YDCMComponent
     */
    class YDCMUserobject extends YDDatabaseTree3 {
    
        function YDCMUserobject() {

			// init parent object
			$this->YDDatabaseTree3( 'YDCMUserobject', 'default', 'userobject_id', 'parent_id', 'lineage', 'level', 'position' );

			// add custom tree fields
			$this->registerField( 'type' );
			$this->registerField( 'reference' );			
			$this->registerField( 'state' );

			// define tree order
			$this->order( 'parent_id ASC, position ASC' );
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
			$this->resetValues();

			// get parent_id of this userobject
			$this->set( 'userobject_id', intval( $userobject_id ) );

			// check if user is valid
			if ( $this->find() != 1 ) return false;

			// save parent_id for future use
			$parent_id = $this->get( 'parent_id ' );

			// reset all previous class values
			$this->resetValues();

			// update all children to new parent_id
			$this->set( 'parent_id', intval( $parent_id ) );
			$this->where( 'parent_id', $userobject_id );

			// update node
			return $this->update();
		}


        /**
         *  This method returns an associative array of nodes that belogs to a specific type
         *
         *  @param $type       Node type (or array of types)
         *  @param $attributes (Optional) Attribute name or (array of attributes) to get only
         *
         *  @returns    Associative array
         */
		function getElements( $type, $attributes = array() ){

			// reset previous settings
			$this->resetValues();

			// set user id
			$this->where( 'type IN (' . $this->_db->escapeSqlArray( $type ) . ')' );

			// get all attributes
			$this->find();

			return $this->_getTreeElementsAsAssocArray( $attributes );
		}


    }
?>