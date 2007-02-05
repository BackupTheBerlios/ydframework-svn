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

    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDDatabase.php' );

    /**
     *  This class implements a database tree as described on:
     *  http://www.phpriot.com/d/articles/php/application-design/nested-trees-2/
     *
     *  Schema description
     *
     *  @code
     *  CREATE TABLE category (
     *    id int(11) NOT NULL auto_increment,
     *    category varchar(50) NOT NULL default '0',
     *    PRIMARY KEY  (`id`)
     *  );
     *
     *  CREATE TABLE nested_tree (
     *      id int(11) NOT NULL auto_increment,
     *      parent_id int(11) NOT NULL default '0',
     *      title varchar(255) NOT NULL default '',
     *      nleft int(11) NOT NULL default '0',
     *      nright int(11) NOT NULL default '0',
     *      nlevel int(11) NOT NULL default '0',
     *      position int(11) NOT NULL default '0',
     *      cat_id int(11) NOT NULL default '0',
     *      PRIMARY KEY  (id),
     *      KEY nested_tree_parent_id (parent_id),
     *      KEY nested_tree_nleft (nleft),
     *      KEY nested_tree_nright (nright),
     *      KEY nested_tree_nlevel (nlevel)
     *  );
     *  @endcode
     *
     *  Default values for testing
     *
     *  @code
     *  INSERT INTO category VALUES (1,'cat1');
     *
     *  INSERT INTO nested_tree VALUES (1,0,'General Resources',1,22,1,0,1);
     *  INSERT INTO nested_tree VALUES (2,1,'Code Paste',2,3,2,0,1);
     *  INSERT INTO nested_tree VALUES (3,1,'Documentation',4,5,2,0,1);
     *  INSERT INTO nested_tree VALUES (4,1,'Books & Publications',6,13,2,0,1);
     *  INSERT INTO nested_tree VALUES (5,4,'Apache',7,8,3,0,1);
     *  INSERT INTO nested_tree VALUES (6,4,'PostgreSQL',9,10,3,0,1);
     *  INSERT INTO nested_tree VALUES (7,4,'MySQL',11,12,3,0,1);
     *  INSERT INTO nested_tree VALUES (8,1,'Links',14,21,2,0,1);
     *  INSERT INTO nested_tree VALUES (9,8,'Databases',15,16,3,0,1);
     *  INSERT INTO nested_tree VALUES (10,8,'Generators',17,18,3,0,1);
     *  INSERT INTO nested_tree VALUES (11,8,'Portals',19,20,3,0,1);
     *  @endcode
     *
     *  Sample data dump
     *
     *  @code
     *  +----+----------+
     *  | id | category |
     *  +----+----------+
     *  |  1 | cat1     |
     *  +----+----------+
     *
     *  +----+-----------+----------------------+-------+--------+--------+----------+--------+
     *  | id | parent_id | title                | nleft | nright | nlevel | position | cat_id |
     *  +----+-----------+----------------------+-------+--------+--------+----------+--------+
     *  |  1 |         0 | General Resources    |     1 |     22 |      1 |        0 |      1 |
     *  |  2 |         1 | Code Paste           |    10 |     11 |      2 |        0 |      1 |
     *  |  3 |         1 | Documentation        |    12 |     13 |      2 |        0 |      1 |
     *  |  4 |         1 | Books & Publications |     2 |      9 |      2 |        0 |      1 |
     *  |  5 |         4 | Apache               |     3 |      4 |      3 |        0 |      1 |
     *  |  6 |         4 | PostgreSQL           |     7 |      8 |      3 |        0 |      1 |
     *  |  7 |         4 | MySQL                |     5 |      6 |      3 |        0 |      1 |
     *  |  8 |         1 | Links                |    14 |     21 |      2 |        0 |      1 |
     *  |  9 |         8 | Databases            |    15 |     16 |      3 |        0 |      1 |
     *  | 10 |         8 | Generators           |    17 |     18 |      3 |        0 |      1 |
     *  | 11 |         8 | Portals              |    19 |     20 |      3 |        0 |      1 |
     *  +----+-----------+----------------------+-------+--------+--------+----------+--------+
     *  @endcode
     *
     *  @todo
     *      Change the sort fields from a string separated list of fields to an array so that we can properly remove and
     *      add the table prefix. Right now, it should work but there might be cases where it's failing.
     *
     *  @todo
     *      Add support for moving items up and down. The algorithm can be something like:
     *          - Move up: currentItem+1, nextItem-1
     *          - Move down: currentItem-1, previousItem+1
     *
     *  @ingroup YDDatabase
     */
    class YDDatabaseTree extends YDBase {

        /**
         *  Constructor. Set the database table name and necessary field names
         *
         *  @param $db             The YDDatabase instance name or object pointing to the database
         *  @param $table          Name of the tree database table
         *  @param $idField        (optional) Name of the primary key ID field. Default is id.
         *  @param $parentField    (optional) Name of the parent ID field. Default is parent_id.
         *  @param $sortField      (optional) Name of the field to sort data. Default is position, title.
         */
        function YDDatabaseTree( $db='default', $table, $idField='id', $parentField='parent_id', $sortField='position, title' ) {
            $this->db = YDDatabase::getNamedInstance( $db );
            $this->table = $table;
            $this->tablePrefix = $table;
            $this->joins = array();
            $this->fields = array(
                'id'        => $this->_addTablePrefix( $idField ),
                'parent'    => $this->_addTablePrefix( $parentField ),
                'sort'      => $this->_addTablePrefix( $sortField ),
                'nleft'     => $this->_addTablePrefix( 'nleft' ),
                'nright'    => $this->_addTablePrefix( 'nright' ),
                'nlevel'    => $this->_addTablePrefix( 'nlevel' ),
                'position'  => $this->_addTablePrefix( 'position' ),
            );
            $this->_use_query_cache = true;
            $this->_query_cache = array();
        }

        /**
         *  This function adds the default table prefix to the field name.
         *
         *  @param  $field  The name of the field to add the prefix to.
         *
         *  @returns    The name of the field with the prefix added to it.
         */
        function _addTablePrefix( $field ) {
            $field = explode( ', ', $field );
            foreach ( $field as $key=>$val ) {
                if ( strpos( $val, '.' ) === false && ! YDStringUtil::startsWith( $val, $this->tablePrefix . '.' ) ) {
                    $val = $this->tablePrefix . '.' . $val;
                }
                $field[$key] = $val;
            }
            return join( ', ', $field );
        }

        /**
         *  This function removes the default table prefix to the field name.
         *
         *  @param  $field  The name of the field to remove the prefix from.
         *
         *  @returns    The name of the field with the prefix removed from it.
         */
        function _removeTablePrefix( $field ) {
            $field = explode( ', ', $field );
            foreach ( $field as $key=>$val ) {
                if ( YDStringUtil::startsWith( $val, $this->tablePrefix . '.' ) ) {
                    $field[$key] = substr( $val, strlen( $this->tablePrefix )+1 );
                }
                $pos = strpos( $val, '.' );
                if ( $pos !== false ) {
                    $field[$key] = substr( $val, $pos+1 );
                }
            }
            return join( ', ', $field );
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
            return join( ', ', $this->_getFields() );
        }

        /**
         *  A utility function to return an array of the tables that need to be selected in SQL select queries.
         *
         *  @returns array   An indexed array of tables to select
         *
         *  @internal
         */
        function _getTables() {
            $result = array_values( $this->joins );
            array_unshift( $result, $this->table . ' ' . $this->tablePrefix );
            return $result;
        }

        /**
         *  Get the tables and joins joined as single string.
         *
         *  @returns A single string with the tables and joins joined as a string
         *
         *  @internal
         */
        function _getTablesAsString() {
            return join( ' ', $this->_getTables() );
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
            $arr = array();
            foreach ( $this->fields as $fieldItem ) {
                $fieldItem = explode( ', ', $this->_removeTablePrefix( $fieldItem ) );
                foreach( $fieldItem as $f ) {
                    if ( isset( $node[ $f ] ) ) {
                        $arr[ $f ] = $node[ $f ];
                    }
                }
            }
            return $arr;
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
         *  Add a join table to the list of tables to will get returned by this class.
         *
         *  @param  $join   The full join statement such as LEFT JOIN x ON x = Z
         */
        function addJoinTable( $join ) {
            if ( ! in_array( $join, $this->joins ) ) {
                $this->joins[ $join ] = $join;
            }
        }

        /**
         *  Set the sort field.
         *
         *  @param $sortField      (optional) Name of the field to sort data. Default is title.
         */
        function setSortField( $sortField='title' ) {
            $this->fields[ 'sort' ] = $sortField;
        }

        /**
         *  Fetch the node data for the node identified by $id.
         *
         *  @param $id      The ID of the node to fetch.
         *  @param $field   The unique field to select on. Defaults to null, which means that the ID field specified
         *                  when the object was instantiated will be used.
         *
         *  @returns An object containing the node's data, or false if node not found
         */
        function getNode( $id, $field=null ) {

            // Get the name of the field
            $field = is_null( $field ) ? $this->fields['id'] : $field;
            $field = empty( $field )   ? $this->fields['id'] : $field;
            $field = $this->_addTablePrefix( $field );

            // The query to execute
            if ( is_int( $id ) ) {
                $query = sprintf(
                    'select %s from %s where %s = %d',
                    $this->_getFieldsAsString(), $this->_getTablesAsString(), $field, $id
                );
            } else {
                $query = sprintf(
                    'select %s from %s where %s = \'%s\'',
                    $this->_getFieldsAsString(), $this->_getTablesAsString(), $field, $id
                );
            }

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
         *  @param $order           (optional) The order of the records to return.
         *  @param $specificPart    (optional) Return array should be not-associative (using a specific key to filter)
         *
         *  @returns The descendants of the passed now
         */
        function getDescendants(
            $id=0, $includeSelf=false, $childrenOnly=false, $max_level=null, $order=null, $specificPart=null
        ) {

            // Get the ID field
            $idField = $this->fields['id'];

            // Get the node if it's not given by a numeric ID
            $node = is_numeric( $id ) ? $this->getNode( $id ) : $id;

            // Convert the node
            $node = $this->_toNodeArray( $node );

            // Find nleft, nright and parent_id
            if ( ! $node ) {
                $nleft = 0;
                $nright = 0;
                $parent_id = 0;
            } else {
                $nleft = $node['nleft'];
                $nright = $node['nright'];
                $parent_id = $node[$this->_removeTablePrefix($idField)];
            }

            // Get the order
            if ( empty( $order ) ) {
                $order = 'order by nleft';
            } else {
                $order = 'order by ' . $order . ', nleft';
            }

            // Children only
            if ( $childrenOnly ) {

                // Include ourselves?
                if ( $includeSelf ) {
                    $query = sprintf(
                        'select %s from %s where %s = %d or %s = %d %s',
                        $this->_getFieldsAsString(), $this->_getTablesAsString(),
                        $this->_addTablePrefix( $this->fields['id'] ), $parent_id,
                        $this->_addTablePrefix( $this->fields['parent'] ), $parent_id, $order
                    );
                } else {
                    $query = sprintf(
                        'select %s from %s where %s = %d %s',
                        $this->_getFieldsAsString(), $this->_getTablesAsString(),
                        $this->_addTablePrefix( $this->fields['parent'] ), $parent_id, $order
                    );
                }

            } else {

                // Include all
                if ( $nleft > 0 && $includeSelf ) {
                    $query = sprintf(
                        'select %s from %s where %s.nleft >= %d and %s.nright <= %d %s',
                        $this->_getFieldsAsString(), $this->_getTablesAsString(),
                        $this->tablePrefix, $nleft, $this->tablePrefix, $nright, $order
                    );
                } else if ( $nleft > 0 ) {
                    $query = sprintf(
                        'select %s from %s where %s.nleft > %d and %snright < %d %s',
                        $this->_getFieldsAsString(), $this->_getTablesAsString(),
                        $this->tablePrefix, $nleft, $this->tablePrefix, $nright, $order
                    );
                } else {
                    $query = sprintf(
                        'select %s from %s where %s.id > 0 %s',
                        $this->_getFieldsAsString(), $this->_getTablesAsString(), $this->tablePrefix, $order
                    );
                }

            }

            // Add the level constraint
            if ( ! is_null( $max_level ) ) {
                $max_level = ( $includeSelf ) ? $max_level : $max_level + 1;
                $max_level = ( $includeSelf ) ? $node['nlevel'] + $max_level - 1 : $node['nlevel'] + $max_level;
                $query = str_replace( 'where ', 'where ' . $this->tablePrefix . '.nlevel <= ' . $max_level . ' and ', $query );
            }

            // Get the results as an array
            $records = $this->_getRecords( $query );

            // Reformat the array
            $arr = array();
            foreach ( $records as $record ) {
                $arr[ $record[$this->_removeTablePrefix($idField)] ] = $this->_fromNodeArray( $record );
            }

            // check if we want a specific column
            if ( !is_null( $specificPart ) ){

                // init temporary array
                $nodes = array();
                foreach ( $arr as $node )
                    array_push( $nodes, $node[ $this->_removeTablePrefix($specificPart) ] );

                return $nodes;
            }

            // Return the result
            return $arr;

        }

        /**
         *  Fetch the children of a node, or if no node is specified, fetch the top level items.
         *
         *  @param $id             (optional) The ID of the node to fetch child data for.
         *  @param $includeSelf    (optional) Whether or not to include the passed node in the the results.
         *  @param $order          (optional) The order of the records to return.
         *
         *  @returns The children of the passed node
         */
        function getChildren( $id=0, $includeSelf=false, $order=null ) {
            return $this->getDescendants( $id, $includeSelf, true, null, $order );
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

            // Get the node if it's not given by a numeric ID
            $node = is_numeric( $id ) ? $this->getNode( $id ) : $id;

            // Convert the node
            $node = $this->_toNodeArray( $node );

            // No node, return empty array
            if ( ! $node ) {
                return array();
            }

            // Include ourselves?
            if ( $includeSelf ) {
                $query = sprintf(
                    'select %s from %s where ( %s.nleft <= %d and %s.nright >= %d ) or %s.id = %d order by %s.nlevel',
                    $this->_getFieldsAsString(), $this->_getTablesAsString(),
                    $this->tablePrefix, $node['nleft'], $this->tablePrefix, $node['nright'], $this->tablePrefix,
                    $id, $this->tablePrefix
                );
            } else {
                $query = sprintf(
                    'select %s from %s where %s.nleft < %d and %s.nright > %d order by %s.nlevel',
                    $this->_getFieldsAsString(), $this->_getTablesAsString(),
                    $this->tablePrefix, $node['nleft'], $this->tablePrefix, $node['nright'], $this->tablePrefix
                );
            }

            // Get the results as an array
            $records = $this->_getRecords( $query );

            // Reformat the array
            $arr = array();
            foreach ( $records as $record ) {
                $arr[ $record[$this->_removeTablePrefix($idField)] ] = $this->_fromNodeArray( $record );
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

            // Get the node if it's not given by a numeric ID
            $node = is_numeric( $ancestor_id ) ? $this->getNode( $ancestor_id ) : $ancestor_id;

            // Convert the node
            $node = $this->_toNodeArray( $node );

            // No node, return empty array
            if ( ! $node ) {
                return false;
            }

            // The query
            $query = sprintf(
                'select count(*) as is_descendant from %s where %s = %d and %s.nleft > %d and %s.nright < %d',
                $this->_getTablesAsString(), $this->_addTablePrefix( $this->fields['id'] ), $descendant_id,
                $this->tablePrefix, $node['nleft'], $this->tablePrefix, $node['nright']
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
                $this->_getTablesAsString(), $this->_addTablePrefix( $this->fields['id'] ),
                $child_id, $this->_addTablePrefix( $this->fields['parent'] ), $parent_id
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
                $query = sprintf('select count(*) as num_descendants from %s', $this->_getTablesAsString() );

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
                'select count(*) as num_children from %s where %s = %d',
                $this->_getTablesAsString(), $this->_addTablePrefix( $this->fields['parent'] ), $id
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

            // Get the node if it's not given by a numeric ID
            $node = is_numeric( $id ) ? $this->getNode( $id ) : $id;

            // Convert the node
            $node = $this->_toNodeArray( $node );

            // The ID and parent field
            $idField = $this->_addTablePrefix( $this->fields['id'] );
            $parentField = $this->_addTablePrefix( $this->fields['parent'] );

            // Is the passed node valid?
            if ( $node[$this->_removeTablePrefix($idField)] > 0 ) {
                $query = sprintf(
                    'select %s from %s where %s = %s or %s = %s or %s = %s order by nleft',
                    $this->_getFieldsAsString(), $this->_getTablesAsString(), $idField, $node[$parentField], $parentField,
                    $node[$parentField], $parentField, $node[$idField]
                );

            } else {
                $query = sprintf(
                    'select %s from %s where %s = 0 order by nleft',
                    $this->_getFieldsAsString(), $this->_getTablesAsString(), $parentField
                );
            }

            //  Get the result
            $records = $this->_getRecords( $query );

            // Get the result
            $arr = array();
            foreach ( $records as $record ) {
                $record['num_descendants'] = ( $record['nright'] - $record['nleft'] - 1 ) / 2;
                $arr[ $record[$this->_removeTablePrefix($idField)] ] = $this->_fromNodeArray( $record );
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
            $idField     = $this->_removeTablePrefix( $this->fields['id'] );
            $parentField = $this->_removeTablePrefix( $this->fields['parent'] );

            // The query
            $query = sprintf(
                'select %s from %s order by %s',
                $this->_getFieldsAsString(), $this->_getTablesAsString(), $this->_addTablePrefix( $this->fields['sort'] )
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
         *
         *  @todo
         *      Check if there is any way of optimizing this part so that we don't have to issue and update statement
         *      for each item in the table. If I understand the algorithm correctly, only part of the tree data should
         *      be updated when we change an item.
         */
        function rebuild() {

            // Clear the cache
            $this->_clearCache();

            // Get the complete tree
            $data = $this->getTreeWithChildren();

            // Keep the original data (we need to optimize the updates)
            $data_ori = array_merge( $data );

            // Keep track of the number and level
            $n     = 0;
            $level = 0;

            // Invoke the recursive function. Start it processing on the fake "root node" generated in
            // getTreeWithChildren(). because this node doesn't really exist in the database, we give it an initial
            // nleft value of 0 and an nlevel of 0.
            $this->_generateTreeData( $data, 0, 0, $n );

            // At this point the the root node will have nleft of 0, nlevel of 0 and nright of (tree size * 2 + 1)
            foreach ( $data as $id=>$row ) {
                if ( $id == 0 ) {
                    continue;
                }
                @ $query = sprintf(
                    'update %s set nlevel = %d, nleft = %d, nright = %d where %s = %d',
                    $this->table, $row->nlevel, $row->nleft, $row->nright,
                    $this->_removeTablePrefix( $this->fields['id'] ), $id
                );
                $this->db->executeSql( $query );
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
         *
         *  @returns    The ID of the newly inserted node.
         */
        function addNode( $values, $parent_id=null ) {

            // Convert the values from an ojbect to an array
            $values = $this->_toNodeArray( $values );

            // Clear the cache
            $this->_clearCache();

            // Add the parent field if needed
            $parentField = $this->_removeTablePrefix( $this->fields['parent'] );
            if ( ! is_null( $parent_id ) && ! isset( $values[ $parentField ] ) ) {
                $values[ $parentField ] = $parent_id;
            }

            // Use 0 if no parent ID specified (the root element)
            if ( ! isset( $values[ $parentField ] ) ) {
                $values[ $parentField ] = 0;
            }

            // Remove the table prefixes from the values
            foreach ( $values as $key=>$val ) {
                unset( $values[ $key ] );
                $values[ $this->_removeTablePrefix( $key ) ] = $val;
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
         *  This function updates a node in the database.
         *
         *  @param $values      The field values of the node.
         *  @param $id          (optional) The ID of the node to update.
         *
         *  @returns    Total of lines affected
         */
        function updateNode( $values, $id=null ) {

            // The ID and parent field
            $idField = $this->fields['id'];

            // Convert the values from an ojbect to an array
            $values = $this->_toNodeArray( $values );

            // Clear the cache
            $this->_clearCache();

            // Get the ID of the node to update
            $id = empty( $id ) ? $values['id'] : $id;

            // Perform the insert
            $res = $this->db->executeUpdate(
                $this->table, $values, sprintf( '%s = %s', $idField, $this->db->escapeSql( $id ) )
            );

            // Rebuild the tree
            $this->rebuild();

            // Return total of lines affected
            return $res;
        }


        /**
         *  Delete the node and it's children.
         *
         *  @param $id             The ID of the node to delete.
         *  @param $includeParent  (Optional) Delete id and all children (true by default. if false, deletes children only)
         */
        function deleteNode( $id, $includeParent = true ) {

            // Clear the cache
            $this->_clearCache();

            // Get the node details
            $node = $this->getNode( $id );

            // Return if unknown node
            if ( ! $node ) {
                return;
            }

            // Get the list of IDs to delete
            $nodes_to_delete = $this->getDescendants( $id, $includeParent, false, null, null, $this->fields['id'] );

            // Check if there is something to delete
            if ( sizeof( $nodes_to_delete ) > 0 ) {

                // The query to execute
                $query = sprintf(
                    'DELETE FROM %s WHERE %s IN ( %s )',
                    $this->table, $this->_removeTablePrefix( $this->fields['id'] ), join( ', ', $nodes_to_delete )
                );

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