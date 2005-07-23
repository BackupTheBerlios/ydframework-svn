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

    /**
     *  The YDDatabaseQuery class defines an object-oriented interface to create SQL queries.
     */
    class YDDatabaseQuery extends YDAddOnModule {

        /**
         *  This is the class constructor for the YDDatabaseQuery class.
         */
        function YDDatabaseQuery() {

            // Initialize the parent
            $this->YDAddOnModule();

            // Setup the module
            $this->_author = 'David Bittencourt';
            $this->_version = '1.0';
            $this->_copyright = '(c) 2005 David Bittencourt, muitocomplicado@hotmail.com';
            $this->_description = 'This class defines a YDDatabaseQuery object.';
            
        }

        /**
         *  This function creates a new YDDatabaseQuery class for the specified database engine.
         *
         *  @param  $db The YDDatabaseDriver object to use.
         *
         *  @returns    Returns a new YDDatabaseQuery class.
         */
        function getInstance( & $db ) {

            // Get the driver name
            $driver = $db->getDriver();
            
            // The list of known drivers
            $regDrivers = array();

            // Register the standard drivers
            $regDrivers[ strtolower( 'mysql' ) ] = array(
                'class' => 'YDDatabaseQueryDriver_mysql', 'file' => ''
            );
            $regDrivers[ strtolower( 'oracle' ) ] = array(
                'class' => 'YDDatabaseQueryDriver_oracle', 'file' => 'YDDatabaseQueryDriver_oracle.php'
            );
            $regDrivers[ strtolower( 'postgres' ) ] = array(
                'class' => 'YDDatabaseQueryDriver_postgres', 'file' => 'YDDatabaseQueryDriver_postgres.php'
            );
            $regDrivers[ strtolower( 'sqlite' ) ] = array(
                'class' => 'YDDatabaseQueryDriver_sqlite', 'file' => 'YDDatabaseQueryDriver_sqlite.php'
            );
            
            // Check if the driver exists
            if ( ! array_key_exists( strtolower( $driver ), $regDrivers ) ) {
                trigger_error( 'Unsupported database type: "' . $driver . '".', YD_ERROR );
            }

            // Include the driver
            if ( ! empty( $regDrivers[ strtolower( $driver ) ]['file'] ) ) {
                include_once( dirname( __FILE__ ) . '/YDDatabaseQueryDrivers/' . $regDrivers[ strtolower( $driver ) ]['file'] );
            }
            
            // Check if the driver is supported
            if ( ! call_user_func( array( $regDrivers[ strtolower( $driver ) ]['class'], 'isSupported' ) ) ) {
                trigger_error( 'Unsupported database type: "' . $driver . '". Extension is not loaded.', YD_ERROR );
            }

            // Make a new connection object and return it
            $className = $regDrivers[ strtolower( $driver ) ]['class'];
            return new $className( $db );
        }
        
    }

    /**
     *  This class defines a YDSqlQueryDriver object.
     */
    class YDDatabaseQueryDriver extends YDBase {

        var $action   = 'SELECT';
        var $options  = array();
        var $select   = array();
        var $tables   = array();
        var $join     = array();
        var $where    = array();
        var $group    = array();
        var $having   = array();
        var $order    = array();
        var $values   = array();
        var $joinon   = array();
        var $limit    = -1;
        var $offset   = -1;
        var $reserved = '`';
        var $quote    = "'";
        var $filter   = true;
        
        /**
         *  The class constructor can be used to set the action and optional options.
         */
         function YDDatabaseQueryDriver( & $db ) {
            
            $this->YDBase();
            
            $this->db = & $db;
            
            $this->reset();
            $this->action();
            
        }
        
        /**
         *  This function will check if the server supports this database type.
         *
         *  @returns    Boolean indicating if the database type is supported by the server.
         */
        function isSupported() {
            return true;
        }
        
        /**
         *  This function sets the action to INSERT.
         */
        function insert() {
            $this->action( 'INSERT' );
        }
        
        /**
         *  This function sets the action to SELECT.
         */
        function select() {
            $this->action( 'SELECT' );
        }
        
        /**
         *  This function sets the action to UPDATE.
         */
        function update() {
            $this->action( 'UPDATE' );
        }

        /**
         *  This function sets the action to DELETE.
         */
        function delete() {
            $this->action( 'DELETE' );
        }

        /**
         *  This function sets the action for the query.
         *
         *  @param $action   (optional) Name of the action. Should be one of these:
         *                   SELECT, INSERT, UPDATE, DELETE. Default: SELECT.
         */
        function action( $action='SELECT' ) {
            $this->action = strtoupper( $action );
        }
        
        /**
         *  This function sets the options for the query.
         *
         *  @param $options  Array of flags to be set after the action.
         *                   e.g. DISTINCT, SQL_CACHE, ALL, etc.
         */
        function options( $options ) {
            $this->options = $options;
        }

        /**
         *  Adds a table to the query.
         *
         *  @param $table  The name of the table.
         *  @param $alias  (optional) The table alias.
         *
         *  @returns       The table alias if defined or the table name.
         */
        function table( $table, $alias='' ) {

            $alias = ( $alias == $table ) ? '' : $alias;
            
            if ( strlen( $alias ) ) {
                foreach ( $this->tables as $arr ) {
                    if ( $alias == $arr['alias'] ) {
                        trigger_error( 'Table alias "' . $arr['alias'] . '" already defined.', YD_ERROR );
                    }
                }
            }
            $this->tables[] = array( 'name' => $table, 'alias' => $alias );

            return strlen( $alias ) ? $alias : $table;
        }
        
        /**
         *  Alias of table.
         *
         *  @param $table  The name of the table.
         *  @param $alias  (optional) The table alias.
         *
         *  @returns       The table alias if defined or the table name.
         */
        function from( $table, $alias='' ) {
            return $this->table( $table, $alias );
        }

        /**
         *  Alias of table.
         *
         *  @param $table  The name of the table.
         *  @param $alias  (optional) The table alias.
         *
         *  @returns       The table alias if defined or the table name.
         */
        function into( $table, $alias='' ) {
            return $this->table( $table, $alias );
        }

        /**
         *  Adds a join table to the query.
         *
         *  @param $type        Should be one of these: LEFT, LEFT OUTER, RIGHT, RIGHT OUTER, INNER, CROSS
         *  @param $table       The joining table name.
         *  @param $alias       (optional) The joining table alias.
         *
         *  @returns   The alias for the joining table.
         */
        function join( $type, $table, $alias='' ) {

            if ( ! sizeof( $this->tables ) ) {
                trigger_error( 'No tables defined to join.', YD_ERROR );
            }

            end( $this->tables );

            $this->join[ key( $this->tables ) ] = array( 'type' => $type,
                                         'table' => $table,
                                         'alias' => ( $alias == $table ) ? '' : $alias );

            $this->joinon[ key( $this->tables ) ] = array();

            return $this->table( $table, $alias );
        }

        /**
         *  Adds a join condition for the last join defined.
         *
         *  @param $expr        The expression.
         *  @param $logic       (optional) Logic operator (e.g. AND, OR, XOR). Default: AND.
         */
        function on( $expr, $logic='AND' ) {
            end( $this->tables );
            $this->joinon[ key( $this->tables )-1 ][] = array( 'expr' => $expr,
                                                               'logic' => strtoupper( $logic ) );
        }

        /**
         *  Opens a group of JOIN conditions.
         *
         *  @param $logic  (optional) Logic operator (e.g. AND, OR, XOR). Default: AND.
         */
        function openOn( $logic='AND' ) {
            end( $this->tables );
            $this->joinon[ key( $this->tables )-1 ][] = array( 'logic' => strtoupper( $logic ) );
        }
        
        /**
         *  Closes a group of JOIN conditions. This method is optional as the class
         *  will automatically close all groups that weren't closed.
         */
        function closeOn() {
            end( $this->tables );
            $this->joinon[ key( $this->tables )-1 ][] = array();
        }

        /**
         *  Alias of expression.
         *
         *  @param $expr      The expression.
         *  @param $alias     (optional) The alias for the expression.
         *  @param $reserved  (optional) Escapes the expression as a reserved keyword.
         */
        function expr( $expr, $alias='', $reserved=false ) {
            $this->expression( $expr, $alias, $reserved );
        }
        
        /**
         *  Adds a select expression.
         *
         *  @param $expr      The expression.
         *  @param $alias     (optional) The alias for the expression.
         *  @param $reserved  (optional) Escapes the expression as a reserved keyword.
         */
        function expression( $expr, $alias='', $reserved=false ) {            
            if ( strlen( $expr ) ) {
                $alias = ( $alias == $expr ) ? '' : $alias;
                if ( $alias ) {
                    foreach ( $this->select as $arr ) {
                        if ( $arr['alias'] == $alias ) {
                            trigger_error( 'Select alias "' . $arr['alias'] . '" already defined.', YD_ERROR );
                        }
                    }
                }
                $this->select[] = array( 'expr'     => $expr,
                                         'alias'    => $alias,
                                         'reserved' => $reserved );
            }
        }

        /**
         *  Adds a column to the ORDER BY clause.
         *
         *  @param $expr      The expression.
         *  @param $desc      (optional) If true adds a DESC string to the column. Default: false.
         *  @param $reserved  (optional) Escapes the expression as a reserved keyword.
         */
        function order( $expr, $desc=false, $reserved=false ) {
            if ( strlen( $expr ) ) {
                $this->order[] = array( 'expr'     => $expr,
                                        'desc'     => $desc,
                                        'reserved' => $reserved );
            }
        }

        /**
         *  Alias of order.
         *
         *  @param $expr      The expression.
         *  @param $desc      (optional) If true adds a DESC string to the column. Default: false.
         *  @param $reserved  (optional) Escapes the expression as a reserved keyword.
         */
        function orderby( $expr, $desc=false, $reserved=false ) {
            $this->order( $expr, $desc, $reserved );
        }

        /**
         *  Adds an expression to the GROUP BY clause.
         *
         *  @param $expr  The expression.
         *  @param $desc  (optional) If true adds a DESC string to the expression. Default: false.
         *  @param $reserved  (optional) Escapes the expression as a reserved keyword.
         */
        function group( $expr, $desc=false, $reserved=false ) {
            if ( strlen( $expr ) ) {
                $this->group[] = array( 'expr'     => $expr,
                                        'desc'     => $desc,
                                        'reserved' => $reserved );
            }
        }

        /**
         *  Alias of group.
         *
         *  @param $expr      The expression.
         *  @param $desc      (optional) If true adds a DESC string to the column. Default: false.
         *  @param $reserved  (optional) Escapes the expression as a reserved keyword.
         */
        function groupby( $expr, $desc=false, $reserved=false ) {
            $this->group( $expr, $desc, $reserved );
        }

        /**
         *  Adds an expression to the HAVING clause.
         *
         *  @param $expr   The expression.
         *  @param $logic  (optional) Logic operator (e.g. AND, OR, XOR). Default: AND.
         */
        function having( $expr, $logic='AND' ) {
            if ( strlen( $expr ) ) {
                $this->having[] = array( 'expr'  => $expr,
                                         'logic' => strtoupper( $logic ) );
            }
        }

        /**
         *  Adds a condition to the WHERE clause.
         *
         *  @param $expr   The condition expression.
         *  @param $logic  (optional) Logic operator (e.g. AND, OR, XOR). Default: AND.
         */
        function where( $expr, $logic='AND' ) {
            if ( strlen( $expr ) ) {
                $this->where[] = array( 'expr'  => $expr,
                                        'logic' => strtoupper( $logic ) );
            }
        }

        /**
         *  Opens a group of WHERE conditions.
         *
         *  @param $logic  (optional) Logic operator (e.g. AND, OR, XOR). Default: AND.
         */
        function openWhere( $logic='AND' ) {
            $this->where[] = array( 'logic' => strtoupper( $logic ) );
        }

        /**
         *  Closes a group of WHERE conditions. This method is optional as the class
         *  will automatically close all groups that weren't closed.
         */
        function closeWhere() {
            $this->where[] = array();
        }

        /**
         *  This function sets the LIMIT.
         *
         *  @param $limit  (optional) The limit.
         */
        function limit( $limit=-1 ) {
            $this->limit = $limit;
        }

        /**
         *  This function sets the OFFSET.
         *
         *  @param $offset  (optional) The offset.
         */
        function offset( $offset=-1 ) {
            $this->offset = $offset;
        }

        /**
         *  Sets the values to be used in a UPDATE or INSERT query.
         *
         *  @param $values   The associative array with the column names
         *                   as keys and it's values.
         *  @param $filter   (optional) Don't include columns that start with underscore.
         *                   Default: true.
         */
        function set( $values, $filter=true ) {
            $this->values = $values;
            $this->filter = $filter;
        }

        /**
         *  Alias of set.
         *
         *  @param $values   The associative array with the column names
         *                   as keys and it's values.
         *  @param $filter   (optional) Don't include columns that start with underscore.
         *                   Default: true.
         */
        function values( $values, $filter=true ) {
            $this->set( $values, $filter );
        }
        
        /**
         *  Returns a quoted string for reserved words.
         *
         *  @param $string   The string.
         *  
         *  @returns         The quoted string.
         */
        function reserved( $string ) {
            return $this->reserved . $string . $this->reserved;
        }
        
        /**
         *  Returns the character to quote reserved words.
         *  
         *  @returns         The character.
         */
        function getReserved() {
            return $this->reserved;
        }

        /**
         *  Returns the quote character.
         *  
         *  @returns         The character.
         */
        function getQuote() {
            return $this->quote;
        }

        /**
         *  This function builds the tables references expression and returns it.
         *
         *  @param $title       (optional) If true, returns the FROM string. Default: true.
         *
         *  @returns  The tables references expression.
         */
        function getFrom( $title=true ) {

            $from = array();

            foreach ( $this->tables as $index => $arr ) {

                $sql = '';
                
                if ( ! isset( $this->join[ $index-1 ] ) ) {

                    $table = $arr['name'];
                    $alias = $arr['alias'];
                    
                    $sql  = sizeof( $from ) ? ', ' : '';
                    $sql .= $this->reserved( $table );
                    if ( strlen( $alias ) ) {
                        $sql .= ' AS ' . $this->reserved( $alias );
                    }
                    
                }

                if ( array_key_exists( $index, $this->join ) ) {

                    $join = $this->join[ $index ];
                    $sql .= ' ' . $join['type'] . ' JOIN ';
                    $sql .= $this->reserved( $join['table'] );
                    if ( strlen( $join['alias'] ) ) {
                        $sql .= ' AS ' . $this->reserved( $join['alias'] );
                    }

                    if ( array_key_exists( $index, $this->joinon ) ) {
                        $sql .= $this->getOn( $index );
                    }
                    
                }

                $from[] = $sql;
            }

            return ( $title ? ' FROM ' : '' ) . trim( implode( '', $from ) );

        }

        /**
         *  This function builds the ON expression and returns it.
         *
         *  @param $index  The index of the JOIN.
         *  @param $title  (optional) If true, returns the WHERE string. Default: true.
         *
         *  @returns  The conditions expression.
         */
        function getOn( $index, $title=true ) {

            $sql = $this->_getConditions( $this->joinon[ $index ] );

            if ( strlen( trim( $sql ) ) ) {
                return ( $title ? ' ON ' : '' ) . '( ' . $sql . ' )';
            }
            return '';

        }

        /**
         *  This function builds the WHERE expression and returns it.
         *
         *  @param $title  (optional) If true, returns the WHERE string. Default: true.
         *
         *  @returns  The conditions expression.
         */
        function getWhere( $title=true ) {

            $sql = $this->_getConditions( $this->where );

            if ( strlen( trim( $sql ) ) ) {
                return ( $title ? ' WHERE ' : '' ) . $sql;
            }
            return '';

        }

        /**
         *  This functions builds the HAVING expression and returns it.
         *
         *  @param $title  (optional) If true, returns the HAVING string. Default: true.
         *
         *  @returns  The GROUP BY expression.
         */
        function getHaving( $title=true ) {

            if ( ! sizeof( $this->having ) ) {
                return '';
            }

            $having = array();
            foreach ( $this->having as $arr ) {
                $having[] = ( sizeof( $having ) ? $arr['logic'] . ' ' : '' ) . $arr['expr'];
            }
            return ( $title ? ' HAVING ' : '' ) . implode( ' ', $having );
        }

        /**
         *  This functions builds the ORDER BY expression and returns it.
         *
         *  @param $title  (optional) If true, returns the ORDER BY string. Default: true.
         *
         *  @returns  The ORDER BY expression.
         */
        function getOrder( $title=true ) {

            if ( ! sizeof( $this->order ) ) {
                return '';
            }
            $order = array();
            foreach ( $this->order as $arr ) {
                $sql = $arr['expr'];
                if ( $arr['reserved'] ) {
                    $parts = explode( '.', $arr['expr'] );
                    foreach ( $parts as $k => $exp ) {
                        $parts[$k] = $this->reserved( $exp );
                    }
                    $sql = implode( '.', $parts );
                } 
                $sql .= $arr['desc'] ? ' DESC' : '';
                
                $order[] = $sql;
            }
            return ( $title ? ' ORDER BY ' : '' ) . implode( ', ', $order );

        }

        /**
         *  This functions builds the GROUP BY expression and returns it.
         *
         *  @param $title  (optional) If true, returns the GROUP BY string. Default: true.
         *
         *  @returns  The GROUP BY expression.
         */
        function getGroup( $title=true ) {

            if ( ! sizeof( $this->group ) ) {
                return '';
            }
            $group = array();
            foreach ( $this->group as $arr ) {
                
                $sql = $arr['expr'];
                if ( $arr['reserved'] ) {
                    $parts = explode( '.', $arr['expr'] );
                    foreach ( $parts as $k => $exp ) {
                        $parts[$k] = $this->reserved( $exp );
                    }
                    $sql = implode( '.', $parts );
                } 
                $sql .= $arr['desc'] ? ' DESC' : '';
                
                $group[] = $sql;
            }
            return ( $title ? ' GROUP BY ' : '' ) . implode( ', ', $group );

        }

        /**
         *  This functions builds the SELECT expression and returns it.
         *
         *  @returns  The SELECT expression without the SELECT string.
         */
        function getSelect() {

            if ( ! sizeof( $this->select ) ) {
                return '*';
            }

            $select = array();
            foreach ( $this->select as $arr ) {
                
                $sql = $arr['expr'];
                if ( $arr['reserved'] ) {
                    $parts = explode( '.', $arr['expr'] );
                    foreach ( $parts as $k => $exp ) {
                        $parts[$k] = $this->reserved( $exp );
                    }
                    $sql = implode( '.', $parts );
                }
                if ( $arr['alias'] ) {
                    $sql .= ' AS ' . $this->reserved( $arr['alias'] );
                }
                $select[] = $sql;
            }
            return trim( implode( ', ', $select ) );
        }

        /**
         *  This functions builds the columns and values part of the INSERT query.
         *
         *  @returns  The expression without the INSERT INTO "table_name" part.
         */
        function getInsert() {

            if ( ! sizeof( $this->values ) ) {
                trigger_error( 'No values were added for the INSERT query.', YD_ERROR );
            }

            $columns = array();
            $values = array();
            foreach ( $this->values as $key => $value ) {
                if ( $this->filter && substr( $key, 0, 1 ) == '_' ) {
                    continue;
                }
                $columns[] = $this->reserved( $key );
                $values[]  = $this->sqlString( $value );
            }
            return ' ( ' . implode( ', ', $columns ) . ' ) VALUES ( ' . implode( ', ', $values ) . ' )';
        }

        /**
         *  This functions builds the columns and values part of the UPDATE query.
         *
         *  @returns  The expression withou the UPDATE "table_name" part.
         */
        function getUpdate() {

            if ( ! sizeof( $this->values ) ) {
                trigger_error( 'No values were added for the UPDATE query.', YD_ERROR );
            }

            $update = array();
            foreach ( $this->values as $key => $value ) {
                if ( $this->filter && substr( $key, 0, 1 ) == '_' ) {
                    continue;
                }
                $update[] = $this->reserved( $key ) . " = " . $this->sqlString( $value );
            }
            return ' SET ' . implode( ', ', $update );
        }

        /**
         *  This functions builds the array of flags that are defined after the action.
         *  e.g. DISTINCT, SQL_CACHE, ALL, etc.
         *
         *  @returns  The expression.
         */
        function getOptions() {
            return sizeof( $this->options ) ? implode( ' ', $this->options ) . ' ' : '';
        }
        
        /**
         *  This functions returns the LIMIT.
         *
         *  @returns  The expression.
         */
        function getLimit( $title=true ) {
            if ( $this->limit < 0 ) {
                return '';
            }
            return ( $title ? ' LIMIT ' : '' ) . $this->limit;
        }
        
        /**
         *  This functions returns the OFFSET.
         *
         *  @returns  The expression.
         */
        function getOffset( $title=true ) {
            if ( $this->offset < 0 ) {
                return '';
            }
            return ( $title ? ' OFFSET ' : '' ) . $this->offset;
        }
        
        /**
         *  This functions builds and returns the query defined by the object.
         *
         *  @returns  The query.
         */
        function getQuery() {

            switch ( strtoupper( $this->action ) ) {

                case 'SELECT':
                    return $this->getSelectQuery();

                case 'INSERT':
                    return $this->getInsertQuery();

                case 'UPDATE':
                    return $this->getUpdateQuery();

                case 'DELETE':
                    return $this->getDeleteQuery();
            }

            trigger_error( 'Incorrect action for the query.', YD_ERROR );

        }

        /**
         *  This functions builds and returns the select query defined by the object.
         *
         *  @returns  The query.
         */
        function getSelectQuery() {
            
            return 'SELECT ' . $this->getOptions() . $this->getSelect()
                             . $this->getFrom() . $this->getWhere() . $this->getGroup()
                             . $this->getHaving() . $this->getOrder() . $this->getLimit()
                             . $this->getOffset();
            
        }

        /**
         *  This functions builds and returns the insert query defined by the object.
         *
         *  @returns  The query.
         */
        function getInsertQuery() {
            
            return 'INSERT ' . $this->getOptions() .
                   'INTO '   . $this->getFrom( false ) . $this->getInsert();
            
        }
        
        
        /**
         *  This functions builds and returns the update query defined by the object.
         *
         *  @returns  The query.
         */
        function getUpdateQuery() {
            
            return 'UPDATE ' . $this->getOptions() . $this->getFrom( false ) . $this->getUpdate()
                             . $this->getWhere();
            
        }

        /**
         *  This functions builds and returns the delete query defined by the object.
         *
         *  @returns  The query.
         */
        function getDeleteQuery() {
            
            return 'DELETE ' . $this->getOptions() . $this->getFrom( true ) . $this->getWhere();
        }
        

        /**
         *  This function resets all variables of the object.
         */
        function reset() {
            $this->resetAction();
            $this->resetOptions();
            $this->resetSelect();
            $this->resetFrom();
            $this->resetValues();
            $this->resetWhere();
            $this->resetGroup();
            $this->resetHaving();
            $this->resetOrder();
            $this->resetLimit();
            $this->resetOffset();
        }

        /**
         *  This function resets the action defined in the object.
         */
        function resetAction() {
            $this->action();
        }

        /**
         *  This function resets the options defined in the object.
         */
        function resetOptions() {
            $this->options = array();
        }

        /**
         *  This function resets the values defined in the object.
         */
        function resetValues() {
            $this->values = array();
        }

        /**
         *  This function resets the SELECT expressions defined in the object.
         */
        function resetSelect() {
            $this->select = array();
        }

        /**
         *  This function resets the tables defined in the object.
         */
        function resetFrom() {
            $this->tables = array();
            $this->join = array();
            $this->joinon = array();
        }

        /**
         *  This function resets the WHERE expressions defined in the object.
         */
        function resetWhere() {
            $this->where = array();
        }

        /**
         *  This function resets the GROUP BY expressions defined in the object.
         */
        function resetGroup() {
            $this->group = array();
        }

        /**
         *  This function resets the HAVING expressions defined in the object.
         */
        function resetHaving() {
            $this->having = array();
        }

        /**
         *  This function resets the ORDER BY expressions defined in the object.
         */
        function resetOrder() {
            $this->order = array();
        }
        
        /**
         *  This function resets the LIMIT.
         */
        function resetLimit() {
            $this->limit = -1;
        }
        
        /**
         *  This function resets the OFFSET.
         */
        function resetOffset() {
            $this->offset = -1;
        }

        /**
         *  This function builds the JOIN and WHERE conditions expressions.
         *
         *  @param $array  The array of expressions and logical operators.
         *
         *  @returns  The complete expression.
         *
         *  @internal
         */
        function _getConditions( $array ) {

            if ( ! sizeof( $array ) ) {
                return '';
            }

            $cond = array();
            $open = array();
            $exists = false;
            $start = true;

            foreach ( $array as $arr ) {

                $arr['expr']  = ! isset( $arr['expr'] )  ? '' : $arr['expr'];
                $arr['logic'] = ! isset( $arr['logic'] ) ? '' : $arr['logic'];
                
                if ( ! $arr['expr'] ) {
                    if ( ! $arr['logic'] && ! sizeof( $open ) ) {
                        continue;
                    }
                    if ( $arr['logic'] ) {
                        $sql = $start ? '( ' : $arr['logic'] . ' ( ';
                        $open[] = $sql;
                        $start = true;
                    } else {
                        $sql = ') ';
                        array_pop ( $open );
                        $start = false;
                    }
                } else {
                    $sql  = ( $start ? '' : $arr['logic'] . ' ' ) . $arr['expr'] . ' ';
                    $exists = true;
                    $start = false;
                }
                $cond[] = $sql;
            }
            if ( $exists ) {
                foreach ( $open as $op ) {
                    $cond[] = ') ';
                }
                return trim( implode( '', $cond ) );
            }
            return '';

        }
        
        /**
         *  This function will escape a string so that it's safe to include 
         *  it in an SQL statement.
         *
         *  @param $string  The string to escape.
         *
         *  @returns    The escaped string.
         */
        function string( $string ) {
            if ( is_string( $string ) ) {
                if ( strtolower( $string ) != 'null' ) {
                    return str_replace( "'", "''", $string );
                }
            } else if ( is_null( $string ) ) {
                return 'null';
            }
            return $string;
        }

        /**
         *  This function will escape a string so that it's safe to include it 
         *  in an SQL statement and will surround it with the quotes appropriate 
         *  for the database backend.
         *
         *  @param $string  The string to escape.
         *
         *  @returns    The escaped string surrounded by quotes.
         */
        function sqlString( $string ) {
            if ( is_string( $string ) ) {
                if ( strtolower( $string ) != 'null' ) {
                    return $this->quote . $this->string( $string ) . $this->quote;
                }
            } else if ( is_null( $string ) ) {
                return 'null';
            }
            return $string;
        }

        
    }
    
    /**
     *  This class defines a YDSqlQueryDriver_mysql object.
     */
    class YDDatabaseQueryDriver_mysql extends YDDatabaseQueryDriver {

        /**
         *  The class constructor can be used to set the action and optional options.
         */
        function YDDatabaseQueryDriver_mysql( & $db ) {
            
            $this->YDDatabaseQueryDriver( $db );
            
        }
        
        /**
         *  This function will check if the server supports this database type.
         *
         *  @returns    Boolean indicating if the database type is supported by the server.
         */
        function isSupported() {
            return extension_loaded( 'mysql' );
        }
        
        /**
         *  This function will escape a string so that it's safe to include it 
         *  in an SQL statement.
         *
         *  @param $string  The string to escape.
         *
         *  @returns    The escaped string.
         */
        function string( $string ) {
            
            $this->db->connect();
            
            if ( is_string( $string ) ) {
                if ( strtolower( $string ) != 'null' ) {
                    return mysql_real_escape_string( $string );
                }
            } else if ( is_null( $string ) ) {
                return 'null';
            }
            return $string;
        }
        
        /**
         *  This functions builds and returns the select query defined by the object.
         *
         *  @returns  The query.
         */
        function getSelectQuery() {
            
            $sql = 'SELECT ' . $this->getOptions() . $this->getSelect()
                             . $this->getFrom() . $this->getWhere() . $this->getGroup()
                             . $this->getHaving() . $this->getOrder();
                             
            if ( $this->limit < 0 && $this->offset < 0 ) {
                return $sql;
            }
            
            if ( $this->limit < 0 ) {
                return $sql . ' LIMIT ' . $this->offset . ',18446744073709551615';
            } else {
                if ( $this->offset < 0 ) {
                    return $sql . $this->getLimit();
                } else {
                    return $sql . $this->getLimit() . $this->getOffset();
                }
            }
            
        }
        
        /**
         *  This functions builds and returns the update query defined by the object.
         *
         *  @returns  The query.
         */
        function getUpdateQuery() {
            
            return 'UPDATE ' . $this->getOptions() . $this->getFrom( false ) . $this->getUpdate()
                             . $this->getWhere() . $this->getOrder() . $this->getLimit();
            
        }

        /**
         *  This functions builds and returns the delete query defined by the object.
         *
         *  @returns  The query.
         */
        function getDeleteQuery() {
            
            return 'DELETE ' . $this->getOptions() . $this->getFrom( true ) . $this->getWhere()
                             . $this->getOrder() . $this->getLimit();
        }
        
    }


?>