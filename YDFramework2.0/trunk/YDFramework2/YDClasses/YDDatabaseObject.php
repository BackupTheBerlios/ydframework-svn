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
	 *  Fields constants.
	 */	
	define( 'YD_DATABASEOBJECT_SQL',   1 );
	define( 'YD_DATABASEOBJECT_NUM',   2 );
	define( 'YD_DATABASEOBJECT_STR',   3 );

	/**
	 *  Relations constants.
	 */	
	define( 'YD_DATABASEOBJECT_ONETOONE',   1 );
	define( 'YD_DATABASEOBJECT_ONETOMANY',  2 );
	define( 'YD_DATABASEOBJECT_MANYTOMANY', 3 );

	/**
	 *  YDDatabaseObject class name prefix.
	 *  Default: (empty)
	 */	
	YDConfig::set( 'YD_DATABASEOBJECT_CLASSPREFIX', '', false );

	/**
	 *  Path to all YDDatabaseObjects.
	 *  Default: YD_SELF_DIR
	 */		
	YDConfig::set( 'YD_DATABASEOBJECT_PATH', YD_SELF_DIR, false ); 

	/**
	 *  YDDatabaseObjects file extension.
	 *  Default: YD_SCR_EXT
	 */		
	YDConfig::set( 'YD_DATABASEOBJECT_EXT', YD_SCR_EXT, false ); 

	/**
	 *  This constant defines if a DELETE query with no conditions can be executed.
	 *  Default: false.
	 */	
	YDConfig::set( 'YD_DATABASEOBJECT_DELETE', false, false ); 
	
	/**
	 *  This constant defines if a UPDATE query with no conditions can be executed.
	 *  Default: false.
	 */		
	YDConfig::set( 'YD_DATABASEOBJECT_UPDATE', false, false ); 
	
	YDInclude('YDDatabase.php');
	YDInclude('YDSqlQuery.php');

	/**
	 *  This class defines a YDDatabaseObject object.
	 */		
	class YDDatabaseObject extends YDBase {
	
		var $__db 			= null;
		var $__table		= null;
		var $__sql          = null;
		var $__last         = array();
		var $__fields       = array();
		var $__keys         = array();
		var $__relations    = array();
		var $__protected    = array();
		var $__results      = array();
		var $__limit        = -1;
		var $__offset       = -1;
		var $__count        = 0;

		/**
		 *  The class constructor loads all the configuration defined and validates it.
		 */	
		function YDDatabaseObject() {
		
			$this->YDBase(); 

			// Load the YDDatabaseObject configuration
			$this->setDatabase();
			$this->setTable();
			$this->setFields();
			$this->setKeys();
			$this->setRelations();
		
			// Check if the table is set
			if ( ! strlen( $this->getTable() ) ) {
				trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
								The table name is not set.', YD_ERROR );
			}	
			
			// Check if the object has at least one field defined
			if ( ! sizeof( $this->__fields ) ) {
				trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
								The fields are not defined.', YD_ERROR );
			}
			
			// Check field/alias and columns names
			$columns = array();
			foreach ( $this->__fields as $field => $specs ) {				
				$column = $this->_getFieldSpec( $field, 'column' );
				if ( in_array( $column, $columns ) ) {
					trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
									Field/Column "' . $column . '" is repeated.', YD_ERROR );
				}
				$columns[] = $field;
				$columns[] = $column;
			}
			
			// Change all field names to lower-case
			$fields = array();
			foreach ( $this->__fields as $field => $specs ) {
				$fields[ strtolower( $field ) ] = $specs;
			}
			$this->__fields = $fields;
			
			// Check if the keys are NULL
			foreach ( $this->__keys as $field ) {
				if ( $this->_getFieldSpec( $field, 'null' ) ) {
					trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
									The PRIMARY KEY "' . $field . '" can not be NULL.', YD_ERROR );
				}
			}
			
			$this->__sql = new YDSqlQuery();			
			$this->resetQuery();
			
		}

		/**
		 *  This function sets the YDDatabase instance.
		 */
		function setDatabase() {
			trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
							You need to override the setDatabase method.', YD_ERROR );
			// $this->__db = YDDatabase::getInstance( $type, $name, $user, $pass, $host );
		}

		/**
		 *  This function sets the table name.
		 */
		function setTable() {
			trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
							You need to override the setTable method.', YD_ERROR );
			// $this->__table = '';
		}
		
		/**
		 *  This function defines the fields of the YDDatabaseObject.
		 *  The keys of the associative array $__fields should be the alias
		 *  of the column or the column name itself.
		 *
		 *  Array of specifications: 
		 *
		 *  type     - YD_DATABASEOBJECT_NUM || YD_DATABASEOBJECT_STR || YD_DATABASEOBJECT_SQL (special type)
		 *  null     - true/false (optional) - default: false
		 *  auto     - auto-increment field - true/false (optional) - default: false
		 *  default  - value (optional)
		 *  column   - real column name or SQL expression if type = YD_DATABASEOBJECT_SQL (optional)
		 *  callback - callback when setting the field value (optional)
		 */
		function setFields() {			
			trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
							You need to override the setFields method.', YD_ERROR );
			// $this->__fields[ $field ] = $specifications;
		}

		/**
		 *  This function sets the PRIMARY KEYS of the table.
		 *  A simple array with the fields names (as defined in $__fields).
		 */
		function setKeys() {
			$this->__keys = array();
		}
		
		/**
		 *  This function defines the relations with other YDDatabaseObjects.
		 *  The keys of the associative array $__relations are the relation unique id.
		 *
		 *  Specifications:
		 *
		 *  type - YD_DATABASEOBJECT_ONETOONE || YD_DATABASEOBJECT_ONETOMANY || YD_DATABASEOBJECT_MANYTOMANY
		 *  local_key - column name (optional if it's the only key)
		 *  foreign_dataobject - databaseobject name
		 *  foreign_type_join - INNER | LEFT | RIGHT (optional - Default INNER)
		 *  foreign_key - column name (optional if it's the only key)
		 *  foreign_var - variable name (optional - default relation id)
		 *  foreign_conditions - conditions in foreign table (optional)
		 *
		 *  For YD_DATABASEOBJECT_MANYTOMANY relations:
		 *
		 *  join_dataobject - databaseobject name 
		 *  join_type_join - INNER | LEFT | RIGHT (optional - Default INNER)
		 *  join_local_key - column name (optional if it's the only key)
		 *  join_foreign_key - column name (optional if it's the only key)
		 *  join_var - variable name (optional - "join_" plus the default relation id)
		 *  join_conditions - conditions in join table (optional)
		 *
		 *  select - expression (optional)
		 *  where - expression (optional)
		 *  group - expression (optional)
		 *  having - expression (optional)
		 *  order - expression (optional)
		 */
		function setRelations() {
			$this->__relations = array();
		}
		
		/**
		 *  This function sets the default value for every field, if defined.
		 */
		function setDefaults() {
			foreach ( $this->__fields as $field => $spec ) {
				if ( $value = $this->_getFieldSpec( $field, 'default' ) ) {
					$this->set( $field, $value );
				}
			}
		}
		
		/**
		 *  This function returns the table name defined.
		 *
		 *  @returns  The table name.
		 */
		function getTable() {
			return $this->__table;
		}

		/**
		 *  This function registers a protected field.
		 *
		 *  @param $field     The registered field name.
		 *  @param $value     The protected value.
		 */		
		function registerProtected( $field, $value ) {		
			if ( ! array_key_exists( $field, $this->__fields ) ) {
				trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
								The field "' . $field . '" doesn\'t exist.', YD_ERROR );
			}		
			$this->__protected[ $field ] = $value;		
		}

		/**
		 *  This function unregisters a protected field.
		 *
		 *  @param $field     The registered field name.
		 */				
		function unregisterProtected( $field ) {
			unset( $this->__protected[ $field ] );
		}
		
		/**
		 *  This function registers a SELECT expression.
		 *
		 *  @param $alias     The alias for the expression.
		 *  @param $expr      The SQL expression.
		 *  @param $callback  (optional) The callback method when this field is set.
		 */		
		function registerSelect( $alias, $expr, $callback='' ) {
			if ( array_key_exists( $alias, $this->__fields ) ) {
				trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
								The alias "' . $alias . '" is already defined.', YD_ERROR );
			}
			$this->__fields[ $alias ] = array( 'type' => YD_DATABASEOBJECT_SQL, 
											   'column' => $expr, 
											   'callback' => $callback );
		}

		/**
		 *  This function unregisters a SELECT expression.
		 *
		 *  @param $alias     The alias name.
		 */		
		function unregisterSelect( $alias ) {
			if ( ! array_key_exists( $alias, $this->__fields ) ) {
				trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
								The alias "' . $alias . '" doesn\'t exist.', YD_ERROR );
			}
			if ( $this->_getFieldSpec( $alias, 'type' ) != YD_DATABASEOBJECT_SQL ) {
				trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
								The field "' . $alias . '" can not be unregistered.', YD_ERROR );
			}
			unset( $this->__fields[ $alias ] );
		}

		/**
		 *  This function returns the value of a field.
		 *
		 *  @param $field  The field name
		 *
		 *  @returns The field value.
		 */				
		function get( $field ) {				
			$this->_resetProtected();
			if ( empty( $field ) ) {
				trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
								No field name passed in get method.', YD_ERROR );
			}
			if ( isset( $this->$field ) ) {
				return $this->$field;
			}
			return; // return null? we can have NULL values...
		}

		/**
		 *  This function sets the value of a field.
		 *
		 *  @param $field		The field name
		 *  @param $value		The field value
		 *	@param $protected	(optional) Sets protected fields. Default: false.
		 */				
		function set( $field, $value, $protected=false ) {				
			if ( empty( $field ) ) {
				trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
								No field name passed in set method.', YD_ERROR );
			}		
			if ( ! $protected && array_key_exists( $field, $this->__protected ) ) {
				return;
			}
			$type = $this->_getFieldSpec( $field, 'type' );
			if ( is_null( $value ) && ! $this->_getFieldSpec( $field, 'null' ) ) {
				unset( $this->$field );
				return;
			} 
			$this->$field = $value;
			
			if ( $callback = $this->_getFieldSpec( $field, 'callback' ) ) {
				call_user_func( array( & $this, $callback ), $value );
			}
		}

		/**
		 *  This function receives and adds an associative array of values to
		 *  the object. The array keys are the objects fields.
		 *
		 *  @param $values  The associative array of values.
		 */					
		function setValues( $values=array() ) {
			
			$current = $this->getValues();
			
			foreach ( $current as $field => $value ) {
				if ( ! array_key_exists( $field, $values ) ) {					
					unset( $this->$field );
				}
			}
			foreach( $values as $field => $value ) {
				$this->set( $field, $value );
			}
			$this->_resetProtected();
		}

		/**
		 *  This function retrieves all the object's fields values.
		 *
		 *  @param $only_fields  Returns only defined fields. Default: false.
		 *  @param $prefix       (optional) Adds a prefix to all field names. Default: (empty)
		 *
		 *  @returns  An associative array with the values.
		 */					
		function getValues( $only_fields=false, $prefix='' ) {
				
			$this->_resetProtected();
			$values = get_object_vars( $this );
			
			$new = array();
			foreach ( $values as $field => $value ) {
				$valid = false;
				if ( ! is_object( $value ) && substr( $field, 0, 1 ) != '_' ) {
					if( ! $only_fields || ( $only_fields && array_key_exists( $field, $this->__fields ) && 
							   $this->_getFieldSpec( $field, 'type' ) != YD_DATABASEOBJECT_SQL ) ) {
						$new[ $prefix . $field ] = $value;
					}					
				}
			}
			
			return $new;	
		}		

		/**
		 *  This function loads a relation object into the YDDatabaseObject.
		 *
		 *  @param $relation  The relation id.
		 */				
		function loadRelation( $relation ) {
			
			$relation = $this->_checkRelation( $relation, false );

			$path   = YDConfig::get( 'YD_DATABASEOBJECT_PATH' ) . YD_DIRDELIM;
			$ext    = YDConfig::get( 'YD_DATABASEOBJECT_EXT' );
			$prefix = YDConfig::get( 'YD_DATABASEOBJECT_CLASSPREFIX' );
			
			$f_var = $this->_getRelationSpec( $relation, 'foreign_var' );	
			$f_dao = $this->_getRelationSpec( $relation, 'foreign_dataobject' );
			
			require_once $path . $f_dao . $ext;
			
			if ( ! isset( $this->$f_var ) ) {
				$f_dao = $prefix . $f_dao;
				$this->$f_var = new $f_dao;
			}			

			if ( $this->_getRelationSpec( $relation, 'type' ) == YD_DATABASEOBJECT_MANYTOMANY ) {
	
				$j_var = $this->_getRelationSpec( $relation, 'join_var' );
				$j_dao = $this->_getRelationSpec( $relation, 'join_dataobject' );
				
				require_once $path . $j_dao . $ext;
				
				if ( ! isset( $this->$j_var ) ) {
					$j_dao = $prefix . $j_dao;
					$this->$j_var = new $j_dao;
				}
				
			} 
			
			$this->__last = array( $relation );
			
		}

		/**
		 *  This function loads all relations objects into the YDDatabaseObject.
		 */
		function loadAllRelations() {			
			foreach ( $this->__relations as $relation => $specs ) {
				$this->loadRelation( $relation );
			}
			$this->__last = array_keys( $this->__relations );
		}

		/**
		 *  This function loads and finds the records for relations defined.
		 *
		 *  @returns  The total rows retrieved.
		 */				
		function findRelation() {
		
			$args = func_get_args();
			
			$relations = $this->__last;
			if ( sizeof( $args ) ) {
				$relations = $args;
			}
			
			if ( ! sizeof( $relations ) ) {
				return $this->findAllRelations();
			}
			
			$this->__sql->setAction( 'SELECT' );
			$this->__sql->resetFrom();
			
			$last = 0;
			$prefixes = array();
			
			foreach ( $relations as $k => $relation ) {
				
				$relation = $this->_checkRelation( $relation );
				$relations[ $k ] = $relation;
				
				$l_table = $this->getTable();
				
				// Local table
				if ( $last == 0 ) {

					$this->_prepareQuery( $this, false, substr( '0' . ( ++$last ) . '_', -3 ) );
					$this->__sql->addTable( $l_table );
					$prefixes[] = '';
					
					YDDebugUtil::debug( 'YDDatabaseObject ' . $this->getClassName() . YDDebugUtil::r_dump( $this->getValues() ) );
				}
				

				$foreign_var = $this->_getRelationSpec( $relation, 'foreign_var' );	
				$f_table = $this->_prepareQuery( $this->$foreign_var, false, substr( '0' . ( ++$last ) . '_', -3 ) );
				$prefixes[] = $foreign_var;

				YDDebugUtil::debug( 'YDDatabaseObject ' . $this->$foreign_var->getClassName() . 
									 YDDebugUtil::r_dump( $this->$foreign_var->getValues() ) );
				
				// Joining table
				if ( $this->_getRelationSpec( $relation, 'type' ) == YD_DATABASEOBJECT_MANYTOMANY ) {					
					
					$join_var = $this->_getRelationSpec( $relation, 'join_var' );
					$j_table = $this->_prepareQuery( $this->$join_var, false, substr( '0' . ( ++$last ) . '_', -3 ) );
					$prefixes[] = $join_var;					
	
					YDDebugUtil::debug( 'YDDatabaseObject ' . $this->$join_var->getClassName() . 
										 YDDebugUtil::r_dump( $this->$join_var->getValues() ) );
	
					$this->__sql->addJoin( $this->_getRelationSpec( $relation, 'join_type_join' ), $j_table );
					$this->__sql->addJoinOn( $l_table . '.' . $this->_getRelationSpec( $relation, 'local_key' ) . ' = ' . 
											 $j_table . '.' . $this->_getRelationSpec( $relation, 'join_local_key' ) );
	
					$this->__sql->select = array_merge( $this->__sql->select, $this->$join_var->__sql->select );
					if ( $where = $this->$join_var->__sql->getWhere( false ) ) {
						$this->__sql->addJoinOn( $where );
					}
					
					// Foreign table
					$this->__sql->addJoin( $this->_getRelationSpec( $relation, 'foreign_type_join' ), $f_table );
					$this->__sql->addJoinOn( $j_table . '.' . $this->_getRelationSpec( $relation, 'join_foreign_key' ) . ' = ' . 
											 $f_table . '.' . $this->_getRelationSpec( $relation, 'foreign_key' ) );	
					
				} else {
				
					// Foreign table
					$this->__sql->addJoin( $this->_getRelationSpec( $relation, 'foreign_type_join' ), $f_table );
					$this->__sql->addJoinOn( $l_table . '.' . $this->_getRelationSpec( $relation, 'local_key' ) . ' = ' . 
											 $f_table . '.' . $this->_getRelationSpec( $relation, 'foreign_key' ) );
											 
				}			
	
				$this->__sql->select = array_merge( $this->__sql->select, $this->$foreign_var->__sql->select );
				if ( $where = $this->$foreign_var->__sql->getWhere( false ) ) {
					$this->__sql->addJoinOn( $where );
				}
				
				$this->_loadOptionalExpressions( $relation, 'order');
				$this->_loadOptionalExpressions( $relation, 'select');
				$this->_loadOptionalExpressions( $relation, 'group');
				$this->_loadOptionalExpressions( $relation, 'where');
				$this->_loadOptionalExpressions( $relation, 'having');
				
			}

			$this->__last = $relations;
			
			return $this->findSql( $this->__sql->getSql(), $this->__limit, $this->__offset, $prefixes );
		
		}
		
		/**
		 *  This function executes a single query for all relations that meet the conditions
		 *  established by the object values.
		 *
		 *  @returns  The number of rows found.
		 */		
		function findAllRelations() {
			$this->__last = array_keys( $this->__relations );			
			return $this->findRelation();
		}
		
		/**
		 *  This function returns all the relation objects getValues arrays in
		 *  a single array.
		 *
		 *  @param $prefix    (optional) If true all foreign values will be prefixed with the foreign_var.
		 *					             Default: true.
		 *  @param $relation  (optional) The relation id. If empty, the last relation id loaded.
		 *
		 *  @returns  The single array with all values.
		 */		
		function getRelationValues( $prefix=true, $relation='' ) {
			
			$relations = $this->__last;
						
			if ( strlen( $relation ) ) {
				$relations = array( $relation );
			}
			
			$result = $this->getValues();
			
			foreach ( $relations as $relation ) {
			
				$relation = $this->_checkRelation( $relation );
				
				$foreign_var = $this->_getRelationSpec( $relation, 'foreign_var' );				
				$values = $this->$foreign_var->getValues( false, $prefix ? $foreign_var . '_' : '' );
				
				$result = array_merge( $result, $values );
							
				if ( $this->_getRelationSpec( $relation, 'type' ) == YD_DATABASEOBJECT_MANYTOMANY ) {
					
					$join_var = $this->_getRelationSpec( $relation, 'join_var' );
					$values = $this->$join_var->getValues( false, $prefix ? $join_var . '_' : '' );
					
					$result = array_merge( $result, $values );
				}
				
			}
			
			return $result;
			
		}

		/**
		 *  This function resets the information defined in all the relation objects.
		 *
		 *  @param $relation  (optional) The relation id. If empty, the last relation id loaded.
		 */				
		function resetRelation( $relation='' ) {
			
			$relations = $this->__last;
						
			if ( strlen( $relation ) ) {
				$relations = array( $relation );
			}
			
			foreach ( $relations as $relation ) {
			
				$relation = $this->_checkRelation( $relation );
				
				$foreign_var = $this->_getRelationSpec( $relation, 'foreign_var' );				
				$this->$foreign_var->reset();
							
				if ( $this->_getRelationSpec( $relation, 'type' ) == YD_DATABASEOBJECT_MANYTOMANY ) {
					$join_var = $this->_getRelationSpec( $relation, 'join_var' );
					$this->$join_var->reset();
				}
			}

			$this->reset();
			
		}
		
		/**
		 *  This function fetch the results of all the objects of the relation.
		 *
		 *  @param $relation  The relation id.
		 *
		 *  @returns  True if we still have records, otherwise false.
		 */				
		function fetchRelation( $relation='' ) {				
		
			$relations = $this->__last;
						
			if ( strlen( $relation ) ) {
				$relations = array( $relation );
			}
			
			foreach ( $relations as $relation ) {
		
				$relation = $this->_checkRelation( $relation );
				
				$foreign_var = $this->_getRelationSpec( $relation, 'foreign_var' );				
				$this->$foreign_var->fetch();

				if ( $this->_getRelationSpec( $relation, 'type' ) == YD_DATABASEOBJECT_MANYTOMANY ) {
					$join_var = $this->_getRelationSpec( $relation, 'join_var' );
					$this->$join_var->fetch();
				}

			}
			
			return $this->fetch();

		} 

		/**
		 *  This function finds the rows that match the object field's values
		 *  an any other condition added with addWhere, addGroup, addHaving, etc.
		 *  
		 *  You can add values to the method's parameters to search the object
		 *  keys. The parameters should set in the same order of the $__keys array.
		 *
		 *  If you want to limit this query, use the setLimit method.
		 *  
		 *  @returns  The number of records found.
		 */	
		function find() {
		
			$args = func_get_args();
			
			$this->__sql->setAction( 'SELECT' );
			$this->__sql->resetFrom();
			$this->__sql->addTable( $this->getTable() );
			
			// Searching with keys
			if ( sizeof( $args ) && sizeof( $this->__keys ) ) {

				// Set the keys values
				for ( $i=0; $i < count( $args ); $i++ ) {
					if ( array_key_exists( $i, $this->__keys ) ) {
						$this->set( $this->__keys[ $i ], $args[ $i ] );
					}
				}
				
				$this->__sql->resetWhere();
				$this->_prepareQuery( $this, true );
				
			} else {
				$this->_prepareQuery( $this );
			}
			
			YDDebugUtil::debug( 'YDDatabaseObject ' . $this->getClassName() . YDDebugUtil::r_dump( $this->getValues() ) );

			return $this->findSql( $this->__sql->getSql(), $this->__limit, $this->__offset );
			
		}

		/**
		 *  This function execute the SQL statement passed and adds it's
		 *  results to the object.
		 *
		 *  @param $sql     The SQL statement.
		 *  @param $limit   (optional) How many records to return.
		 *  @param $offset  (optional) Where to start from.
		 *  
		 *  @returns  The number of records found.
		 */	
		function findSql( $sql, $limit=-1, $offset=-1, $prefixes=array() ) {				
		
			YDDebugUtil::debug( YDStringUtil::removeWhiteSpace( $sql ) );

			if ( $limit != -1 || $offset != -1 ) {
				$this->setLimit( $limit, $offset );
			}
			
			$results = $this->__db->getRecords( $sql, $this->__limit, $this->__offset );			

			$this->__results = array();			
			$this->__count   = $results ? sizeof( $results ) : 0;
						
			$this->resetQuery();			
			
			if ( ! $results ) {
				return $this->__count;
			}

			if ( ! sizeof( $prefixes ) ) {
				$prefixes = array( '' );
			}

			for ( $i=0; $i < count( $prefixes ); $i++ ) {

				$prefix = ( sizeof( $prefixes ) > 1 ) ? substr( '0' . ( $i+1 ) . '_', -3 ) : '';
				$var = $prefixes[ $i ];

				if ( ! strlen( $var ) ) {
					$dataobject = & $this;
				}  else {
					$dataobject = & $this->$var;
				}
				
				$dataobject->__results = array();
				
				if ( sizeof( $prefixes ) > 1 ) {
					
					// Find the range of fields with prefix
					$first_result = $results[0];
					ksort( $first_result );
					
					$pos = false;
					$length = 0;
					$j = 0;
		
					foreach ( $first_result as $field => $value ) {
						if ( substr( $field, 0, strlen( $prefix ) ) == $prefix ) {
							if ( $pos === false ) {
								$pos = $j;
							}
							$length++;
						} 
						$j++;
					}
					
					// Filter the range of prefix for each result
					$j = 0;
					$continue = true;
					while ( $continue ) {
						
						$result = & $results[ $j ];
						ksort( $result );						
						$j++;						
						
						$res = array_splice( $result, $pos, $length );
						
						// Take out the prefix
						$res_noprefix = array();
						foreach ( $res as $field => $value ) {
							$field = substr( $field, strlen( $prefix ), strlen( $field ) - strlen( $prefix ) );
							$res_noprefix[ $field ] = $value;
						}
						unset( $res );
		
						// Add the result to the dataobject
						$dataobject->__results[] = $res_noprefix;
						
						if ( ! array_key_exists( $j, $results ) ) {
							$continue = false;
						}
		
					}
				} else {
					$dataobject->__results = $results;
				}
				
				if ( $this->__count == 1 ) {
					$dataobject->setValues( $dataobject->__results[0] );
				}
				
			}
		
			return $this->__count;
			
		}		
		
		/**
		 *  This function executes any SQL query.
		 *  
		 *  @param $sql  The SQL query
		 *
		 *  @returns  The result
		 */				
		function executeSql( $sql ) {
			return $this->__db->executeSql( $sql );		
		}

		/**
		 *  This function returns the total of results remaining to be fetched.
		 *  
		 *  @returns  The number of rows to be fetch.
		 */				
		function count() {				
			return $this->__count;
		}

		/**
		 *  This function fetch the results and adds it's values to the object.
		 *  
		 *  @returns  True if any result has been fetched, otherwise false.
		 */				
		function fetch() {
			$values = array_shift( $this->__results );
			if ( ! is_null( $values ) ) {
				$this->setValues( $values );
				return true;
			} 
			return false;
		}
		
		/**
		 *  This function executes an INSERT query based on the values of the object.
		 *
		 *  @param $auto  (optional) Defines if the auto increment field (if any) 
		 *                should be excluded from the query.
		 *
		 *  @returns  The last autoincrement value if any.
		 */					
		function insert( $auto=true ) {
					
			$values = $this->getValues( true );

			// Get AUTO-INCREMENT field
			$auto_fields = array();
			foreach ( $this->__fields as $field => $specs ) {
				if( $this->_getFieldSpec( $field, 'auto' ) ) {
					$auto_fields[] = $field;
				}
			}			
			if ( sizeof( $auto_fields ) > 1 ) {
				trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
								Multiple AUTO_INCREMENT fields are not allowed.', YD_ERROR );
			}
			
			$auto_field = sizeof( $auto_fields ) ? $auto_fields[0] : false;

			// Remove auto-increment field value
			if ( ! $auto && $auto_field ) {
				if ( array_key_exists( $auto_field, $values ) ) {
					unset( $values[ $auto_field ] );
				}
			}
			
			$this->resetQuery();

			if ( sizeof( $values ) ) {
			
				// Convert fields to columns names
				$new = array();
				foreach ( $values as $field => $value ) {
					$new[ $this->_getFieldSpec( $field, 'column' ) ] = $value;
				}
				$values = $new;
				unset( $new );
				
				YDDebugUtil::debug( 'YDDatabaseObject ' . $this->getClassName() . YDDebugUtil::r_dump( $this->getValues() ) );

				$result = $this->__db->executeInsert( $this->getTable(), $values);
				
				YDDebugUtil::debug( end( $GLOBALS['YD_SQL_QUERY'] ) );
				
				if ( is_numeric( $result ) && strlen( $auto_field ) ) {
					$this->set( $auto_field, $result );
				}				
				return $result;
			}
			return false;
		}

		/**
		 *  This function executes an UPDATE query based on the values of the object
		 *  and any value set by addWhere.
		 *
		 *  @returns  The number of rows affected.
		 */			
		function update() {
		
			$values = $this->getValues( true );
			
			// Don't update keys
			foreach ( $values as $field => $value ) {
				if ( in_array( $field, $this->__keys ) ) {
					unset( $values[ $field ] );
				}
			}
			
			if ( sizeof( $values ) ) {				
			
				// Convert fields to columns names
				$new = array();
				foreach ( $values as $field => $value ) {
					$new[ $this->_getFieldSpec( $field, 'column' ) ] = $value;
				}
				$values = $new;
				unset( $new );
				
				$this->_prepareQuery( $this, true );
				$where = $this->__sql->getWhere( false );				
				$this->resetQuery();

				YDDebugUtil::debug( 'YDDatabaseObject ' . $this->getClassName() . YDDebugUtil::r_dump( $this->getValues() ) );

				// Not secure if no conditions...
				if( ! strlen( trim( $where ) ) && ! YDConfig::get( 'YD_DATABASEOBJECT_UPDATE' ) ) {
					trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
									Your UPDATE query has no conditions and will not be executed.', YD_NOTICE );
					
					return;
				}
			
				$result = $this->__db->executeUpdate( $this->getTable(), $values, $where );
				$this->__count = (int) $result;
				
				YDDebugUtil::debug( end( $GLOBALS['YD_SQL_QUERY'] ) );	

				return $this->__count;
			}
			return false;
		}
		
		/**
		 *  This function executes an DELETE query based on the values of the object
		 *  or any condition set by addWhere and addOrder.
		 *
		 *  @returns  The number of rows affected.
		 */			
		function delete() {

			$this->__sql->setAction( 'DELETE' );
			$this->__sql->resetFrom();
			$this->__sql->addTable( $this->getTable() );
			
			$this->_prepareQuery( $this, true );

			$where = $this->__sql->getWhere( false );
			$order = $this->__sql->getOrder();
			
			$this->resetQuery();

			YDDebugUtil::debug( 'YDDatabaseObject ' . $this->getClassName() . YDDebugUtil::r_dump( $this->getValues() ) );

			// Not secure if no conditions...
			if ( ! strlen( trim( $where ) ) && ! YDConfig::get( 'YD_DATABASEOBJECT_DELETE' ) ) {
				trigger_error( 'YDDatabaseObject delete - ' . $this->getClassName() . ' - 
								Your DELETE query has no conditions and will not be executed.', YD_NOTICE );
				
				return;			
			}			
			
			$result = $this->__db->executeDelete( $this->getTable(), $where . $order ); 
			$this->__count = (int) $result;
			
			YDDebugUtil::debug( end( $GLOBALS['YD_SQL_QUERY'] ) );
			
			return $this->__count;
			
		}

		/**
		 *  This function resets the object's values, any additional query value and
		 *  sets the default values for the fields.
		 */			
		function reset() {
			$this->setValues();
			$this->resetQuery();
		}

		/**
		 *	Adds a select expression. 
		 *
		 *	@param $expr   The expression.
		 *  @param $alias  (optional) The alias for the expression.
		 */
		function addSelect() {
		
			$args = func_get_args();
			
			if ( sizeof( $args ) ) {
			
				foreach ( $args as $field ) {
				
					if ( ! array_key_exists( $field, $this->__fields ) ) {
						trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
										Field "' . $field . '" is not registered.', YD_ERROR );
					}
				
					$column = $this->_getFieldSpec( $field, 'column' );
				
					if ( $this->_getFieldSpec( $field, 'type' ) != YD_DATABASEOBJECT_SQL ) {
						$column = $this->getTable() . '.' . $column;
					}
	
					$this->__sql->addSelect( $column, $field );				
				}			
			}			
		}
		
		/**
		 *	Adds a condition to the WHERE clause.
		 *
		 *  @param $expr   The condition expression.
		 *  @param $logic  (optional) Logic operator (e.g. AND, OR, XOR). Default: AND.
		 */			
		function addWhere( $expr, $logic='AND' ) {
			$this->__sql->addWhere( $expr, $logic );
		}

		/**
		 *	Adds an expression to the GROUP BY clause.
		 *
		 *	@param $expr  The expression.
		 *  @param $desc  (optional) If true adds a DESC string to the expression. Default: false.
		 */				
		function addGroup( $expr, $desc=false ) {
			$this->__sql->addGroup( $expr, $desc );
		}


		/**
		 *	Adds an expression to the HAVING clause.
		 *
		 *	@param $expr   The expression.
		 *  @param $logic  (optional) Logic operator (e.g. AND, OR, XOR). Default: AND.
		 */				
		function addHaving( $expr, $logic='AND' ) {
			$this->__sql->addHaving( $expr, $logic );
		}

		/**
		 *	Adds a column to the ORDER BY clause.
		 *
		 *	@param $expr  Column name.
		 *  @param $desc  (optional) If true adds a DESC string to the column. Default: false.
		 */				
		function addOrder( $expr, $desc=false ) {
			$this->__sql->addOrder( $expr, $desc );
		}

		/**
		 *	This function sets the limit for SELECT queries only.
		 *
		 *  @param $limit     How many records to return.
		 *  @param $offset    (optional) Where to start from.
		 */					
		function setLimit( $limit, $offset=-1 ) {
			$this->__limit  = $limit;
			$this->__offset = $offset;
		}

		/**
		 *	This function resets all the query information and adds
		 *  the fields columns to the SELECT statement as default.
		 */					
		function resetQuery() {					
			$this->__sql->reset();
			$this->__limit  = -1;
			$this->__offset = -1;
		}				

		/**
		 *	This function resets all protected fields to it's protected value.
		 *
		 *  @internal
		 */			
		function _resetProtected() {				
			foreach ( $this->__protected as $field => $value ) {
				$this->set( $field, $value, true );
			}
		}
		
		/**
		 *  This function returns a field specification.
		 *
		 *  @param $field  The field name.
		 *  @param $spec   The specification.
		 *
		 *  @returns  The specification value for the field.
		 *
		 *  @internal
		 */		
		function _getFieldSpec( $field, $spec ) {
		
			$possible = array( 'type', 'column', 'default', 'callback', 'null', 'auto' );
							   
			if ( ! in_array( $spec, $possible ) ) {
				trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
								"' . $spec . '" is not a valid specification for a field.', YD_ERROR );
			}
		
			if ( array_key_exists( $field, $this->__fields ) ) {
				if ( array_key_exists( $spec, $this->__fields[ $field ] ) ) {
					return $this->__fields[ $field ][ $spec ];
				}
				switch ( $spec ) {
					case 'column':
						return $field;
					case 'null':
					case 'auto':
						return false;
				}
			} 
			return null;
			
		}
				
		/**
		 *  This function returns a relation specification.
		 *
		 *  @param $relation  The relation id.
		 *  @param $spec      The specification name.
		 *  
		 *  @returns  The specification value.
		 *
		 *  @internal
		 */		
		function _getRelationSpec( $relation, $spec ) {
				
		
			$possible = array( 'type', 'local_key', 'foreign_dataobject', 'foreign_type_join', 
							   'foreign_key', 'foreign_var', 'join_dataobject', 'join_type_join',
							   'join_local_key', 'join_foreign_key', 'join_var', 'join_conditions',
							   'foreign_conditions', 'where', 'order', 'group', 'having', 'select' );
							   
			if ( ! in_array( $spec, $possible ) ) {
				trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
								"' . $spec . '" is not a valid specification for a relation.', YD_ERROR );
			}
			
			$specs = $this->__relations[ $relation ];
			$part = isset( $specs[ $spec ] ) ? $specs[ $spec ] : null;
			
			if ( is_null( $part ) ) {
			
				switch ( $spec ) {
					case 'foreign_var':
						return $relation;
						
					case 'join_var':
						return 'join_' . $relation;
						
					case 'local_key':
						if ( sizeof( $this->__keys ) == 1 ) {
							return $this->_getFieldSpec( $this->__keys[0], 'column' );
						}
						break;
						
					case 'foreign_key':
						$f_var = $this->_getRelationSpec( $relation, 'foreign_var' );
						if ( sizeof( $this->$f_var->__keys ) == 1 ) {
							return $this->$f_var->_getFieldSpec( $this->$f_var->__keys[0], 'column' );
						}
						break;
						
					case 'join_local_key':
						$l_key   = $this->_getRelationSpec( $relation, 'local_key' );						
						$l_table = $this->getTable();
						return $l_table . '_' . $l_key;
						
					case 'join_foreign_key':
						$f_var = $this->_getRelationSpec( $relation, 'foreign_var' );						
						$f_key = $this->_getRelationSpec( $relation, 'foreign_key' );
						$f_table = $this->$f_var->getTable();
						return $f_table . '_' . $f_key;
					
					case 'foreign_type_join':
					case 'join_type_join':
						return 'INNER';
					case 'join_conditions':
					case 'foreign_conditions':
					case 'where':
					case 'order':
					case 'group':
					case 'having':
					case 'select':
						return '';
					
				}
								
				trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
								Required specification "' . $spec . '" not defined 
								in relation "' . $relation . '".', YD_ERROR );
				
			}

			return $part;
			
		}


		/**
		 *  This function prepares the SQL query based on the fields of the object.
		 *
		 *  @param $dataobject  A reference to the YDDatabaseObject.
		 *  @param $only_keys   (optional) Adds only the keys to the WHERE clause.
		 *  @param $prefix		(optional) The fields prefix.
		 *
		 *  @internal
		 */
		function _prepareQuery( & $dataobject, $only_keys=false, $prefix='' ) {				
			
			$table = $dataobject->getTable();
			
			$dataobject->_resetProtected();

			// Prepare the SELECT statement			
			if ( $dataobject->__sql->getSelect() == '*' ) {
				
				foreach ( $dataobject->__fields as $field => $specs ) {
					$dataobject->addSelect( $field );
				}
				
			}

			$select = & $dataobject->__sql->select;
			
			if ( strlen( $prefix ) ) {
				foreach ( $dataobject->__sql->select as $num => $arr ){
					if ( array_key_exists( $arr['alias'], $dataobject->__fields ) ) {
						$select[ $num ]['alias'] = $prefix . $arr['alias'];
					}
				}
			}
			
			// We have no keys, so we get all fields always
			if ( ! sizeof( $dataobject->__keys ) ) {
				$only_keys = false;
			}
			
			// Prepare the WHERE statement			
			$values = $dataobject->getValues( true );

			foreach ( $values as $field => $value ) {
				
				if ( $only_keys && ! in_array( $field, $dataobject->__keys ) ) {
					continue;
				}
				
				$sql_field = $dataobject->getTable() . '.' . $dataobject->_getFieldSpec( $field, 'column' );
				
				$value = $dataobject->get( $field );
				if ( is_null( $value ) ) {
					$sql_value = ' IS NULL';
				} else { 
					if ( $dataobject->_getFieldSpec( $field, 'type' ) != YD_DATABASEOBJECT_NUM ) {
						$sql_value = ' = ' . $dataobject->__db->sqlString( $dataobject->get( $field ) );
					} else {
						$sql_value = ' = ' . $value;
					}
				}
								
				$dataobject->__sql->addWhere( $sql_field . $sql_value );				
				
			}
			
			// Prepare the GROUP BY statement
			$groups = & $dataobject->__sql->group;
			foreach ( $groups as $n => $group ) {				
				if ( array_key_exists( $group['expr'], $dataobject->__fields ) ) {
					$groups[ $n ]['expr']  = $dataobject->getTable() . '.';
					$groups[ $n ]['expr'] .= $dataobject->_getFieldSpec( $order['expr'], 'column' );
				}				
			}

			// Prepare the ORDER BY statement
			$orders = & $dataobject->__sql->order;
			foreach ( $orders as $n => $order ) {				
				if ( array_key_exists( $order['expr'], $dataobject->__fields ) ) {
					$orders[ $n ]['expr']  = $dataobject->getTable() . '.';
					$orders[ $n ]['expr'] .= $dataobject->_getFieldSpec( $order['expr'], 'column' );
				}				
			}
			
			return $table;
			
		}
				
		/**
		 *  This function checks the relation was passed and it's valid.
		 *
		 *  @param $relation  (optional) The relation id. If empty, the last relation id loaded.
		 *  @param $autoload  (optional) Load the relation if not loaded. Default: true.
		 *
		 *  @returns  The relation id.
		 */						
		function _checkRelation( $relation, $autoload=true ) {
		
			$relation = empty( $relation ) ? end( $this->__last ) : $relation;
			
			if ( empty( $relation ) ) {
				if ( sizeof( $this->__relations ) == 1 ) {
					$relation = key( $this->__relations );
				} else {
					trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
									You need to pass a relation name.', YD_ERROR );
				}				
			} 
			
			if ( ! array_key_exists( $relation, $this->__relations ) ) {
				trigger_error( 'YDDatabaseObject ' . $this->getClassName() . ' - 
								The relation "' . $relation . '" is not defined.', YD_ERROR );
			}
			
			$foreign_var = $this->_getRelationSpec( $relation, 'foreign_var' );		
			
			if ( $autoload && ! isset( $this->$foreign_var ) ) {
				$this->loadRelation( $relation );
			}	
			
			return $relation;
			
		}

		/**
		 *  This function loads optional expressions to a relation.
		 *
		 *  @param $relation  The relation id.
		 *  @param $spec      The specification (e.g. order, select, group, etc)
		 *
		 *  @internal
		 */
		function _loadOptionalExpressions( $relation, $spec ) {				
	
			$reset_func = 'reset' . ucwords( $spec );
			$add_func   = 'add' . ucwords( $spec );
			
			if ( strlen( $result = $this->_getRelationSpec( $relation, $spec ) ) ) {
				$this->__sql->$reset_func();
				$this->__sql->$add_func( $result );
			}
		
		}	
		
	}

?>