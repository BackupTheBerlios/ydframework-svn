<?php

	/*
	
		Yellow Duck Framework version 2.0
		Copyright (C) (c) copyright 2004 Pieter Claerhout
	
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
	
	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}
	
	/**
	 *  This class defines a YDSqlQuery object.
	 */	
	class YDSqlQuery extends YDBase {
	
		var $action;
		var $options = array();
		var $select  = array();
		var $tables  = array();
		var $join   = array();
		var $where   = array();
		var $group  = array();
		var $having  = array();
		var $order  = array();
		var $values  = array();
		var $limit   = -1;
		var $offset  = -1;
	
		/**
		 *	The class constructor can be used to set the action and optional options.
		 *
		 *	@param $action	 (optional) Name of the action. Should be one of these:
		 *                   SELECT, INSERT, UPDATE, DELETE. Default: SELECT.
		 *	@param $options	 (optional) Array of flags to be set after the action.
		 *					 e.g. DISTINCT, SQL_CACHE, ALL, etc.
		 */
		 function YDSqlQuery( $action='SELECT', $options=array() ) {
		 	$this->YDBase();
			$this->reset();
			$this->setAction( $action );			
			$this->setOptions( $options );
		}

		/**
		 *	This function sets the action for the query.
		 *
		 *	@param $action	 (optional) Name of the action. Should be one of these:
		 *                   SELECT, INSERT, UPDATE, DELETE. Default: SELECT.
		 */		
		function setAction( $action='SELECT' ) {
			$this->action = $action;
		}	
		/**
		 *	This function sets the options for the query.
		 *
		 *	@param $options	 Array of flags to be set after the action.
		 *					 e.g. DISTINCT, SQL_CACHE, ALL, etc.
		 */
		function setOptions( $options ) {
			$this->options = $options;
		}
			
		/**
		 *	Adds a table to the query.
		 *
		 *	@param $table  The name of the table.
		 *	@param $alias  (optional) The table alias.
		 *
		 *  @returns	   The table alias if defined or the table name.
		 */	
		function addTable( $table, $alias='' ) {

			$alias = ( $alias == $table ) ? '' : $alias;
			array_push( $this->tables, array( 'name' => $table, 'alias' => $alias ) ); 

			if ( strlen( $alias ) ) {			
				$defined = array();
				foreach ( $this->tables as $arr ) {
					if ( strlen( $arr['alias'] ) && in_array( $arr['alias'], $defined ) ) {
						trigger_error( 'Table alias "' . $arr['alias'] . '" already defined.', YD_ERROR );
					}
					array_push( $defined, $arr['alias'] );					
				}
			}
			return strlen( $alias ) ? $alias : $table;
		}

		/**
		 *	Adds a join table to the query. 
		 *
		 *	@param $type        Should be one of these: LEFT, LEFT OUTER, RIGHT, RIGHT OUTER, INNER, CROSS
		 *  @param $table       The joining table name.
		 *  @param $conditions  The conditions for the join (the ON part).
		 *  @param $alias       (optional) The joining table alias.
		 *
		 *  @returns   The alias for the joining table.
		 */			
		function addJoin( $type, $table, $conditions, $alias='' ) {

			if ( ! sizeof( $this->tables ) ) {
				trigger_error( 'No tables defined to join.', YD_ERROR );
			}
			
			// Get last table added
			$arr = end( $this->tables );
						
			$alias = ( $alias == $table ) ? '' : $alias;
			$this->join[ $arr['name'] ] = array( 'type' => $type, 
											     'table' => $table, 
											     'alias' => $alias,
											     'conditions' => $conditions );

			return $this->addTable( $table, $alias );
		}

				
		
		/**
		 *	Adds a select expression. 
		 *
		 *	@param $expr   The expression.
		 *  @param $alias  (optional) The alias for the expression.
		 */				
		function addSelect( $expr, $alias='' ) {
			if ( strlen( $expr ) ) {
				$alias = ( $alias == $expr ) ? '' : $alias;
				array_push( $this->select, array( 'expr' => $expr, 
												  'alias' => $alias ) );
			}
		}

		/**
		 *	Adds a column to the ORDER BY clause.
		 *
		 *	@param $expr  Column name.
		 *  @param $desc  (optional) If true adds a DESC string to the column. Default: false.
		 */		
		function addOrder( $expr, $desc=false ) {
			if ( strlen( $expr ) ) {
				array_push( $this->order, array( 'expr' => $expr, 'desc' => $desc ) );
			}
		}
		
		/**
		 *	Adds an expression to the GROUP BY clause.
		 *
		 *	@param $expr  The expression.
		 *  @param $desc  (optional) If true adds a DESC string to the expression. Default: false.
		 */		
		function addGroup( $expr, $desc=false ) {
			if ( strlen( $expr ) ) {
				array_push( $this->group, array( 'expr' => $expr, 'desc' => $desc ) );
			}
		}
	
		/**
		 *	Adds an expression to the HAVING clause.
		 *
		 *	@param $expr   The expression.
		 *  @param $logic  (optional) Logic operator (e.g. AND, OR, XOR). Default: AND.
		 */		
		function addHaving( $expr, $logic='AND' ) {
			if ( strlen( $expr ) ) {
				array_push( $this->having, array( 'expr' => $expr, 'logic' => strtoupper( $logic ) ) );
			}
		}
		
		/**
		 *	Opens a group of conditions.
		 *
		 *  @param $logic  (optional) Logic operator (e.g. AND, OR, XOR). Default: AND.
		 */		
		function openWhereGroup( $logic='AND' ) {
			array_push( $this->where, array( 'logic' => strtoupper( $logic ) ) );
		}
		
		/**
		 *	Adds a condition to the WHERE clause.
		 *
		 *  @param $expr   The condition expression.
		 *  @param $logic  (optional) Logic operator (e.g. AND, OR, XOR). Default: AND.
		 */			
		function addWhere( $expr, $logic='AND' ) {
			if ( strlen( $expr ) ) {
				array_push( $this->where, array( 'expr' => $expr, 'logic' => strtoupper( $logic ) ) );
			}
		}

		/**
		 *	Closes a group of conditions. This method is optional as the class
		 *  will automatically close all groups that weren't closed.
		 */			
		function closeWhereGroup() {
			array_push( $this->where, array() );
		}		

		/**
		 *	Sets the limit and offset for the query.
		 *
		 *  @param $limit   The limit.
		 *  @param $offset  (optional) The offset. Default: -1.
		 */		
		function setLimit( $limit, $offset=-1 ) {
			$this->limit = $limit;
			$this->offset = $offset;
		}

		/**
		 *	Adds a column and it's value to be used in a 
		 *  UPDATE or INSERT query.
		 *
		 *  @param $column   The column name.
		 *  @param $value    The column value. If null, the value will
		 *                   be converted to a string "null" without the quotes.
		 */				
		function addValue( $column, $value ) {
			$this->values[ $column ] = $value;
		}

		/**
		 *	Sets the values to be used in a UPDATE or INSERT query.
		 *
		 *  @param $values   The associative array with the column names
		 *                   as keys and it's values.
		 */		
		function setValues( $values ) {
			$this->values = $values;
		}

		/**
		 *	This function builds the tables references expression and returns it.
		 *
		 *  @param $title       (optional) If true, returns the FROM string. Default: true.
		 *
		 *  @returns  The tables references expression.
		 */						
		function getFrom( $title=true ) {
		
			$from = array();
			$done = array();
			
			foreach ( $this->tables as $arr ) {	
			
				$table = $arr['name'];
				$alias = $arr['alias'];
				$sql = '';
				
				if ( ! in_array( $table, $done ) ) {
					$sql  = sizeof( $from ) ? ', ' : '';
					$sql .= '`' . $table . '`';
					$sql .= strlen( $alias ) ? ' AS "' . $alias . '"' : '';
					array_push( $done, $table );
				}
				
				if ( array_key_exists( $table, $this->join ) ) {
					$join = $this->join[ $table ];					
					$sql .= ' ' . $join['type'] . ' JOIN `' . $join['table'] . '`';
					$sql .= strlen( $join['alias'] ) ? ' AS "' . $join['alias'] . '"' : '';
					$sql .= ' ON ( ' . $join['conditions'] . ' ) ';
					array_push( $done, $join[ 'table' ] );
				}
				
				array_push( $from, $sql );
			}
			
			return ( $title ? ' FROM ' : '' ) . trim( implode( '', $from ) );
			
		}

		/**
		 *	This function builds the conditions expression and returns it.
		 *
		 *  @param $title  (optional) If true, returns the WHERE string. Default: true.
		 *
		 *  @returns  The conditions expression.
		 */			
		function getWhere( $title=true ) {			
		
			if ( ! sizeof( $this->where ) ) {
				return '';
			}

			$where = array();
			$open = array();
			$exists = false;
			$start = true;
			
			foreach ( $this->where as $arr ) {
			
				$arr['expr']  = ! isset( $arr['expr'] )  ? '' : $arr['expr'];
				$arr['logic'] = ! isset( $arr['logic'] ) ? '' : $arr['logic'];
				
				if ( ! $arr['expr'] ) {
					if ( ! $arr['logic'] && ! sizeof( $open ) ) {
						continue;
					}
					if ( $arr['logic'] ) {
						$sql = $start ? '( ' : $arr['logic'] . ' ( ';
						array_push( $open, $sql );						
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
				array_push( $where, $sql );
			}
			if ( $exists ) {
				foreach ( $open as $op ) {
					array_push( $where, ') ');
				}
				return ( $title ? ' WHERE ' : '' ) . trim( implode( '', $where ) );
			}
			return '';
		}

		/**
		 *	This functions builds the HAVING expression and returns it.
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
				array_push( $having, ( sizeof( $having ) ? $arr['logic'] . ' ' : '' ) . $arr['expr'] );			
			}
			return ( $title ? ' HAVING ' : '' ) . implode( ' ', $having );	
		}

		/**
		 *	This functions builds the ORDER BY expression and returns it.
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
				array_push( $order, $arr['expr'] . ( $arr['desc'] ? ' DESC' : '' ) );			
			}
			return ( $title ? ' ORDER BY ' : '' ) . implode( ', ', $order );
			
		}

		/**
		 *	This functions builds the GROUP BY expression and returns it.
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
				array_push( $group, $arr['expr'] . ( $arr['desc'] ? ' DESC' : '' ) );			
			}
			return ( $title ? ' GROUP BY ' : '' ) . implode( ', ', $group );
			
		}		

		/**
		 *	This functions builds the LIMIT expression and returns it.
		 *
		 *  @param $title  (optional) If true, returns the LIMIT string. Default: true.
		 *
		 *  @returns  The LIMIT expression.
		 */				
		function getLimit( $title=true ) {
			if ( $this->limit > -1 ) {
				$sql = ( $this->offset >= 0 ) ? $this->offset . ',' . $this->limit : $this->limit;
				return ( $title ? ' LIMIT ' : '' ) . $sql;
			}
			return '';
		}
		
		/**
		 *	This functions builds the SELECT expression and returns it.
		 *
		 *  @returns  The SELECT expression without the SELECT string.
		 */			
		function getSelect() {
		
			if ( ! sizeof( $this->select ) ) {
				return '*';
			}
			
			$select = array();
			foreach ( $this->select as $arr ) {
				$sql = $arr['expr'] . ( $arr['alias'] ? ' AS "' . $arr['alias'] . '"' : '' );
				array_push( $select, $sql );
			}
			return trim( implode( ', ', $select ) );
		}

		/**
		 *	This functions builds the columns and values part of the INSERT query.
		 *
		 *  @returns  The expression without the INSERT INTO "table_name" part.
		 */				
		function getInsert() {
	
			if ( ! sizeof( $this->values ) ) {
				trigger_error( 'No values were added for the INSERT statement.', YD_ERROR );
			}	
			
			$columns = array();
			$values = array();
			foreach ( $this->values as $key => $value ) {
				if ( substr( $key, 0, 1 ) != '_' ) {
					array_push( $columns, $key );
					if ( is_null( $value ) ) {
						array_push( $values, 'NULL' );
					} else if ( is_numeric( $value ) ) {
						array_push( $values, $value );
					} else {
						array_push( $values, "'" . $value . "'" );					
					}
				}
			}			
			return ' ( ' . implode( ', ', $columns ) . ' ) VALUES ( ' . implode( ', ', $values ) . ' )';
		}

		/**
		 *	This functions builds the columns and values part of the UPDATE query.
		 *
		 *  @returns  The expression withou the UPDATE "table_name" part.
		 */						
		function getUpdate() {
	
			if ( ! sizeof( $this->values ) ) {
				trigger_error( 'No values were added for the UPDATE statement.', YD_ERROR );
			}	
		
			$update = array();
			foreach ( $this->values as $key => $value ) {
				if ( substr( $key, 0, 1 ) != '_' ) {
					if ( is_null( $value ) ) {
						array_push( $update, $key . " = " . 'NULL' );
					} else if ( is_numeric( $value ) ) {
						array_push( $update, $key . " = " . $value );
					} else {
						array_push( $update, $key . " = " . "'" . $value . "'" );
					}
				}
			}		
			return ' SET ' . implode( ', ', $update );	
		}

		/**
		 *	This functions builds the array of flags that are defined after the action.
		 *	e.g. DISTINCT, SQL_CACHE, ALL, etc.
		 *
		 *  @returns  The expression.
		 */				
		function getOptions() {
			return sizeof( $this->options ) ? implode( ' ', $this->options ) . ' ' : '';
		}

		/**
		 *	This functions builds and returns the query defined by the object.
		 *
		 *  @returns  The expression.
		 */				
		function getSql( $break=false ) {
		
			switch ( strtoupper( $this->action ) ) {
			
				case 'SELECT':
					$sql =  'SELECT ' . $this->getOptions() . $this->getSelect()
									  . $this->getFrom() . $this->getWhere() . $this->getGroup()
							          . $this->getHaving() . $this->getOrder() . $this->getLimit();
					break;

				case 'INSERT':
					$sql =  'INSERT ' . $this->getOptions() .
							'INTO '   . $this->getFrom( false ) . $this->getInsert();
					break;

				case 'UPDATE':
					$sql =  'UPDATE ' . $this->getOptions() . $this->getFrom( false ) . $this->getUpdate()
									  . $this->getWhere() . $this->getOrder() . $this->getLimit();
					break;

				case 'DELETE':
					$sql =  'DELETE ' . $this->getOptions() . trim( $this->getFrom( true ) ) . $this->getWhere() 
									  . $this->getOrder() . $this->getLimit();	
					break;
			}
			
			if ( ! $sql ) {
				trigger_error( 'Action not defined correctly.', YD_ERROR );
				return;
			}
			
			if ( $break ) {
				return $this->getBrokenSql( $sql );
			}
			return $sql;
		
		}
		
		function getBrokenSql( $sql='' ) {
		
			if ( ! strlen( $sql ) ) {
				$sql = $this->getSql();
			}
			
			$arr = explode( ' ', $sql );
			$sql = '';
			
			foreach ( $arr as $word ) {
				switch ( strtoupper( $word ) ) {
					case 'FROM':
					case 'VALUES':
					case 'SET':
					case 'WHERE':
					case 'GROUP':
					case 'HAVING':
					case 'ORDER':
					case 'LIMIT':
						$break = true;
						break;
					default: 
						$break = false;
				}
				
				if ( $break ) {
					$sql .= YD_CRLF;
				}
				$sql .= $word . " ";
				
			}
			
			return trim( $sql );
			
		}
		
		/**
		 *	This function resets all variables of the object.
		 */		
		function reset() {
			$this->resetAction();
			$this->resetOptions();
			$this->resetSelect();
			$this->resetTables();
			$this->resetValues();
			$this->resetWhere();
			$this->resetGroup();
			$this->resetHaving();
			$this->resetOrder();
			$this->resetLimit();
		}

		/**
		 *	This function resets the action defined in the object.
		 */		
		function resetAction() {
			$this->action = null;
		}

		/**
		 *	This function resets the options defined in the object.
		 */				
		function resetOptions() {
			$this->options = array();
		}
		
		/**
		 *	This function resets the values defined in the object.
		 */		
		function resetValues() {
			$this->values = array();
		}

		/**
		 *	This function resets the SELECT expressions defined in the object.
		 */		
		function resetSelect() {
			$this->select = array();
		}
		
		/**
		 *	This function resets the tables defined in the object.
		 */		
		function resetTables() {
			$this->tables = array();
			$this->join = array();
		}
		
		/**
		 *	This function resets the WHERE expressions defined in the object.
		 */					
		function resetWhere() {
			$this->where = array();
		}

		/**
		 *	This function resets the GROUP BY expressions defined in the object.
		 */				
		function resetGroup() {
			$this->group = array();
		}
		
		/**
		 *	This function resets the HAVING expressions defined in the object.
		 */			
		function resetHaving() {
			$this->having = array();
		}
		
		/**
		 *	This function resets the ORDER BY expressions defined in the object.
		 */			
		function resetOrder() {
			$this->order = array();
		}

		/**
		 *	This function resets the LIMIT defined in the object.
		 */					
		function resetLimit() {
			$this->limit   = -1;
			$this->offset  = -1;
		}		
	
	}

?>