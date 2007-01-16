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

    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDDatabase.php' );

    /**
     *  This class implements a database tree

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
        );


        INSERT INTO nested_tree VALUES ( 1, null, '',         0, 1, '');
        INSERT INTO nested_tree VALUES ( 2,    1, '//',       1, 1, 'General Resources');
        INSERT INTO nested_tree VALUES ( 3,    2, '//2/',     2, 1, 'Code Paste');
        INSERT INTO nested_tree VALUES ( 4,    2, '//2/',     2, 2, 'Documentation');
        INSERT INTO nested_tree VALUES ( 5,    2, '//2/',     2, 3, 'Books & Publications');
        INSERT INTO nested_tree VALUES ( 6,    5, '//2/5/',   3, 1, 'Apache');
        INSERT INTO nested_tree VALUES ( 7,    5, '//2/5/',   3, 2, 'PostgreSQL');
        INSERT INTO nested_tree VALUES ( 8,    5, '//2/4/',   3, 3, 'MySQL');
        INSERT INTO nested_tree VALUES ( 9,    2, '//2/',     2, 1, 'Links');
        INSERT INTO nested_tree VALUES (10,    9, '//2/9/',   3, 1, 'Databases');
        INSERT INTO nested_tree VALUES (11,    9, '//2/10/',  3, 1, 'Generators');
        INSERT INTO nested_tree VALUES (12,    9, '//2/10/',  3, 2, 'Portals');
     */

    class YDDatabaseTree2 extends YDBase {

        /**
         *  Constructor. Set the database table name and necessary field names
         *
         *  @param $table          Name of the tree database table
         *  @param $db             (optional) The YDDatabase instance name or object pointing to the database
         *  @param $idField        (optional) Name of the primary key ID field. Default is id.
         *  @param $parentField    (optional) Name of the parent ID field. Default is parent_id.
         *  @param $sortField      (optional) Name of the field to sort data.
         */
        function YDDatabaseTree2( $table, $db='default', $idField='id', $parentField='parent_id', $sortField='parent_id ASC, position ASC' ) {

            $this->db = YDDatabase::getNamedInstance( $db );

            $this->table = $table;
            $this->fields = array( 'id' => $idField, 'parent' => $parentField, 'lineage' => 'lineage', 'level' => 'level', 'position' => 'position' );
			$this->sortField = $sortField;
        }


        /*
         *  Function to convert an object to a node array
         *
         *  @param $node    The node to convert to a node array.
         *
         *  @return The node as a node array.
         */
        function _toNodeArray( $node ) {
            $arr = array();

            foreach ( $this->fields as $f )
                if ( isset( $node[ $f ] ) ) $arr[ $f ] = $node[ $f ];

            return $arr;
        }


        /**
         *  A utility function to return an array of the fields that need to be selected in SQL select queries.
         *
         *  @returns array   An indexed array of fields to select
         *
         *  @internal
         */
        function _getFields() {
            return array_values( $this->fields );
        }


        /**
         *  Get the fields joined as single string.
         *
         *  @returns A single string with the fields joined as a string
         *
         *  @internal
         */
        function _getFieldsAsString() {
            return join( ',', $this->_getFields() );
        }


        /**
         *  Add a field to the list of fields to will get returned by this class.
         *
         *  @param  $name   The name of the field that should be added.
         */
        function addField( $name ) {
            if ( ! in_array( $name, $this->fields ) ) {
                $this->fields[ $name ] = $name;
            }
        }


        /**
         *  Add a number of fields to the list of fields to will get returned by this class.
         *
         *  @param  $names   The name of the field that should be added.
         */
        function addFields( $names ) {
            if ( is_array( $names ) ) {
                foreach ( $names as $name ) {
                    $this->addField( $name );
                }
            }
        }


        /**
         *  Returns the level based on the lineage
         *
         *  @returns  level int value
         */
        function _getLevel( $lineage ) {
			return substr_count( $lineage, '/' ) - 1;
        }


        /**
         *  Set the sort field.
         *
         *  @param $sortField      (optional) Name of the field to sort data. Default is title.
         */
        function setSortField( $sortField='title' ) {
            $this->sortField = $sortField;
        }


        /**
         *  Returns orberby sql clause
         *
         *  @returns orderBy sql
         */
        function _getOrder() {
            return ' order by ' . $this->sortField;
        }


        /**
         *  Fetch the node data for the node identified by $id.
         *
         *  @param $id      The ID of the node to fetch.
         *  @param $field   The unique field to select on. Defaults to id, which means that the ID field specified
         *                  when the object was instantiated will be used.
         *
         *  @returns An object containing the node's data, or false if node not found
         */
        function getNode( $id, $field = 'id' ) {

            // Get the name of the field
			if ( ! isset( $this->fields[ $field ] ) ) trigger_error( 'YDDatabaseTree::getNode, Field ' . $field . ' is not set!', YD_ERROR );

			$query = 'SELECT ' . $this->_getFieldsAsString() . ' FROM ' . $this->table . ' WHERE ' . $this->fields[ $field ] . ' = ' . $this->db->escapeSql( $id );

            // Execute the query and return the record
            return $this->db->getRecord( $query );
        }


        /**
         *  Fetch the descendants of a node. To get all elements use getTreeElements
         *
         *  @param $id              The ID of the node to fetch descendant data for. 
         *  @param $includeSelf     (optional) Whether or not to include the passed node in the the results. 
         *  @param $specificPart    (optional) Return array should be not-associative (using a specific key to filter)
         *
         *  @returns The descendants of the passed now
         */
        function getDescendants( $id, $includeSelf=false, $specificPart=null ) {

            // get just children
			if ( $includeSelf == false ){

				// get elements where lineage has expression  /$id/
				$query = 'SELECT ' . $this->_getFieldsAsString() . ' FROM ' . $this->table . ' WHERE ' . $this->fields['lineage'] . ' LIKE "%/' . intval( $id ) . '/%"' . $this->_getOrder();

			}else{

				// get elements where lineage has expression  /$id/ or id is $id
				$query = 'SELECT ' . $this->_getFieldsAsString() . ' FROM ' . $this->table . ' WHERE ' . $this->fields['lineage'] . ' LIKE "%/' . intval( $id ) . '/%" OR ' . $this->fields['id'] . ' = ' . intval( $id ) . $this->_getOrder();
			}

            // check if we want a specific column
            if ( is_string( $specificPart ) ) return $this->db->getValuesByName( $query, $specificPart );

			return $this->db->getRecords( $query );
		}


        /**
         *  Fetch all elements of a tree
         *
         *  @param $specificPart    (optional) Return array should be not-associative (using a specific key to filter)
         *
         *  @returns The descendants of the passed now
         */
        function getTreeElements( $specificPart=null ) {

			$query = 'SELECT ' . $this->_getFieldsAsString() . ' FROM ' . $this->table . ' WHERE ' . $this->fields['id'] . ' != 1' . $this->_getOrder();

            // check if we want a specific column
            if ( is_string( $specificPart ) ) return $this->db->getValuesByName( $query, $specificPart );

			return $this->db->getRecords( $query );
		}


        /**
         *  Fetch the children of a node, or if no node is specified, fetch the top level items.
         *
         *  @param $id             The ID of the node to fetch child data for.
         *  @param $includeSelf    (optional) Whether or not to include the passed node in the the results.
         *
         *  @returns The children of the passed node
         */
        function getChildren( $id, $includeSelf = false, $specificPart = null ){

            // get just children
			if ( $includeSelf == false ){

				// get elements where parent is $id
				$query = 'SELECT ' . $this->_getFieldsAsString() . ' FROM ' . $this->table . ' WHERE ' . $this->fields['parent'] . ' = ' . intval( $id ) . $this->_getOrder();

			}else{

				// get elements where parent is $id or id is $id
				$query = 'SELECT ' . $this->_getFieldsAsString() . ' FROM ' . $this->table . ' WHERE ' . $this->fields['parent'] . ' = ' . intval( $id ) . ' OR ' . $this->fields['id'] . ' = ' . intval( $id ) . $this->_getOrder();
			}

            // check if we want a specific column
            if ( is_string( $specificPart ) ) return $this->db->getValuesByName( $query, $specificPart );

			return $this->db->getRecords( $query );
        }


        /**
         *  Fetch the path to a node. If an invalid node is passed, an empty array is returned. If a top level node is 
         *  passed, an array containing on that node is included (if 'includeSelf' is set to true, otherwise an empty
         *  array).
         *
         *  @param $id             (optional) The ID of the node to fetch child data for.
         *  @param $includeSelf    (optional) Whether or not to include the passed node in the the results.
         *
         *  @returns An array of each node to passed node
         */
        function getPath( $id, $includeSelf=false ) {

            // Get the ID field
            $idField = $this->fields['id'];

            // Get the node lineage
            $node = $this->getNode( $id ) ;

            // No node, return empty array
            if ( ! $node ) return array();

			// compute parents of this node. delete first '//' and implode
			$nodes = array_map( 'intval', explode('/', substr( $node[ $this->fields[ 'lineage' ] ], 2 ) ) );

			// delete current id from nodes to get only parents
			array_pop( $nodes );

            if ( $includeSelf == false ) {

				// get all elements that have an id that belongs to $nodes
                $query = 'SELECT ' . $this->_getFieldsAsString() . ' FROM ' . $this->table . ' WHERE ' . $this->fields['id'] . ' IN ("' . implode( '","', $nodes ) . '")' . $this->_getOrder();

            }else{

				// get all elements that have an id that belongs to $nodes or is $id
                $query = 'SELECT ' . $this->_getFieldsAsString() . ' FROM ' . $this->table . ' WHERE ' . $this->fields['id'] . ' IN ("' . implode( '","', $nodes ) . '") OR ' . $this->fields['id'] . ' = ' . intval( $id ) . $this->_getOrder();
            }

			return $this->db->getRecords( $query );
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

			// count elements where id is $descendant_id and lineage has expression /$ancestor_id/
			$query = 'SELECT count(*) as is_descendant FROM ' . $this->table . ' WHERE ' . $this->fields['id'] . ' = ' . intval( $descendant_id ) . ' AND ' . $this->fields['lineage'] . ' LIKE "%/' . intval( $ancestor_id ) . '/%"' . $this->_getOrder();

            // Execute the query and get the record
            $record = $this->db->getRecord( $query );

            // Return the result
            return $record[ 'is_descendant' ] == 1;
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


			// count elements where id is $child_id and parent_id is $parent_id
			$query = 'SELECT count(*) as is_child FROM ' . $this->table . ' WHERE ' . $this->fields['id'] . ' = ' . intval( $child_id ) . ' AND ' . $this->fields['parent'] . ' = ' . intval( $parent_id );

            // Execute the query and get the record
            $record = $this->db->getRecord( $query );

            // Return the result
            return $record[ 'is_child' ] == 1;
        }


        /**
         *  Find the number of descendants a node has
         *
         *  @param $id     The ID of the node to search for.
         *
         *  @returns The number of descendants the node has
         */
        function numDescendants( $id ) {

			$query = 'SELECT count(*) as num_descendants FROM ' . $this->table . ' WHERE ' . $this->fields['lineage'] . ' LIKE "%/' . intval( $id ) . '/%"';

            // Execute the query and get the record
            $record = $this->db->getRecord( $query );

            // Return the result
            return $record[ 'num_descendants' ];
        }


        /**
         *  Find the number of children a node has
         *
         *  @param $id     The ID of the node to search for. Pass 0 to count the first level items
         *
         *  @returns The number of descendants the node has, or -1 if the node isn't found.
         */
        function numChildren( $id ) {

			$query = 'SELECT count(*) as num_children FROM ' . $this->table . ' WHERE ' . $this->fields['parent'] . ' = ' . intval( $id );

            // Execute the query and get the record
            $record = $this->db->getRecord( $query );

            // Return the result
            return $record[ 'num_children' ];
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

			// get elements that have parent $parent (this returns current element and brothers), that have id $parent (returns parent), and that have lineage like /$id/ (returns all children)
			$query = 'SELECT ' . $this->_getFieldsAsString() . ' FROM ' . $this->table . ' WHERE ' . $this->fields['parent'] . ' = ' . $node[ $this->fields['parent'] ] . ' OR ' . $this->fields['id'] . ' = ' . $node[ $this->fields['parent'] ] . ' OR ' . $this->fields['lineage'] . ' LIKE "%/' . intval( $id ) . '/%"' . $this->_getOrder();

            // Execute the query and get the record
            return $this->db->getRecords( $query );
        }


        /**
         *  Fetch the tree data, nesting within each node references to the node's children
         *
         *  @returns The tree with the node's child data
         */
        function getTreeWithChildren() {
        }


        /**
         *  This function adds a node to the database.
         *
         *  @param $values      The field values of the node. Do NOT define parent_id here!
         *  @param $parent_id   The parent ID of the node. If not set, node will be added as a root child
         *  @param $position    (optional) Node position. If not set, node will be added at the end position
         *
         *  @returns    The ID of the newly inserted node.
         */
        function addNode( $values, $parent_id = 1, $position = null ) {

			// override parent in $values
			$values[ $this->fields['parent'] ] = $parent_id;

            // Check if we want to add a root child
            if ( $parent_id == 1 ) { 

				// compute lineage and level
				$lineage = '//';
			}else{

				// get lineage from parent and compute its lineage
				$parent_node = $this->getNode( $parent_id );
				$lineage     = $parent_node[ $this->fields['lineage'] ] . $parent_id . '/';
			}

			$values[ $this->fields['lineage'] ] = $lineage;
			$values[ $this->fields['level'] ]   = $this->_getLevel( $lineage );

			// compute position. If passed in arg we check if really can be that value, otherwise get the total of brothers and place node at the end
			$total_brothers = $this->numChildren( $parent_id );

			if ( is_numeric( $position ) && intval( $position ) > 0 && intval( $position ) < 1 + $total_brothers ){
				$values[ $this->fields['position'] ] = intval( $position );
			}else{
				$values[ $this->fields['position'] ] = 1 + $total_brothers;
			}

			// increment position of nodes that have the same parent AND have position bigger than $position
			$this->db->executeSql( 'UPDATE ' . $this->table . ' SET ' . $this->fields['position'] . ' = ' . $this->fields['position'] . ' + 1 WHERE ' . $this->fields['parent'] . ' = ' . intval( $parent_id ) . ' AND ' .  $this->fields['position'] . ' > ' . $values[ $this->fields['position'] ] );

			// insert node
			$this->db->executeInsert( $this->table, $values );

			return $this->db->getLastInsertID();
        }


        /**
         *  This function updates a node in the database.
         *
         *  @param $values      The field values of the node. Do NOT update position, parent_id, lineage or level
         *  @param $id          (optional) The ID of the node to update.
         *
         *  @returns    Total of lines affected
         */
        function updateNode( $values, $id ) {

            // Convert the values from an ojbect to an array
            $values = $this->_toNodeArray( $values );

			// delete reserved fields. Any update on these could corrupt all information ;)
			if ( isset( $values[ $this->fields['id'] ] ) )			unset( $values[ $this->fields['id'] ] );
			if ( isset( $values[ $this->fields['parent'] ] ) )		unset( $values[ $this->fields['parent'] ] );
			if ( isset( $values[ $this->fields['lineage'] ] ) )		unset( $values[ $this->fields['lineage'] ] );
			if ( isset( $values[ $this->fields['level'] ] ) )		unset( $values[ $this->fields['level'] ] );
			if ( isset( $values[ $this->fields['position'] ] ) )	unset( $values[ $this->fields['position'] ] );

            // Perform the insert
            return $this->db->executeUpdate( $this->table, $values, $this->fields['id'] . ' = ' . intval( $id ) );
        }


        /**
         *  Delete the node and it's children.
         *
         *  @param $id             The ID of the node to delete.
         *  @param $deleteAll     (Optional) Delete id and all children (true by default. if false, deletes children only)
         *
         *  @returns    Total of lines affected
         */
        function deleteNode( $id, $deleteAll = true ) {

            // delete nodes
			if ( $deleteAll ){

	            // get the node details before delete
	            $node = $this->getNode( $id );

				// delete id. we must update brothers positions of this id
	            $res = $this->db->executeSql( 'DELETE FROM ' . $this->table . ' WHERE ' . $this->fields['id'] . ' = ' . intval( $id ) );

				// update positions. decrease position of nodes with same parent when position id bigger than $node[position]
				$this->db->executeSql( 'UPDATE ' . $this->table . ' SET ' . $this->fields['position'] . ' = ' . $this->fields['position'] . ' - 1 WHERE ' . $this->fields['parent'] . ' = ' . $node[ $this->fields['parent'] ] . ' AND ' . $this->fields['position'] . ' > ' . $node[ $this->fields['position'] ] );

			// delete children of $id
			}else{

				// delete children of $id. we don't need to update position because all children will be deleted
	            $res = $this->db->executeSql( 'DELETE FROM ' . $this->table . ' WHERE ' . $this->fields['parent'] . ' = ' .  intval( $id ) );
			}

			return $res;
        }


        /**
         *  Move a node to a different parent node.
         *
         *  @param  $id         	The ID of the node to move-
         *  @param  $new_parent_id  (optional) The ID of the new parent node. If not set, will be moved in same parent
         *  @param  $position   	(optional) The new position.
         */
        function moveNode( $id, $new_parent_id = null, $position = null ) {

            // get old node details before move
            $old_node      = $this->getNode( $id );
            $old_parent_id = $old_node[ $this->fields['parent'] ];
            $old_position  = $old_node[ $this->fields['position'] ];
            $old_lineage   = $old_node[ $this->fields['lineage'] ];

			// compute new parent id
			if ( ! is_numeric( $new_parent_id ) ) $new_parent_id = $old_node[ $this->fields['parent'] ];

			// if position not set, we will move node to the end of the new parent
			$total_new_brothers = $this->numChildren( $new_parent_id );

			if ( is_numeric( $position ) && intval( $position ) > 0 && intval( $position ) < 1 + $total_new_brothers ){
				$new_position = intval( $position );
			}else{
				$new_position = 1 + $total_new_brothers;
			}

			// get information of old and new parents
            $old_parent_node     = $this->getNode( $old_parent_id );
            $new_parent_node     = $this->getNode( $new_parent_id );
			$new_lineage         = $new_parent_node[ $this->fields['lineage'] ] . $new_parent_id . '/';

			// compute node to add
			$values[ $this->fields['parent'] ] = $new_parent_id;
			$values[ $this->fields['lineage'] ]   = $new_lineage;
			$values[ $this->fields['level'] ]     = $this->_getLevel( $new_lineage );
			$values[ $this->fields['position'] ]  = $new_position;

			// add position space for this node in new parent: increase positions of new brothers that have position bigger than this node position
			$this->db->executeSql( 'UPDATE ' . $this->table . ' SET ' . $this->fields['position'] . ' = ' . $this->fields['position'] . ' + 1 WHERE ' . $this->fields['parent'] . ' = ' . $new_parent_id . ' AND ' . $this->fields['position'] . ' > ' . $new_position );

			// decrease positions of old brothers that have position bigger than this node position
			$this->db->executeSql( 'UPDATE ' . $this->table . ' SET ' . $this->fields['position'] . ' = ' . $this->fields['position'] . ' - 1 WHERE ' . $this->fields['parent'] . ' = ' . $old_parent_id . ' AND ' . $this->fields['position'] . ' > ' . $old_position );

			// update node
            $res = $this->db->executeUpdate( $this->table, $values, $this->fields['id'] . ' = ' . $id );

			// update lineage of all descendants of this node.
			$this->db->executeSql( 'UPDATE ' . $this->table . ' SET ' . $this->fields['lineage'] . ' = REPLACE(' . $this->fields['lineage'] . ',"' . $old_lineage . '/' . $id . '/","' . $new_lineage . '/' . $id . '/")' );

			return $res;
        }


    }

?>