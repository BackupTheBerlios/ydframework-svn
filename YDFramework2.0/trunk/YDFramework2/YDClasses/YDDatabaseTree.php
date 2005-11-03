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

    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDDatabase.php' );

    /**
     *  This class implements a database tree as described on:
     *  http://www.phpriot.com/d/articles/php/application-design/nested-trees-2/
     */
    class YDDatabaseTree extends YDBase {

        /**
         *  Constructor. Set the database table name and necessary field names
         *
         *  @param $db             The YDDatabase object pointing to the database
         *  @param $table          Name of the tree database table
         *  @param $idField        (optional) Name of the primary key ID field. Default is id.
         *  @param $parentField    (optional) Name of the parent ID field. Default is parent_id.
         *  @param $sortField      (optional) Name of the field to sort data. Default is title.
         */
        function YDDatabaseTree( $db, $table, $idField='id', $parentField='parent_id', $sortField='title' ) {
            $this->db    = $db;
            $this->table = $table;
            $this->fields = array( 'id' => $idField, 'parent' => $parentField, 'sort' => $sortField );
            $this->_use_query_cache = true;
            $this->_query_cache = array();
        }

        /**
         *  A utility function to return an array of the fields that need to be selected in SQL select queries.
         *
         *  @returns array   An indexed array of fields to select
         *
         *  @internal
         */
        function _getFields() {
            return array(
                $this->fields['id'], $this->fields['parent'], $this->fields['sort'], 'nleft', 'nright', 'nlevel'
            );
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

        /*
         *  Convert an array to an object.
         *
         *  @param The array to convert.
         *
         *  @returns The array as an object.
         *
         *  @internal
         */
        function _arrayToObj( $arr ) {
            $obj = new YDBase();
            foreach ( $arr as $key => $val ) {
                $obj->$key = $val;
            }
            return $obj;
        }

        /*
         *  Function to convert an object to a node array
         *
         *  @param $node    The node to convert to a node array.
         *
         *  @return The node as a node array.
         */
        function _toNodeArray( $node ) {
            return $node;
        }

        /*
         *  Function to convert a node array to a object
         *
         *  @param $node    The node to convert to an object.
         *
         *  @return The node as an object.
         */
        function _fromNodeArray( $node ) {
            return $node;
        }

        /**
         *  Function to clear the query cache.
         *
         *  @internal
         */
        function _clearCache() {
            $this->_query_cache = array();
        }

        /**
         *  This function is a cache-enabled version of the $db->getRecord function.
         *
         *  @internal
         */
        function _getRecord( $query ) {
            if ( $this->_use_query_cache && isset( $this->_query_cache[ '[_getRecord]' . $query ] ) ) {
                return $this->_query_cache[ '[_getRecord]' . $query ];
            } else {
                $result = $this->db->getRecord( $query );
                $this->_query_cache[ '[_getRecord]' . $query ] = $result;
                return $result;
            }
        }

        /**
         *  This function is a cache-enabled version of the $db->getRecords function.
         *
         *  @internal
         */
        function _getRecords( $query ) {
            if ( $this->_use_query_cache && isset( $this->_query_cache[ '[_getRecords]' . $query ] ) ) {
                return $this->_query_cache[ '[_getRecords]' . $query ];
            } else {
                $result = $this->db->getRecords( $query );
                $this->_query_cache[ '[_getRecords]' . $query ] = $result;
                return $result;
            }
        }

        /**
         *  Fetch the node data for the node identified by $id.
         *
         *  @param $id  The ID of the node to fetch
         *
         *  @returns An object containing the node's data, or false if node not found
         */
        function getNode( $id ) {

            // The query to execute
            $query = sprintf(
                'select %s from %s where %s = %d',
                $this->_getFieldsAsString(), $this->table, $this->fields['id'], $id
            );

            // Execute the query and return the record
            return $this->_fromNodeArray( $this->_getRecord( $query ) );

        }

        /**
         *  Fetch the descendants of a node, or if no node is specified, fetch the entire tree. Optionally, only return
         *  child data instead of all descendant data.
         *
         *  @param $id              (optional) The ID of the node to fetch descendant data for. Specify an  invalid ID
         *                          (e.g. 0) to retrieve all data.
         *  @param $includeSelf     (optional) Whether or not to include the passed node in the the results. This has no
         *                          meaning if fetching entire tree.
         *  @param $childrenOnly    (optional) True if only returning children data. False if returning all descendant
         *                          data.
         *  @param $max_level       (optional) Maximum level to retrieve. Default is all.
         *
         *  @returns The descendants of the passed now
         */
        function getDescendants( $id=0, $includeSelf=false, $childrenOnly=false, $max_level=null ) {

            // Get the ID field
            $idField = $this->fields['id'];

            // Get the node
            $node = $this->_toNodeArray( $this->getNode( $id ) );

            // Find nleft, nright and parent_id
            if ( ! $node ) {
                $nleft = 0;
                $nright = 0;
                $parent_id = 0;
            } else {
                $nleft = $node['nleft'];
                $nright = $node['nright'];
                $parent_id = $node[$idField];
            }

            // Children only
            if ( $childrenOnly ) {

                // Include ourselves?
                if ( $includeSelf ) {
                    $query = sprintf(
                        'select %s from %s where %s = %d or %s = %d order by nleft',
                        $this->_getFieldsAsString(), $this->table, $this->fields['id'], $parent_id, $this->fields['parent'], $parent_id
                    );
                } else {
                    $query = sprintf(
                        'select %s from %s where %s = %d order by nleft',
                        $this->_getFieldsAsString(), $this->table, $this->fields['parent'], $parent_id
                    );
                }

            } else {
                
                // Include all
                if ( $nleft > 0 && $includeSelf ) {
                    $query = sprintf(
                        'select %s from %s where nleft >= %d and nright <= %d order by nleft',
                         $this->_getFieldsAsString(), $this->table, $nleft, $nright
                    );
                } else if ( $nleft > 0 ) {
                    $query = sprintf(
                        'select %s from %s where nleft > %d and nright < %d order by nleft',
                        $this->_getFieldsAsString(), $this->table, $nleft, $nright
                    );
                } else {
                    $query = sprintf( 'select %s from %s where id > 0 order by nleft', $this->_getFieldsAsString(), $this->table );
                }
                
            }

            // Add the level constraint
            if ( ! is_null( $max_level ) ) {
                $max_level = ( $includeSelf ) ? $max_level : $max_level + 1;
                $max_level = ( $includeSelf ) ? $node['nlevel'] + $max_level - 1 : $node['nlevel'] + $max_level;
                $query = str_replace( 'where ', 'where nlevel <= ' . $max_level . ' and ', $query );
            }

            // Get the results as an array
            $records = $this->_getRecords( $query );

            // Reformat the array
            $arr = array();
            foreach ( $records as $record ) {
                $arr[ $record[$idField] ] = $this->_fromNodeArray( $record );
            }

            // Return the result
            return $arr;

        }

        /**
         *  Fetch the children of a node, or if no node is specified, fetch the top level items.
         *
         *  @param $id             (optional) The ID of the node to fetch child data for.
         *  @param $includeSelf    (optional) Whether or not to include the passed node in the the results.
         *
         *  @returns The children of the passed node
         */
        function getChildren( $id=0, $includeSelf=false ) {
            return $this->getDescendants( $id, $includeSelf, true );
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
        function getPath( $id=0, $includeSelf=false ) {

            // Get the ID field
            $idField = $this->fields['id'];

            // Get the node
            $node = $this->_toNodeArray( $this->getNode( $id ) );

            // No node, return empty array
            if ( ! $node ) {
                return array();
            }

            // Include ourselves?
            if ( $includeSelf ) {
                $query = sprintf(
                    'select %s from %s where nleft <= %d and nright >= %d order by nlevel',
                    $this->_getFieldsAsString(), $this->table, $node['nleft'], $node['nright']
                );
            } else {
                $query = sprintf(
                    'select %s from %s where nleft < %d and nright > %d order by nlevel',
                    $this->_getFieldsAsString(), $this->table, $node['nleft'], $node['nright']
                );
            }

            // Get the results as an array
            $records = $this->_getRecords( $query );

            // Reformat the array
            $arr = array();
            foreach ( $records as $record ) {
                $arr[ $record[$idField] ] = $this->_fromNodeArray( $record );
            }

            // Return the result
            return $arr;

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

            // Get the node
            $node = $this->_toNodeArray( $this->getNode( $ancestor_id ) );

            // No node, return empty array
            if ( ! $node ) {
                return false;
            }

            // The query
            $query = sprintf(
                'select count(*) as is_descendant from %s where %s = %d and nleft > %d and nright < %d',
                $this->table, $this->fields['id'], $descendant_id, $node['nleft'], $node['nright']
            );

            // Execute the query and get the record
            $record = $this->_getRecord( $query );

            // Return the result
            if ( $record ) {
                return $record['is_descendant'] > 0;
            } else {
                return false;
            }

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

            // The query
            $query = sprintf(
                'select count(*) as is_child from %s where %s = %d and %s = %d',
                $this->table, $this->fields['id'], $child_id, $this->fields['parent'], $parent_id
            );

            // Execute the query and get the record
            $record = $this->_getRecord( $query );

            // Return the result
            if ( $record ) {
                return $record['is_child'] > 0;
            } else {
                return false;
            }

        }

        /**
         *  Find the number of descendants a node has
         *
         *  @param $id     The ID of the node to search for. Pass 0 to count all nodes in the tree.
         *
         *  @returns The number of descendants the node has, or -1 if the node isn't found.
         */
        function numDescendants( $id ) {

            // Check for the ID
            if ( $id == 0 ) {

                // The query
                $query = sprintf('select count(*) as num_descendants from %s', $this->table );

                // Execute the query and get the record
                $record = $this->_getRecord( $query );

                // Return the number of descendants for the node
                if ( $record ) {
                    return intval( $row['num_descendants'] );
                }

            } else {

                // Get the node
                $node = $this->getNode( $id );

                // Return the number of descendants
                if ( $node ) {
                    return ( $node['nright'] - $node['nleft'] - 1 ) / 2;
                }

            }

            // No descendants
            return -1;

        }

        /**
         *  Find the number of children a node has
         *
         *  @param $id     The ID of the node to search for. Pass 0 to count the first level items
         *
         *  @returns The number of descendants the node has, or -1 if the node isn't found.
         */
        function numChildren( $id ) {

            // The query
            $query = sprintf(
                'select count(*) as num_children from %s where %s = %d', $this->table, $this->fields['parent'], $id
            );

            // Execute the query and get the record
            $record = $this->_getRecord( $query );

            // Return the number of descendants for the node
            if ( $record ) {
                return intval( $record['num_children'] );
            }

            // Return -1
            return -1;

        }

        /**
         *  Fetch the immediately family of a node. More specifically, fetch a node's parent, siblings and children. If 
         *  the node isn't valid, fetch the first level of nodes from the tree.
         *
         * @param $id   The ID of the node to fetch child data for.
         *
         * @returns An array of each node in the family
         */
        function getImmediateFamily( $id ) {

            // Get the node
            $node = $this->_toNodeArray( $this->getNode( $id ) );

            // The ID and parent field
            $idField = $this->fields['id'];
            $parentField = $this->fields['parent'];

            // Is the passed node valid?
            if ( $node[$idField] > 0 ) {
                $query = sprintf(
                    'select %s from %s where %s = %s or %s = %s or %s = %s order by nleft',
                    $this->_getFieldsAsString(), $this->table, $idField, $node[$parentField], $parentField,
                    $node[$parentField], $parentField, $node[$idField]
                );

            } else {
                $query = sprintf(
                    'select %s from %s where %s = 0 order by nleft',
                    $this->_getFieldsAsString(), $this->table, $parentField
                );
            }

            //  Get the result
            $records = $this->_getRecords( $query );

            // Get the result
            $arr = array();
            foreach ( $records as $record ) {
                $record['num_descendants'] = ( $record['nright'] - $record['nleft'] - 1 ) / 2;
                $arr[ $record[$idField] ] = $this->_fromNodeArray( $record );
            }

            // Return the result
            return $arr;

        }


        /**
         *  Fetch the tree data, nesting within each node references to the node's children
         *
         *  @returns The tree with the node's child data
         */
        function getTreeWithChildren() {

            // The ID and parent field
            $idField = $this->fields['id'];
            $parentField = $this->fields['parent'];

            // The query
            $query = sprintf(
                'select %s from %s order by %s', $this->_getFieldsAsString(), $this->table, $this->fields['sort']
            );

            // Get the records
            $records = $this->_getRecords( $query );

            // create a root node to hold child data about first level items
            $root = new YDBase();
            $root->$idField = 0;
            $root->children = array();
            $arr = array( $root );

            // populate the array and create an empty children array
            foreach ( $records as $record ) {
                $record = $this->_arrayToObj( $record );
                $arr[ $record->$idField ] = $record;
                $arr[ $record->$idField ]->children = array();
            }

            // now process the array and build the child data
            foreach ( $arr as $id => $row ) {
                if ( isset( $row->$parentField ) ) {
                    $arr[ $row->$parentField ]->children[ $id ] = $id;
                }
            }

            // Return the array
            return $arr;

        }

        /**
         *  Rebuilds the tree data and saves it to the database
         */
        function rebuild() {

            // Clear the cache
            $this->_clearCache();

            // Get the complete tree
            $data = $this->getTreeWithChildren();

            // Keep the original data (we need to optimize the updates)
            $data_ori = array_merge( $data );

            // Keep track of the number and level
            $n     = 0; // Need a variable to hold the running n tally
            $level = 0; // Need a variable to hold the running level tally

            // Invoke the recursive function. Start it processing on the fake "root node" generated in 
            // getTreeWithChildren(). because this node doesn't really exist in the database, we give it an initial 
            // nleft value of 0 and an nlevel of 0.
            $this->_generateTreeData( $data, 0, 0, $n );

            // At this point the the root node will have nleft of 0, nlevel of 0 and nright of (tree size * 2 + 1)
            foreach ( $data as $id=>$row ) {
                if ( $id == 0 ) {
                    continue;
                }
                if ( version_compare( phpversion(), '5' ) >= 0 ) {
                    $query = sprintf(
                        'update %s set nlevel = %d, nleft = %d, nright = %d where %s = %d',
                        $this->table, $row->nlevel, $row->nleft, $row->nright, $this->fields['id'], $id
                    );
                    $this->db->executeSql( $query );
                } else {
                    if (
                        $data[$id]->nlevel != $data_ori[$id]->nlevel ||
                        $data[$id]->nleft  != $data_ori[$id]->nleft ||
                        $data[$id]->nright != $data_ori[$id]->nright
                    ) {
                        $query = sprintf(
                            'update %s set nlevel = %d, nleft = %d, nright = %d where %s = %d',
                            $this->table, $row->nlevel, $row->nleft, $row->nright, $this->fields['id'], $id
                        );
                        $this->db->executeSql( $query );
                    }
                }

            }

        }

        /**
         *  Generate the tree data. A single call to this generates the n-values for 1 node in the tree. This function 
         *  assigns the passed in n value as the node's nleft value. It then processes all the node's children (which in
         *  turn recursively processes that node's children and so on), and when it is finally done, it takes the update
         *  n-value and assigns it as its nright value. Because it is passed as a reference, the subsequent changes in
         *  subrequests are held over to when control is returned so the nright can be assigned.
         *
         *  @param &$arr   A reference to the data array, since we need to be able to update the data in it
         *  @param $id     The ID of the current node to process
         *  @param $level  The nlevel to assign to the current node
         *  @param &$n     A reference to the running tally for the n-value
         *
         *  @internal
         */
        function _generateTreeData( &$arr, $id, $level, &$n ) {

            // Assign nlevel and nleft
            $arr[$id]->nlevel = $level;
            $arr[$id]->nleft = $n++;

            // Loop over the node's children and process their data before assigning the nright value
            foreach ( $arr[$id]->children as $child_id ) {
                $this->_generateTreeData( $arr, $child_id, $level + 1, $n );
            }

            // Assign nright
            $arr[$id]->nright = $n++;

        }

        /**
         *  This function adds a node to the database.
         *
         *  @param $values      The field values of the node.
         *  @param $parent_id   (optional) The parent ID of the node. The default takes it from the $values.
         */
        function addNode( $values, $parent_id=null ) {

            // Clear the cache
            $this->_clearCache();

            // Add the parent field if needed
            $parentField = $this->fields['parent'];
            if ( ! is_null( $parent_id ) && ! isset( $values[ $parentField ] ) ) {
                $values[ $parentField ] = $parent_id;
            }

            // Use 0 if no parent ID specified (the root element)
            if ( ! isset( $values[ $parentField ] ) ) {
                $values[ $parentField ] = 0;
            }

            // Check if the parent node exists
            if ( isset( $values[ $parentField ] ) && intval( $values[ $parentField ] ) != 0 ) {
                $node = $this->getNode( $values[ $parentField ] );
                if ( ! $node ) {
                    trigger_error( 'Parent node (' . $values[ $parentField ] . ') does not exist in the tree!', YD_ERROR );
                }
            }

            // Perform the insert
            $this->db->executeInsert( $this->table, $values );

            // Get the ID of the new node
            $id = $this->db->getLastInsertID();

            // Rebuild the tree
            $this->rebuild();

            // Return the ID
            return $id;

        }

        /**
         *  Delete the node and it's children.
         *
         *  @param $id  The ID of the node to delete.
         */
        function deleteNode( $id ) {

            // Clear the cache
            $this->_clearCache();

            // Get the node details
            $node = $this->getNode( $id );

            // Return if unknown node
            if ( ! $node ) {
                return;
            }

            // Get the children of the node
            $children = $this->getDescendants( $id );

            // Get the list of IDs to delete
            $nodes_to_delete = array();
            foreach ( $children as $child ) {
                array_push( $nodes_to_delete, $child['id'] );
            }

            // Add the current node
            array_push( $nodes_to_delete, $id  );

            // Check if there is something to delete
            if ( sizeof( $nodes_to_delete ) > 0 ) {

                // The query to execute
                $query = 'delete from ' . $this->table . ' where id in ( ' . join( ', ', $nodes_to_delete ) . ' )';

                // Delete the nodes
                $this->db->executeSql( $query );

                // Rebuild the tree
                $this->rebuild();

            }

        }

        /**
         *  Move a node to a different parent node.
         *
         *  @param  $id         The ID of the node to move.
         *  @param  $parent_id  The ID of the new parent node
         */
        function moveNode( $id, $parent_id ) {

            // Clear the cache
            $this->_clearCache();

            // The ID and parent field
            $idField = $this->fields['id'];
            $parentField = $this->fields['parent'];

            // Update the new parent id
            $values = array();
            $values[ $parentField ] = $parent_id;

            // Execute the update
            $this->db->executeUpdate( $this->table, $values, $idField . ' = ' . $id );

            // Rebuild the tree
            $this->rebuild();

        }

    }

?>