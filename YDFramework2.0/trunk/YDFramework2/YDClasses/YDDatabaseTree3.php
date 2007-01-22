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

    /**
     *  @addtogroup YDDatabase Core - Database
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

	// add YDF libs needed by this class
	require_once( YD_DIR_HOME_ADD . '/YDDatabaseObject/YDDatabaseObject.php' );


    /** Database scheme example
        
        CREATE TABLE nested_tree (
            id int NOT NULL auto_increment,
            parent_id int NULL,
            lineage varchar(255) NOT NULL default '//',
            level int NOT NULL default '1',
            position int NOT NULL default '1',
            title varchar(255) NOT NULL default '',
            PRIMARY KEY (id),
            FOREIGN KEY (parent_id)
                REFERENCES nested_tree(id)
                     ON DELETE CASCADE
                     ON UPDATE CASCADE
        )TYPE=InnoDB;
    
        Note: root node must have ID 1, PARENT null and LINEAGE '' !
       
        INSERT INTO nested_tree VALUES ( 1, null, '',         0, 1, '');
        INSERT INTO nested_tree VALUES ( 2,    1, '//',       1, 1, 'General Resources');
        INSERT INTO nested_tree VALUES ( 3,    2, '//2/',     2, 1, 'Code Paste');
        INSERT INTO nested_tree VALUES ( 4,    2, '//2/',     2, 2, 'Documentation');
        INSERT INTO nested_tree VALUES ( 5,    2, '//2/',     2, 3, 'Books & Publications');
        INSERT INTO nested_tree VALUES ( 6,    5, '//2/5/',   3, 1, 'Apache');
        INSERT INTO nested_tree VALUES ( 7,    5, '//2/5/',   3, 2, 'PostgreSQL');
        INSERT INTO nested_tree VALUES ( 8,    5, '//2/5/',   3, 3, 'MySQL');
        INSERT INTO nested_tree VALUES ( 9,    2, '//2/',     2, 4, 'Links');
        INSERT INTO nested_tree VALUES (10,    9, '//2/9/',   3, 1, 'Databases');
        INSERT INTO nested_tree VALUES (11,    9, '//2/9/',   3, 2, 'Generators');
        INSERT INTO nested_tree VALUES (12,    9, '//2/9/',   3, 3, 'Portals');
     */

    /**
     *	This is the actual implementation of the lineage tree algorithm but as an YDDatabaseObject
     *
     *  @ingroup YDDatabase
     */
    class YDDatabaseTree3 extends YDDatabaseObject {
    
        function YDDatabaseTree3( $table, $db = 'default', $idField = 'id', $parentField = 'parent_id', $lineageField = 'lineage', $levelField = 'level', $positionField = 'position' ) {
        
			// init DB object
            $this->YDDatabaseObject();

			// register database
            $this->registerDatabase( $db );

			// register table
            $this->registerTable( $table );

			// register reserved fields
			$this->registerKey( $idField, true );
			$this->registerField( $parentField );
			$this->registerField( $lineageField );
			$this->registerField( $levelField );
			$this->registerField( $positionField );

			// save field names for future use
			$this->__id       = $idField;
			$this->__parent   = $parentField;
			$this->__lineage  = $lineageField;
			$this->__level    = $levelField;
			$this->__position = $positionField;

			$this->__table_id       = $table . '.' . $idField;
			$this->__table_parent   = $table . '.' . $parentField;
			$this->__table_lineage  = $table . '.' . $lineageField;
			$this->__table_level    = $table . '.' . $levelField;
			$this->__table_position = $table . '.' . $positionField;


			// define a generic tree order
			$this->setOrder( $this->__table_parent . ' ASC, ' . $this->__table_position . ' ASC' );
		}


        /**
         *  This function defines the order used in all SELECTS
         *
         *  @param $sql  The sql order string.
         */
        function setOrder( $sql ){

			return $this->_tree_order = $sql;
        }


        /**
         *  This function will overide the YDDatabaseObject to reset object but init order
         */
		function resetAll(){
			parent::resetAll();
			$this->order( $this->_tree_order );
		}


        /**
         *  Returns the node level based on the lineage string
         *
         *  @returns  level int value
         */
        function _getLevel( $lineage ) {
			return substr_count( $lineage, '/' ) - 1;
        }


        /**
         *  Fetch the node data for the node identified by $id.
         *
         *  @param $id      The ID of the node to fetch.
         *  @param $field   (Optional) The unique field to select on. Defaults to id, which means that the ID field specified
         *                  when the object was instantiated will be used.
         *  @param $class   (optional) Relation name
         *  @param $prefix  (optional) Adds the relation's vars as prefixes to the keys. Default: false.
         *
         *  @returns An object containing the node's data, or false if node not found
         */
        function getNode( $id, $field = null, $class = null, $prefix = false ) {

			$this->resetAll();

            // get node
            return $this->_getNode( $id, $field, $class, $prefix );
        }


        /**
         *  Helper method to fetch a node.
         *
         *  @param $id      The ID of the node to fetch.
         *  @param $field   (optional) The unique field to select on. Defaults to id, which means that the ID field specified
         *                             when the object was instantiated will be used.
         *  @param $class   (optional) Relation name
         *  @param $prefix  (optional) Adds the relation's vars as prefixes to the keys. Default: true.
         *
         *  @returns An object containing the node's data, or false if node not found
         */
        function _getNode( $id, $field = null, $class = null, $prefix = true ) {

			// if field not defined, field is id
			if ( is_null( $field ) ) $field = $this->__id;
	
			// set local field
			if ( is_null( $class ) ){

				// set field value
				$this->set( $field, $id );
			}else{

				// load relation and set field
				$this->load( $class );
				$this->$class->set( $field, $id );
			}

			// check results
			if ( $this->findAll() == 0 ) return false;

            // Execute the query and return the record
            return $this->getValues( false, false, false, $prefix );
        }


        /**
         *  Fetch the descendants of a node. NOTE: To get all elements use getTreeElements()
         *
         *  @param $id              The ID of the node to fetch descendant data for. 
         *  @param $includeSelf     (optional) Whether or not to include the passed node in results. 
         *  @param $maxLevel        (optional) Max level to retrieve. Eg: 10 returns all descendants with level smaller than 10; NULL retrieve all descendants.
         *  @param $prefix          (optional) Adds the relation's vars as prefixes to the keys. Default: false.
         *
         *  @returns The descendants of the passed now
         */
        function getDescendants( $id, $includeSelf = false, $maxLevel = null, $prefix = false ) {

			// check if we want an invalid id (like 0 or 1)
			if ( $id < 2 ) return $this->getTreeElements();

			$this->resetAll();

			return $this->_getDescendants( $id, $includeSelf, $maxLevel, $prefix );
		}


        /**
         *  Helper to fetch the descendants of a node
         *
         *  @param $id              The ID of the node to fetch descendant data for. 
         *  @param $includeSelf     (optional) Whether or not to include the passed node in results. 
         *  @param $maxLevel        (optional) Max level to retrieve. Eg: 10 returns all descendants with level smaller than 10; NULL retrieve all descendants.
         *  @param $prefix          (optional) Adds the relation's vars as prefixes to the keys. Default: false.
         *
         *  @returns The descendants of id
         */
        function _getDescendants( $id, $includeSelf = false, $maxLevel = null, $prefix = false ) {

            // get just children
			if ( $includeSelf == false ) $this->where(       $this->__table_lineage . ' LIKE "%/' . intval( $id ) . '/%"' );
			else                         $this->where( '(' . $this->__table_lineage . ' LIKE "%/' . intval( $id ) . '/%" OR ' . $this->__table_id . ' = ' . intval( $id ) . ')' );

			// check max level to retrieve
			if ( is_numeric( $maxLevel ) ) $this->where( $this->__table_level . '<' . intval( $maxLevel ) );

			// find nodes
			$this->findAll();

			// return all nodes
			return $this->getResults( false, false, false, $prefix );
		}


        /**
         *  Fetch all elements of a tree
         *
         *  @returns All tree nodes
         */
        function getTreeElements() {

			$this->resetAll();

			return $this->_getTreeElements();
		}


        /**
         *  Helper to fetch all elements of a tree
         *
         *  @returns All tree nodes
         */
        function _getTreeElements() {

			// get all elements except root
			$this->where( $this->__table_id . ' > 1' );

			// find elements
			$this->findAll();

			// return all nodes
			return $this->getResults();
		}


        /**
         *  Helper to fetch all elements of a tree as an assocArray
         *
         *  @param $columns             (Optional) Columns to retrieve. Default: add columns
         *  @param $key                 (Optional) Key to use. Default: current table key
         *
         *  @returns All tree nodes
         */
        function _getTreeElementsAsAssocArray( $columns = array(), $key = null ) {

			// get all elements except root
			$this->where( $this->__table_id . ' > 1' );

			// find elements
			$this->findAll();

			// compute key
			if ( is_null( $key ) ) $key = $this->__id;

			// return all nodes
			return $this->getResultsAsAssocArray( $key, $columns );
		}


        /**
         *  Fetch the children of a node, or if no node is specified, fetch the top level items.
         *
         *  @param $id             The ID of the node to fetch child data for.
         *  @param $includeSelf    (optional) Include self node in results. Default: false.
         *  @param $prefix         (optional) Adds the relation's vars as prefixes to the keys. Default: false.
         *
         *  @returns The children of the passed id
         */
        function getChildren( $id, $includeSelf = false, $prefix = false ){

			$this->resetAll();
			
			return $this->_getChildren( $id, $includeSelf, $prefix );
        }


        /**
         *  Helper to fetch the children of a node, or if no node is specified, fetch the top level items.
         *
         *  @param $id             The ID of the node to fetch child data for.
         *  @param $includeSelf    (optional) Include self node in results. Default: false.
         *  @param $prefix         (optional) Adds the relation's vars as prefixes to the keys. Default: false.
         *
         *  @returns The children of the passed id
         */
        function _getChildren( $id, $includeSelf = false, $prefix = false  ){

            // get just children
			if ( $includeSelf == false ) $this->where(       $this->__table_parent . ' = ' . intval( $id ) );
			else                         $this->where( '(' . $this->__table_parent . ' = ' . intval( $id ) . ' OR ' . $this->__table_id . ' = ' . intval( $id ) . ')' );

			$this->findAll();

			return $this->getResults( false, false, false, $prefix );
        }


        /**
         *  Fetch the path to a node. If an invalid node is passed, an empty array is returned. If a top level node is 
         *  passed, an array containing on that node is included (if 'includeSelf' is set to true, otherwise an empty
         *  array).
         *
         *  @param $id             The ID of the node to fetch child data for.
         *  @param $includeSelf    (optional) Whether or not to include the passed node in the the results.
         *  @param $prefix         (optional) Adds the relation's vars as prefixes to the keys. Default: false.
         *
         *  @returns An array of each node to passed node
         */
        function getPath( $id, $includeSelf = false, $prefix = false ) {

			$this->resetAll();

			return $this->_getPath( $id, $includeSelf, $prefix );
        }


        /**
         *  Helper to fetch the path to a node. 
         *
         *  @param $id             The ID of the node to fetch child data for.
         *  @param $includeSelf    (optional) Whether or not to include the passed node in the the results.
         *  @param $prefix         (optional) Adds the relation's vars as prefixes to the keys. Default: false.
         *
         *  @returns An array of each node to passed node
         */
        function _getPath( $id, $includeSelf = false, $prefix = false ) {

            // Get the node
            $node = $this->getNode( intval($id) ) ;

			// reset values of previous getNode()
			$this->resetAll();

            // No node, return empty array
            if ( ! $node ) return array();

			// compute parents of this node. Read lineage, delete first '//', last '/', apply 'intval' to all elements and implode
			$nodes = array_map( 'intval', explode( '/', substr( substr( $node[ $this->__lineage ], 2 ), 0, -1 ) ) );

			// if we want current node too, lets add it to nodes array
            if ( $includeSelf == true ) $nodes[] = intval( $id );

			// apply where clause
			$this->where( $this->__table_id . ' IN (' . $this->escapeSqlArray( $nodes ) . ')' );

			$this->findAll();

			return $this->getResults( false, false, false, $prefix );
        }


        /**
         *  Check if one node descends from another node. If either node is not found, then false is returned.
         *
         *  @param $descendant_id  The node that potentially descends
         *  @param $ancestor_id    The node that is potentially descended from
         *
         *  @returns True if $descendant_id descends from $ancestor_id, false otherwise
         */
        function isDescendantOf( $descendant_id, $ancestor_id ) {

			// if ancertor is root, element is descendant if exist
			if ( $ancestor_id == 1 ) return ( $this->getNode( $descendant_id ) != false );

			$this->resetAll();
			
			return $this->_isDescendantOf( $descendant_id, $ancestor_id );
        }


        /**
         *  Helper to check if one node descends from another node.
         *
         *  @param $descendant_id  The node that potentially descends
         *  @param $ancestor_id    The node that is potentially descended from
         *
         *  @returns True if $descendant_id descends from $ancestor_id, false otherwise
         */
        function _isDescendantOf( $descendant_id, $ancestor_id ) {

			// id must be the descendant
			$this->set( $this->__id, intval( $descendant_id ) );

			// check if descendant has the ancestor in lineage ;)
			$this->where( $this->__table_lineage . ' LIKE "%/' . intval( $ancestor_id ) . '/%"' );
		
			// get total of rows
			return ( $this->findAll() == 1 );
        }


        /**
         *  Check if one node is a child of another node. If either node is not found, then false is returned.
         *
         * @param $child_id       The node that is possibly a child
         * @param $parent_id      The node that is possibly a parent
         *
         * @returns True if $child_id is a child of $parent_id, false otherwise
         */
        function isChildOf( $child_id, $parent_id ) {

			$this->resetAll();

			// check if there is a id that equals $child_id
			$this->set( $this->__id, intval( $child_id ) );

			// check if there is a parent that equals $parent_id
			$this->where( $this->__table_parent . ' = ' . intval( $parent_id ) );

			// get total of rows
			return ( $this->findAll() == 1 );
        }


        /**
         *  Find the number of descendants a node has
         *
         *  @param $id     The ID of the node to search for.
         *
         *  @returns The number of descendants the node has
         */
        function numDescendants( $id ) {

			$this->resetAll();

			return $this->_numDescendants( $id );
        }


        /**
         *  Helper to find the number of descendants a node has
         *
         *  @param $id     The ID of the node to search for.
         *
         *  @returns The number of descendants the node has
         */
        function _numDescendants( $id ) {

			// search all nodes that contains this id in lineage. if node is root, count all nodes
			if ( $id > 1 ) $this->where( $this->__table_lineage . ' LIKE "%/' . intval( $id ) . '/%"' );

			// find them
			return $this->findAll();
        }


        /**
         *  Find the number of children a node has
         *
         *  @param $id     The ID of the node to search for. Pass 0 to count the first level items
         *
         *  @returns The number of descendants the node has, or -1 if the node isn't found.
         */
        function numChildren( $id ) {

			$this->resetAll();

			// search all nodes that contains this id in lineage
			$this->where( $this->__table_parent . ' = ' . intval( $id ) );

			// find them
			return $this->findAll();
        }


        /**
         *  Fetch the immediately family of a node. More specifically, fetch a node's parent, siblings and children. 
         *
         * @param $id   The ID of the node to fetch child data for.
         *
         * @returns An array of each node in the family
         */
        function getImmediateFamily( $id ) {

            // Get the node parent
            $node = $this->getNode( $id );

            // No node, return empty array
            if ( ! $node ) return array();

			$this->resetAll();

			// get elements that have parent $parent (this returns current element and brothers), that have id $parent (returns parent), and that have lineage like /$id/ (returns all children)
			$this->where( '(' . $this->__table_parent . '=' . $node[ $this->__parent ] . ' OR ' . $this->__table_id . ' = ' . $node[ $this->__parent ] . ' OR ' . $this->__table_lineage . ' LIKE "%/' . intval( $id ) . '/%"' . ')' );

			$this->findAll();

            // Execute the query and get the record
            return $this->getResults();
        }


        /**
         *  This function adds a node to the database.
         *
         *  @param $values      The field values of the node. Do NOT define parent_id here!
         *  @param $parent_id   The parent ID of the node. If not set, node will be added as a root child (its parent will be 1)
         *  @param $position    (optional) Node position. If not set, node will be added at the end position
         *  @param $onDate      (optional) When element of $values is a date (read: array ), we should convert to this format. Default: 'datetimesql'
         *
         *  @returns    The ID of the newly inserted node.
         */
        function addNode( $values, $parent_id = 1, $position = null, $onDate = 'datetimesql' ) {

			// check values
			foreach( $values as $element => $value )
				if ( is_array( $value ) ) $values[ $element ] = YDStringUtil::formatDate( $value, $onDate );

            // compute linege. to do that we must check if we want to add node to root
            if ( $parent_id == 1 ) { 

				// compute lineage
				$lineage = '//';
			}else{

				// get parent lineage and compute node lineage
				$parent_node = $this->getNode( $parent_id );
				$lineage     = $parent_node[ $this->__lineage ] . $parent_id . '/';
			}

			// get how much brothers we will have
			$total_brothers = $this->numChildren( $parent_id );

			// compute position. If passed in arg we check if really can be that value, otherwise place node at the end
			if ( !is_numeric( $position ) || intval( $position ) < 1 || intval( $position ) > $total_brothers + 1 )
				$position = $total_brothers + 1;

			// create an empty position. To do this, if node is not added in the end, we must increment position of nodes that have the same parent and equal or bigger position 
			if ( $position != $total_brothers + 1 ){
	
				$this->resetAll();

				// position field must increment
				$this->set( $this->__position, $this->__table_position . ' + 1' );

				// only on new brothers with higher or equal position
				$this->where( '(' . $this->__table_parent . ' = ' . intval( $parent_id ) . ' AND ' .  $this->__table_position . ' >= ' . intval( $position ) . ')' );

				// lets update.
				$this->update( array(), $this->__position );
			}

			// reset any previous value to create insert
			$this->resetAll();

			// apply custom values
			$this->setValues( $values );

			// override reserved fields
			$this->set( $this->__parent,   $parent_id );
			$this->set( $this->__lineage,  $lineage );
			$this->set( $this->__level,    $this->_getLevel( $lineage ) );
			$this->set( $this->__position, $position );

			return $this->insert();
        }


        /**
         *  This function updates a node fields ( that are NOT RESERVED only )
         *
         *  @param $values      The field values of the node. Do NOT update position, parent_id, lineage or level
         *  @param $id          (optional) The ID of the node to update.
         *  @param $onDate      (optional) When element of $values is a date (read: array ), we should convert to this format. Default: 'datetimesql'
         *
         *  @returns    Total of lines affected
         */
        function updateNode( $values, $id, $onDate = 'datetimesql' ) {

			// check values
			foreach( $values as $element => $value )
				if ( is_array( $value ) ) $values[ $element ] = YDStringUtil::formatDate( $value, $onDate );

			$this->resetAll();

			// apply custom values
			$this->setValues( $values );
			
			// overwrite id
			$this->set( $this->__id, intval( $id ) );

			// unset reserved fields
			$this->unsetVar( $this->__parent );
			$this->unsetVar( $this->__lineage );
			$this->unsetVar( $this->__level );
			$this->unsetVar( $this->__position );

			return $this->update();
        }


        /**
         *  Delete the node and it's children. NOTE: Make shure your table is in InnoDB !
         *
         *  @param $id             The ID of the node to delete.
         *  @param $deleteAll     (Optional) Delete id and all children (true by default. if false, deletes children only)
         *
         *  @returns    Total of lines affected
         */
        function deleteNode( $id, $deleteAll = true ) {

            // if we want to delete $id (and all children) we must update positions in all $id brothers after delete
			if ( $deleteAll ){

	            // get node details before delete. we must know the position
	            $node = $this->getNode( $id );

				$this->resetAll();

				$this->set( $this->__id, intval( $id ) );

				// if delete didn't affect any rows we don't need to update brothers
				$total = $this->delete();
				
				if ( $total == 0 ) return 0;

				$this->resetAll();

				// decrease positions
				$this->set( $this->__position, $this->__table_position . ' - 1' );

				// in all elements with same parent AND position bigger than our
				$this->where( '(' . $this->__table_parent . ' = ' . intval( $node[ $this->__parent ] ) . ' AND ' . $this->__table_position . ' > ' . intval( $node[ $this->__position ] ) . ')' );

				$this->update( array(), $this->__position );
				
				return $total;
			}

			// here we want do delete child only
			$this->resetAll();

			// we only need to delete children. Children of children will be deleted when mysql is InnoDB
			$this->where( $this->__table_parent . ' = ' . intval( $id ) );

			return $this->delete();
        }


        /**
         *  Move a node to a different parent node.
         *
         *  @param  $id             The ID of the node to move
         *  @param  $new_parent_id  (optional) The ID of the new parent node. If not set, will be moved in same parent
         *  @param  $new_position   (optional) The new position.
         */
        function moveNode( $id, $new_parent_id = null, $new_position = null ) {

            // get old node details before move
            $old_node      = $this->getNode( $id );
            $old_parent_id = $old_node[ $this->__parent ];
            $old_position  = $old_node[ $this->__position ];
            $old_lineage   = $old_node[ $this->__lineage ];

			// compute new parent id
			if ( ! is_numeric( $new_parent_id ) ) $new_parent_id = $old_parent_id;

			// if position not set, we will move node to the end of the new parent
			$total_new_brothers = $this->numChildren( $new_parent_id );

			// if custom position is not valid add node at end
			if ( ! is_numeric( $new_position ) || intval( $new_position ) < 1 || intval( $new_position ) > $total_new_brothers + 1 )
				$new_position = 1 + $total_new_brothers;

			// get information of old parent
            $old_parent_node = $this->getNode( $old_parent_id );

			// get information of new parent if diferent than old parent
			if ( $new_parent_id == $old_parent_id ) $new_parent_node = $old_parent_node;
            else                                    $new_parent_node = $this->getNode( $new_parent_id );


			// only update positions if new parent and old parent are not the same OR if (they are same but) positions are not changed
			if ( $new_parent_id != $old_parent_id || $new_position != $old_position ){

				// decrease positions of old brothers that have position bigger than this node position
				$this->resetAll();
				$this->set( $this->__position, $this->__table_position . ' - 1' );
				$this->where( '(' . $this->__table_parent . ' = ' . $old_parent_id . ' AND ' . $this->__table_position . ' > ' . $old_position . ')' );
				$this->update( array(), $this->__position );

				// add position space for this node in new parent: increase positions of new brothers that have position bigger than this node position
				$this->resetAll();
				$this->set( $this->__position, $this->__table_position . ' + 1' );
				$this->where( '(' . $this->__table_parent . ' = ' . $new_parent_id . ' AND ' . $this->__table_position . ' >= ' . $new_position . ')' );
				$this->update( array(), $this->__position );
			}

			// compute lineage
			if ( $new_parent_id == 1 ) $new_lineage = '//';
			else                       $new_lineage = $new_parent_node[ $this->__lineage ] . $new_parent_id . '/';

			// update node
			$this->resetAll();
			$this->set( $this->__id,       intval( $id ) );
			$this->set( $this->__parent,   intval( $new_parent_id ) );
			$this->set( $this->__lineage,  $new_lineage );
			$this->set( $this->__level,    $this->_getLevel( $new_lineage ) );
			$this->set( $this->__position, intval( $new_position ) );
			$res = $this->update();
			
			// update lineages of node descendants ;)
			if ( $new_parent_id != $old_parent_id ){
				$this->resetAll();
				$this->set( $this->__lineage, 'REPLACE(' . $this->__table_lineage . ',"' . $old_lineage . $id . '/","' . $new_lineage . $id . '/")' );
				$this->where( $this->__table_id . ' > 1 ' );
				$this->update( array(), $this->__lineage );
			}

			return $res;
        }


        /**
         *  Fetch an array of tree nodes containing a traversal of the tree. 
         *
         * @param $id   (optional) The ID of the node to fetch child data for.
         *
         * @returns An array of each node in the tree
         */
		function getTraversedTree( $id = 1 ) {
		
			$this->_tree_data = $this->getTreeElements();
			$this->_tree_data_keys = array_keys( $this->_tree_data );
		
			return $this->_getTraversedTree( $id );
		}


        /**
         *  Helper function to get traversal of tree. 
         *
         * @param $id   (optional) The ID of the node to fetch child data for.
         *
         * @returns An array of each node in the tree
         */
		function _getTraversedTree( $id = 1 ) {
		
			$key_match = false;
			
			foreach ( $this->_tree_data_keys as $key ) {
				if ( $this->_tree_data[$key]['id'] == $id ) {
					$key_match = true;
					$ref = & $this->_tree_data[$key];
					break;
				}
			}
			
			if ( $key_match ) {
				$result = array( $ref );
			} else {
				$result = array();
			}
			
			$children = $this->getChildren ( $id, true );
			
			foreach ( $children as $child ) {			
				$child_ids = $this->getTraversedTree( $child['id'] );				
				$result = array_merge( $result, $child_ids );
			}
						
			return $result;
		}
		

    }
?>