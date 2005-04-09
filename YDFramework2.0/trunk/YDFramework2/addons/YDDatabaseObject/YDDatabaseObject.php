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
     *  Path to all YDDatabaseObjects.
     *  Default: YD_SELF_DIR
     */
    YDConfig::set( 'YD_DBOBJECT_PATH', YD_SELF_DIR, false );

    /**
     *  YDDatabaseObjects file extension.
     *  Default: YD_SCR_EXT
     */
    YDConfig::set( 'YD_DBOBJECT_EXT', YD_SCR_EXT, false );

    /**
     *  YDDatabaseObject class name prefix.
     *  Default: (empty)
     */
    YDConfig::set( 'YD_DBOBJECT_PREFIX', '', false );

    /**
     *  YDDatabaseObject class name sufix.
     *  Default: (empty)
     */
    YDConfig::set( 'YD_DBOBJECT_SUFIX',  '', false );

    /**
     *  This constant defines if a DELETE query with no conditions can be executed.
     *  Default: false.
     */
    YDConfig::set( 'YD_DBOBJECT_DELETE', false, false );

    /**
     *  This constant defines if a UPDATE query with no conditions can be executed.
     *  Default: false.
     */
    YDConfig::set( 'YD_DBOBJECT_UPDATE', false, false );

    YDInclude( 'YDDatabase.php' );
    YDInclude( 'YDSqlQuery.php' );

    /**
     *  This class defines a YDDatabaseObject object.
     */
    class YDDatabaseObject extends YDAddOnModule {

        var $_db        = null;
        var $_table     = null;
        var $_fields    = null;
        var $_relations = null;
        var $_selects   = null;
        var $_sql       = null;

        var $_limit     = -1;
        var $_offset    = -1;
        var $_count     = 0;
        var $_results   = array();
        var $_last      = array();

        /**
         *  The class constructor.
         */
        function YDDatabaseObject() {

            $this->YDAddOnModule();

            // Setup the module
            $this->_author = 'David Bittencourt';
            $this->_version = '1.0';
            $this->_copyright = '(c) 2005 David Bittencourt, muitocomplicado@hotmail.com';
            $this->_description = 'This class defines a YDDatabaseObject object.';

            $this->_fields    = new YDDatabaseObject_Properties();
            $this->_selects   = new YDDatabaseObject_Properties();
            $this->_relations = new YDDatabaseObject_Properties();
            $this->_sql       = new YDSqlQuery();

        }

        /**
         *  This function returns an instance of a YDDatabaseObject class.
         *
         *  @param $class  (optional) The class name.
         *
         *  @returns    An instance of a YDDatabaseObject class.
         *
         *  @static
         */
        function getInstance( $class='' ) {

            $path     = YDConfig::get( 'YD_DBOBJECT_PATH' ) . YD_DIRDELIM;
            $ext      = YDConfig::get( 'YD_DBOBJECT_EXT' );
            $prefix   = YDConfig::get( 'YD_DBOBJECT_PREFIX' );
            $sufix    = YDConfig::get( 'YD_DBOBJECT_SUFIX' );

            if ( ! strlen( $class ) ) {
                $class = $this->getClassName();
            }

            $path  = $path . $class . $ext;
            $class = $prefix . $class . $sufix;

            if ( ! class_exists( $class ) ) {
                require_once $path;
            }

            return new $class();

        }

        /**
         *  This function register the database connection.
         *
         *  @param $db  Reference to the database abstraction layer.
         *
         *  @returns    A reference to the database abstraction layer.
         */
        function & registerDatabase( & $db ) {
            return $this->_db = & $db;
        }

        /**
         *  This function registers the table name.
         *
         *  @param $name  The table name.
         */
        function & registerTable( $name ) {
            $this->_table = $name;
        }

        /**
         *  This function registers a new field.
         *
         *  @param $name     The field name.
         *  @param $type     (optional) The field type: numeric or string. Default: numeric.
         *  @param $key      (optional) Is a key? Default: false.
         *  @param $auto     (optional) Is auto-increment? Default: false.
         *  @param $null     (optional) Can be null? Default: false.
         *
         *  @returns  A reference to the field object.
         */
        function & registerField( $name, $type='numeric', $key=false, $auto=false, $null=false ) {
            if ( $this->_fields->exists( $name ) || $this->_selects->exists( $name ) ) {
                trigger_error(  $this->getClassName() . ' -
                                The field name "' . $name . '" is already defined.', YD_ERROR );
            }

            return $this->_fields->set( $name, new YDDatabaseObject_Field( $name, $type, $key, $auto, $null ) );
        }

        /**
         *  This function registers a numeric key.
         *
         *  @param $name  The field name.
         *  @param $auto  (optional) The key is a auto-increment field? Default: false.
         *
         *  @returns      A reference to the field object.
         */
        function & registerNumericKey( $name, $auto=false ) {
            return $this->registerField( $name, 'numeric', true, $auto, false );
        }

        /**
         *  This function registers a string key.
         *
         *  @param $name  The field name.
         *
         *  @returns      A reference to the field object.
         */
        function & registerStringKey( $name ) {
            return $this->registerField( $name, 'string', true, false, false );
        }

        /**
         *  This function registers a numeric field.
         *
         *  @param $name  The field name.
         *  @param $null  (optional) The field can be null? Default: false.
         *
         *  @returns      A reference to the field object.
         */
        function & registerNumericField( $name, $null=false ) {
            return $this->registerField( $name, 'numeric', false, false, $null );
        }

        /**
         *  This function registers a string field.
         *
         *  @param $name  The field name.
         *  @param $null  (optional) The field can be null? Default: false.
         *
         *  @returns      A reference to the field object.
         */
        function & registerStringField( $name, $null=false ) {
            return $this->registerField( $name, 'string', false, false, $null );
        }

        /**
         *  This function registers a relation.
         *
         *  @param $name           The relation name.
         *  @param $manytomany     (optional) Is a many-to-many relationship? Default: false - 1-1 or 1-n.
         *  @param $foreign_class  (optional) The foreign class name.
         *                           If empty, the $name parameter will be used.
         *  @param $cross_class    (optional) For many-to-many relations, the cross class name.
         *                            If empty, "cross_" . $name will be used.
         *
         *  @returns      A reference to the relation object.
         */
        function & registerRelation( $name, $manytomany=false, $foreign_class='', $cross_class='' ) {

            if ( $this->_relations->exists( $name ) ) {
                trigger_error(  $this->getClassName() . ' -
                                The relation name "' . $name . '" is already defined.', YD_ERROR );
            }

            $rel_obj = new YDDatabaseObject_Relation( $name, $this->getClassName(), $manytomany,
                                                      $foreign_class, $cross_class );

            return $this->_relations->set( $name, $rel_obj );

        }

        /**
         *  This function registers a select statement.
         *
         *  @param $name  The field name.
         *  @param $expr  The select expression.
         */
        function & registerSelect( $name, $expr ) {
            if ( $this->_fields->exists( $name ) || $this->_selects->exists( $name ) ) {
                trigger_error(  $this->getClassName() . ' -
                                The select name "' . $name . '" is already defined.', YD_ERROR );
            }
            $this->_selects->set( $name, new YDDatabaseObject_Select( $name, $expr ) );
        }

        /**
         *  This function unregisters a select statement.
         *
         *  @param $name  The field name.
         */
        function unregisterSelect( $name ) {
            if ( ! $this->_selects->exists( $name ) ) {
                trigger_error(  $this->getClassName() . ' -
                                The select "' . $name . '" is not defined.', YD_ERROR );
            }
            unset( $this->_selects->$name );
        }

        /**
         *  This function registers a protected field.
         *
         *  @param $name   The field name.
         *  @param $value  The protected value.
         */
        function registerProtected( $name, $value ) {
            if ( ! $this->_fields->exists( $name ) ) {
                trigger_error(  $this->getClassName() . ' -
                                The field "' . $name . '" is not defined.', YD_ERROR );
            }
            $this->_fields->$name->setProtected( $value );
        }

        /**
         *  This function unregisters a protected field.
         *
         *  @param $name   The field name.
         */
        function unregisterProtected( $name ) {
            if ( ! $this->_fields->exists( $name ) ) {
                trigger_error(  $this->getClassName() . ' -
                                The field "' . $name . '" is not defined.', YD_ERROR );
            }
            $this->_fields->$name->unsetProtected();
        }

        /**
         *  This function resets all protected values in the object.
         */
        function resetProtected() {

            $protected = $this->_getFieldsByMethod( 'isProtected' );

            foreach ( $protected as $field ) {
                $this->set( $field->getName(), $field->getProtected() );
            }

        }

        /**
         *  This function resets all default values in the object.
         */
        function resetDefaults() {

            $defaults = $this->_getFieldsByMethod( 'hasDefault' );

            foreach ( $defaults as $field ) {
                $this->set( $field->getName(), $field->getDefault() );
            }

        }


        /**
         *  @returns  The table name of the class.
         */
        function getTable() {
            return $this->_table;
        }

        /**
         *  @returns  A reference to the database class.
         */
        function & getDatabase() {
            return $this->_db;
        }

        /**
         *  This function returns a reference to a defined field.
         *
         *  @param $name  The field name.
         *
         *  @returns  A reference to the field.
         */
        function & getField( $name ) {
            if ( $this->_fields->exists( $name ) ) {
                return $this->_fields->$name;
            }
        }

        /**
         *  This function returns a reference to a defined relation.
         *
         *  @param $name  The relation name.
         *
         *  @returns  A reference to the relation.
         */
        function & getRelation( $name ) {
            if ( $this->_relations->exists( $name ) ) {
                return $this->_relations->$name;
            }
        }

        /**
         *  This function returns a reference to a defined select.
         *
         *  @param $name  The select name.
         *
         *  @returns  A reference to the select.
         */
        function & getSelect( $name ) {
            if ( $this->_selects->exists( $name ) ) {
                return $this->_selects->$name;
            }
        }

        /**
         *  This function returns an array with the fields retrieved by a given method.
         *
         *  @param $method  The method name.
         *  @param $object  (optional) If true, returns the field objects, otherwise the field names. Default: true.
         *
         *  @returns  An array with the fields retrieved by a given method.
         */
        function _getFieldsByMethod( $method, $object=true ) {

            $fields = get_object_vars( $this->_fields );

            $filtered = array();
            foreach ( $fields as $field ) {
                $name = $field->getName();
                if ( call_user_func( array( & $this->_fields->$name, $method ) ) ) {
                    $filtered[] = $object ? $field : $field->getColumn();
                }
            }
            return $filtered;
        }

        /**
         *  This function prepares a query based on the object values.
         *
         *  @param $only_keys  (optional) Use only the keys for the WHERE clause. Default: true.
         *
         *  @internal
         */
        function _prepareQuery( $only_keys=true ) {

            $this->resetProtected();

            $keys    = $this->_getFieldsByMethod( 'isKey' );
            $fields  = get_object_vars( $this->_fields );
            $selects = get_object_vars( $this->_selects );

            // prepare select
            if ( $this->_sql->getSelect() == '*' ) {
                foreach ( $fields as $field ) {
                    $this->addSelect( $field->getName() );
                }
                foreach ( $selects as $select ) {
                    $this->addSelect( $select->getName() );
                }
            }

            // prepare group by
            $groups = & $this->_sql->group;
            foreach ( $groups as $n => $group ) {
                $field = $group['expr'];
                if ( $field_obj = & $this->getField( $field ) ) {
                    $groups[ $n ]['expr'] = $this->getTable() . '.' . $field_obj->getColumn();
                }
            }


            // prepare order by
            $orders = & $this->_sql->order;
            foreach ( $orders as $n => $order ) {
                $field = $order['expr'];
                if ( $field_obj = & $this->getField( $field ) ) {
                    $orders[ $n ]['expr'] = $this->getTable() . '.' . $field_obj->getColumn();
                }
            }


            // prepare where
            if ( $only_keys && ! sizeof( $keys ) ) {
                $only_keys = false;
            }

            if ( $only_keys ) {
                $fields = $keys;
            }

            foreach ( $fields as $field ) {

                if ( ! $this->exists( $field->getName() ) ) {
                    continue;
                }

                $value = $this->get( $field->getName() );

                if ( is_null( $value ) ) {
                    $value = ' IS NULL ';
                } else if ( is_array( $value ) ) {
                    $value = " IN ( '" . implode( "', '", $value ) . "' )";
                } else {
                    if ( $field->isNumeric() ) {
                        $value = ' = ' . $value;
                    } else {
                        $value = " = '" . $value . "'";
                    }
                }

                $this->addWhere( $this->getTable() . '.' . $field->getColumn() . $value );
            }
        }

        /**
         *  This function loads a relation object into the YDDatabaseObject.
         *
         *  @param $relation  The relation name.
         */
        function loadRelation( $relation ) {

            if ( ! $this->_relations->exists( $relation ) ) {
                trigger_error(  $this->getClassName() . ' -
                                The relation "' . $relation . '" is not defined.', YD_ERROR );
            }

            $this->_last = array( $relation );

            $relation = & $this->getRelation( $relation );
            $f_var    = $relation->getForeignVar();
            $f_class  = $relation->getForeignClass();

            if ( ! $this->exists( $f_var ) ) {
                $this->set( $f_var, YDDatabaseObject::getInstance( $f_class ) );
            }

            if ( $relation->isManyToMany() ) {

                $c_var    = $relation->getCrossVar();
                $c_class  = $relation->getCrossClass();

                if ( ! $this->exists( $c_var ) ) {
                    $this->set( $c_var, YDDatabaseObject::getInstance( $c_class ) );
                }

            }

        }

        /**
         *  This function loads all relations objects into the YDDatabaseObject.
         */
        function loadAllRelations() {

            $relations = get_object_vars( $this->_relations );

            foreach ( $relations as $relation ) {
                $this->loadRelation( $relation->getName() );
            }

            $this->_last = array_keys( $relations );

        }

        /**
         *  This function loads and finds the records for relations defined.
         *
         *  @returns  The total rows retrieved.
         */
        function findRelation() {

            $args = func_get_args();

            $relations = $this->_last;
            if ( sizeof( $args ) ) {
                $relations = $args;
            }

            if ( ! sizeof( $relations ) ) {
                return $this->findAllRelations();
            }

            $this->_sql->setAction( 'SELECT' );
            $this->_sql->resetFrom();

            $pos = 0;
            $slices = array();

            foreach ( $relations as $relation ) {

                $this->loadRelation( $relation );

                if ( $pos == 0 ) {
                    $this->_sql->addTable( $this->getTable() );
                    $this->_prepareQuery( false );
                    $slices[ $pos ] = '';
                    $pos = $pos + sizeof( $this->_sql->select );
                }

                $rel = & $this->getRelation( $relation );

                // Local table
                $l_table = $this->getTable();
                $l_key   = current( $this->_getFieldsByMethod( 'isKey', false ) );

                if ( $rel->getLocalField() ) {
                    $l_key = $rel->getLocalField();
                }

                // Foreign table
                $f_var   = $rel->getForeignVar();
                $f_class = $rel->getForeignClass();
                $f_table = $this->$f_var->getTable();
                $f_key   = current( $this->$f_var->_getFieldsByMethod( 'isKey', false ) );

                if ( $rel->getForeignField() ) {
                    $f_key = $rel->getForeignField();
                }

                $this->$f_var->_prepareQuery( false );

                $slices[ $pos ] = $f_var;
                $pos = $pos + sizeof( $this->$f_var->_sql->select );

                $this->_sql->select = array_merge( $this->_sql->select, $this->$f_var->_sql->select );

                if ( ! $rel->isManyToMany() ) {

                    $this->_sql->addJoin( $rel->getForeignJoin(), $f_table );
                    $this->_sql->addJoinOn( $l_table . '.' . $l_key . ' = ' .
                                            $f_table . '.' . $f_key );

                } else {

                    // Cross-table
                    $c_var   = $rel->getCrossVar();
                    $c_class = $rel->getCrossClass();

                    $c_table = $this->$c_var->getTable();

                    $c_fkey  = $f_table . '_' . $f_key;
                    $c_lkey  = $l_table . '_' . $l_key;

                    if ( $rel->getCrossForeignField() ) {
                        $c_fkey = $rel->getCrossForeignField();
                    }
                    if ( $rel->getCrossLocalField() ) {
                        $c_lkey = $rel->getCrossLocalField();
                    }

                    $this->$c_var->_prepareQuery( false );

                    $slices[ $pos ] = $c_var;
                    $pos = $pos + sizeof( $this->$c_var->_sql->select );

                    $this->_sql->select = array_merge( $this->_sql->select, $this->$c_var->_sql->select );

                    $this->_sql->addJoin( $rel->getCrossJoin(), $c_table );
                    $this->_sql->addJoinOn( $l_table . '.' . $l_key . ' = ' .
                                            $c_table . '.' . $c_lkey );

                    if ( $where = $rel->getCrossConditions() ) {
                        $this->_sql->addJoinOn( $where );
                    }

                    if ( $where = $this->$c_var->_sql->getWhere( false ) ) {
                        $this->_sql->addJoinOn( $where );
                    }

                    $this->_sql->addJoin( $rel->getForeignJoin(), $f_table );
                    $this->_sql->addJoinOn( $c_table . '.' . $c_fkey . ' = ' .
                                            $f_table . '.' . $f_key );

                }

                if ( $where = $rel->getForeignConditions() ) {
                    $this->_sql->addJoinOn( $where );
                }

                if ( $where = $this->$f_var->_sql->getWhere( false ) ) {
                    $this->_sql->addJoinOn( $where );
                }

                // Additional conditions
                if ( $where = $rel->getWhere() ) {
                    $this->_sql->resetWhere();
                    $this->_sql->addSelect( $where );
                }

                if ( $group = $rel->getGroup() ) {
                    $this->_sql->resetGroup();
                    $this->_sql->addSelect( $group );
                }

                if ( $having = $rel->getHaving() ) {
                    $this->_sql->resetHaving();
                    $this->_sql->addSelect( $having );
                }

                if ( $order = $rel->getOrder() ) {
                    $this->_sql->resetOrder();
                    $this->_sql->addOrder( $order );
                }

            }

            $this->_last = $relations;

            return $this->findSql( $this->_sql->getSql(), $slices );

        }

        /**
         *  This function executes a single query for all relations that meet the conditions
         *  established by the object values.
         *
         *  @returns  The number of rows found.
         */
        function findAllRelations() {

            $this->_last = array_keys( get_object_vars( $this->_relations ) );

            return $this->findRelation();
        }

        /**
         *  This function returns all the relation objects getValues arrays in
         *  a single array.
         *
         *  @param $only_fields  (optional) Returns only defined fields. Default: false.
         *  @param $columns      (optional) Returns the columns names instead of the fields names.
          *  @param $prefix       (optional) If true all foreign values will be prefixed with the foreign and cross var.
         *                                    Default: true.
         *  @param $relation     (optional) The relation name. If empty, the last relations loaded.
         *
         *  @returns  The single array with all values.
         */
        function getRelationValues( $only_fields=false, $columns=false, $prefix=true, $relation='' ) {

            $relations = $this->_last;

            if ( strlen( $relation ) ) {
                $relations = array( $relation );
            }

            $results = $this->getValues( $only_fields, $columns );

            foreach ( $relations as $relation ) {

                $rel = & $this->getRelation( $relation );

                $f_var = $rel->getForeignVar();
                $values = $this->$f_var->getValues( $only_fields, $columns, $prefix ? $f_var . '_' : '' );

                $results = array_merge( $results, $values );

                if ( $rel->isManyToMany() ) {

                    $c_var = $rel->getCrossVar();
                    $values = $this->$c_var->getValues( $only_fields, $columns, $prefix ? $c_var . '_' : '' );

                    $results = array_merge( $results, $values );
                }

            }

            return $results;

        }

        /**
         *  This function retrieves all the relation results that weren't fetched.
         *
         *  @param $only_fields  (optional) Returns only defined fields. Default: false.
         *  @param $columns      (optional) Returns the columns names instead of the fields names.
         *  @param $prefix       (optional) Adds a prefix to the array keys. Default: (empty)
         *  @param $relation     (optional) The relation name. If empty, the last relations loaded.
         *
         *  @returns  An array with the results.
         */
        function getRelationResults( $only_fields=false, $columns=false, $prefix=true, $relation='' ) {

            $results = array();
            while( $this->fetchRelation( $relation ) ) {
                $results[] = $this->getRelationValues( $only_fields, $columns, $prefix, $relation );
            }

            return $results;

        }

        /**
         *  This function resets the information defined in all the relation objects.
         *
         *  @param $relation  (optional) The relation name. If empty, the last relations loaded.
         */
        function resetRelation( $relation='' ) {

            $relations = $this->_last;

            if ( strlen( $relation ) ) {
                $relations = array( $relation );
            }

            foreach ( $relations as $relation ) {

                $rel = & $this->getRelation( $relation );

                $f_var = $rel->getForeignVar();
                $this->$f_var->reset();

                if ( $rel->isManyToMany() ) {

                    $c_var = $rel->getCrossVar();
                    $this->$c_var->reset();

                }
            }

            $this->reset();

        }

        /**
         *  This function fetch the results of all the objects of the relation.
         *
         *  @param $relation  (optional) The relation name. If empty, the last relations loaded.
         *
         *  @returns  True if we still have records, otherwise false.
         */
        function fetchRelation( $relation='' ) {

            $relations = $this->_last;

            if ( strlen( $relation ) ) {
                $relations = array( $relation );
            }

            foreach ( $relations as $relation ) {

                $rel = & $this->getRelation( $relation );

                $f_var = $rel->getForeignVar();
                $this->$f_var->fetch();

                if ( $rel->isManyToMany() ) {

                    $c_var = $rel->getCrossVar();
                    $this->$c_var->fetch();

                }

            }

            return $this->fetch();

        }

        /**
         *  This function executes an INSERT query based on the values of the object.
         *
         *  @param $auto  (optional) Defines if the auto increment field (if any)
         *                should be kept in the query. Default: true.
         *
         *  @returns  The last autoincrement value if any.
         */
        function insert( $auto=true ) {

            $values = $this->getValues( true, true );

            $auto_field = current( $this->_getFieldsByMethod( 'isAutoIncrement' ) );

            if ( ! $auto && $auto_field ) {
                unset( $values[ $auto_field->getColumn() ] );
            }

            $this->resetQuery();

            if ( ! sizeof( $values ) ) {
                return;
            }

            YDDebugUtil::debug( $this->getClassName() . YDDebugUtil::r_dump( $this->getValues() ) );

            $result = $this->_db->executeInsert( $this->getTable(), $values );

            YDDebugUtil::debug( end( $GLOBALS['YD_SQL_QUERY'] ) );

            if ( is_numeric( $result ) && $auto_field ) {
                $this->set( $auto_field->getName(), $result );
            }
            return $result;

        }

        /**
         *  This function executes an UPDATE query based on the values of the object
         *  and any value set by addWhere.
         *
         *  @returns  The number of rows affected.
         */
        function update() {

            $this->_prepareQuery();

            YDDebugUtil::debug( $this->getClassName() . YDDebugUtil::r_dump( $this->getValues() ) );

            $values = $this->getValues( true, true );
            $where = $this->_sql->getWhere( false );

            $this->resetQuery();

            if ( ! sizeof( $values ) ) {
                return;
            }

            if ( ! strlen( trim( $where ) ) &&  ! YDConfig::get( 'YD_DBOBJECT_UPDATE' ) ) {
                trigger_error(  $this->getClassName() . ' -
                                Your UPDATE query has no conditions and will not be executed.', YD_NOTICE );
                return;
            }

            $result = $this->_db->executeUpdate( $this->getTable(), $values, $where );

            YDDebugUtil::debug( end( $GLOBALS['YD_SQL_QUERY'] ) );

            return $this->_count = (int) $result;
        }

        /**
         *  This function executes an DELETE query based on the values of the object
         *  or any condition set by addWhere and addOrder.
         *
         *  @returns  The number of rows affected.
         */
        function delete() {

            $this->_prepareQuery();

            YDDebugUtil::debug( $this->getClassName() . YDDebugUtil::r_dump( $this->getValues() ) );

            $where = $this->_sql->getWhere( false );
            $order = $this->_sql->getOrder();

            $this->resetQuery();

            if ( ! strlen( trim( $where ) ) &&  ! YDConfig::get( 'YD_DBOBJECT_DELETE' ) ) {
                trigger_error(  $this->getClassName() . ' -
                                Your DELETE query has no conditions and will not be executed.', YD_NOTICE );
                return;
            }

            $result = $this->_db->executeDelete( $this->getTable(), $where . $order );

            YDDebugUtil::debug( end( $GLOBALS['YD_SQL_QUERY'] ) );

            return $this->_count = (int) $result;

        }

        /**
         *  This function finds the rows that match the object field's values
         *  an any other condition added with addWhere, addGroup, addHaving, etc.
         *
         *  You can add values to the method's parameters to search by keys values.
         *  The parameters should set in the same order as the keys were defined.
         *
         *  If you want to limit this query, use the setLimit method.
         *
         *  @returns  The number of records found.
         */
        function find() {

            $keys = $this->_getFieldsByMethod( 'isKey' );

            $args = func_get_args();

            if ( sizeof( $args ) && sizeof( $keys ) ) {

                // Set the keys values
                for ( $i=0; $i < count( $args ); $i++ ) {
                    if ( array_key_exists( $i, $keys ) ) {
                        $this->set( $keys[ $i ]->getName(), $args[ $i ] );
                    }
                }

                $this->_sql->resetWhere();
                $this->_prepareQuery( true );

            } else {
                $this->_prepareQuery( false );
            }

            YDDebugUtil::debug( $this->getClassName() . YDDebugUtil::r_dump( $this->getValues() ) );

            $this->_sql->setAction( 'SELECT' );
            $this->_sql->resetFrom();
            $this->_sql->addTable( $this->getTable() );

            return $this->findSql( $this->_sql->getSql() );

        }

        /**
         *  This function execute the SQL statement passed and adds it's
         *  results to the object.
         *
         *  @param $sql      The SQL statement.
         *  @param $slices   (optional) The slices of the query.
         *
         *  @returns  The number of records found.
         */
        function findSql( $sql, $slices=array() ) {

            YDDebugUtil::debug( YDStringUtil::removeWhiteSpace( $sql ) );

            $fetch = YDConfig::get( 'YD_DB_FETCHTYPE' );
            YDConfig::set( 'YD_DB_FETCHTYPE', YD_DB_FETCH_NUM );

            $results = $this->_db->getRecords( $sql, $this->_limit, $this->_offset );

            YDConfig::set( 'YD_DB_FETCHTYPE', $fetch );

            if ( ! sizeof( $slices ) ) {
                $slices = array( 0 => '' );
            }

            reset( $slices );

            $var = current( $slices );

            while ( $var !== false ) {

                $curr_pos = key( $slices );
                $next = next( $slices );

                $next_pos = $next ? key( $slices ) : false;

                if ( ! $var ) {
                    $obj = & $this;
                } else {
                    $obj = & $this->$var;
                }

                $obj->resetResults();
                $obj->_count = $results ? sizeof( $results ) : 0;

                $select = $obj->_sql->select;

                foreach ( $results as $result ) {

                    if ( ! $next_pos ) {
                        $next_pos = sizeof( $result );
                    }
                    $length = $next_pos - $curr_pos;

                    $res = array_slice( $result, $curr_pos, $length );

                    $new_res = array();
                    for ( $i=0; $i < sizeof( $res ); $i++ ) {
                        $new_res[ $select[ $i ]['alias'] ] = $res[ $i ];
                    }

                    $obj->_results[] = $new_res;

                }

                $obj->resetQuery();

                if ( $obj->_count == 1 ) {
                    $obj->setValues( $obj->_results[0] );
                }

                $var = current( $slices );

            }

            return $this->_count;

        }

        /**
         *  This function executes any SQL query.
         *
         *  @param $sql  The SQL query
         *
         *  @returns  The result
         */
        function executeSql( $sql ) {
            return $this->_db->executeSql( $sql );
        }

        /**
         *  This function returns the total of results remaining to be fetched.
         *
         *  @returns  The number of rows to be fetch.
         */
        function count() {
            return $this->_count;
        }

        /**
         *    Adds a select field to the query. The registered fields and selects can
         *  be added at each parameter of the method.
         */
        function addSelect() {

            $args = func_get_args();

            if ( ! sizeof( $args ) ) {
                return;
            }

            foreach ( $args as $field ) {

                if ( ! $this->_fields->exists( $field ) && ! $this->_selects->exists( $field ) ) {
                    trigger_error(  $this->getClassName() . ' -
                                    Field "' . $field . '" is not registered.', YD_ERROR );
                }
                if ( $select = & $this->getSelect( $field ) ) {
                    $expr = $select->getExpression();
                } else {
                    $field_obj = & $this->getField( $field );
                    $expr = $this->getTable() . '.' . $field_obj->getColumn();
                }
                $this->_sql->addSelect( $expr, $field );
            }

        }

        /**
         *    Adds a condition to the WHERE clause.
         *
         *  @param $expr   The condition expression.
         *  @param $logic  (optional) Logic operator (e.g. AND, OR, XOR). Default: AND.
         */
        function addWhere( $expr, $logic='AND' ) {
            $this->_sql->addWhere( $expr, $logic );
        }

        /**
         *    Adds an expression to the GROUP BY clause.
         *
         *    @param $expr  The expression.
         *  @param $desc  (optional) If true adds a DESC string to the expression. Default: false.
         */
        function addGroup( $expr, $desc=false ) {
            $this->_sql->addGroup( $expr, $desc );
        }

        /**
         *    Adds an expression to the HAVING clause.
         *
         *    @param $expr   The expression.
         *  @param $logic  (optional) Logic operator (e.g. AND, OR, XOR). Default: AND.
         */
        function addHaving( $expr, $logic='AND' ) {
            $this->_sql->addHaving( $expr, $logic );
        }

        /**
         *    Adds a column to the ORDER BY clause.
         *
         *    @param $expr  Column name.
         *  @param $desc  (optional) If true adds a DESC string to the column. Default: false.
         */
        function addOrder( $expr, $desc=false ) {
            $this->_sql->addGroup( $expr, $desc );
        }

        /**
         *    This function sets the limit for SELECT queries only.
         *
         *  @param $limit     How many records to return.
         *  @param $offset    (optional) Where to start from.
         */
        function setLimit( $limit=-1, $offset=-1 ) {
            $this->_limit  = $limit;
            $this->_offset = $offset;
        }

        /**
         *  This function fetch the results and adds it's values to the object.
         *
         *  @returns  True if any result has been fetched, otherwise false.
         */
        function fetch() {
            $res = array_shift( $this->_results );
            if ( ! is_null( $res ) ) {
                $this->setValues( $res );
                return true;
            }
            return false;

        }

        /**
         *  This function resets the object's values, any additional query value and
         *  any result remaining to be fetch.
         */
        function reset() {
            $this->resetValues();
            $this->resetQuery();
            $this->resetResults();
            $this->resetCount();
        }

        /**
         *  This function resets the any result remaining to be fetch.
         */
        function resetResults() {
            $this->_results = array();
        }

        /**
         *  This function resets the count of the results.
         */
        function resetCount() {
            $this->_count   = 0;
        }

        /**
         *  This function resets the object's values.
         */
        function resetValues() {
            $this->setValues( array() );
        }

        /**
         *  This function resets any query addition.
         */
        function resetQuery() {
            $this->_sql->reset();
            $this->_limit  = -1;
            $this->_offset = -1;
        }

        /**
         *  This function retrieves all the object's fields values.
         *
         *  @param $only_fields  (optional) Returns only defined fields. Default: false.
         *  @param $columns      (optional) Returns the columns names instead of the fields names.
         *  @param $prefix       (optional) Adds a prefix to the array keys. Default: (empty)
         *
         *  @returns  An associative array with the values.
         */
        function getValues( $only_fields=false, $columns=false, $prefix='' ) {

            $this->resetProtected();

            $values = get_object_vars( $this );

            $new = array();
            foreach ( $values as $field => $value ) {
                if ( $this->exists( $field ) && ! is_object( $this->$field ) && substr( $field, 0, 1 ) != '_' ) {
                    if ( ! $only_fields  || ( $only_fields && $this->_fields->exists( $field ) ) ) {
                        $key = $prefix . $field;
                        if ( $columns ) {
                            $field_obj = & $this->getField( $field );
                            $key = $prefix . $field_obj->getColumn();
                        }
                        $new[ $key ] = $value;
                    }
                }
            }

            return $new;

        }

        /**
         *  This function retrieves all the results that weren't fetched.
         *
         *  @param $only_fields  (optional) Returns only defined fields. Default: false.
         *  @param $columns      (optional) Returns the columns names instead of the fields names.
         *  @param $prefix       (optional) Adds a prefix to the array keys. Default: (empty)
         *
         *  @returns  An array with the results.
         */
        function getResults( $only_fields=false, $columns=false, $prefix='' ) {

            $results = array();
            while( $this->fetch() ) {
                $results[] = $this->getValues( $only_fields, $columns, $prefix );
            }

            return $results;

        }

        /**
         *  This function receives and adds an associative array of values to
         *  the object. The array keys are the Table fields.
         *
         *  @param $values  The associative array of values.
         */
        function setValues( $values ) {

            $current = $this->getValues();

            foreach ( $current as $field => $value ) {
                if ( ! array_key_exists( $field, $values ) ) {
                    unset( $this->$field );
                }
            }
            foreach( $values as $field => $value ) {
                $this->set( $field, $value );
            }

            $this->resetProtected();

        }

        /**
         *  This function sets the value of a field.
         *
         *  @param $field        The field name
         *  @param $value        The field value
         */
        function set( $field, $value ) {

            if ( ! strlen( $field ) ) {
                return;
            }

            if ( is_null( $value ) ) {

                unset( $this->$field );

                if ( $this->_fields->exists( $field ) && ! $this->_fields->$field->isNull() ) {
                    return;
                } else if ( ! $this->_fields->exists( $field ) ) {
                    return;
                }

            }

            $this->$field = $value;

            if ( $this->_fields->exists( $field ) || $this->_selects->exists( $field ) ) {

                if ( $this->_fields->exists( $field ) ) {
                    $callback = $this->_fields->$field->getCallback();
                } else {
                    $callback = $this->_selects->$field->getCallback();
                }

                if ( ! strlen( $callback ) ) {
                    return;
                }

                if ( ! $this->hasMethod( $callback ) ) {
                    trigger_error(  $this->getClassName() . ' -
                                    The callback method "' . $callback . '" is not defined.', YD_ERROR );
                }
                call_user_func( array( & $this, $callback ), $value );
            }

        }

        /**
         *  This function checks if a field value is set.
         *
         *  @param $field        The field name
         *
         *  @returns If the field value is set.
         */
        function exists( $field ) {
            return array_key_exists( $field, $this );
        }

        /**
         *  This function returns the value of a field.
         *
         *  @param $field        The field name
         *
         *  @returns The field value.
         */
        function get( $field ) {
            return $this->exists( $field ) ? $this->$field : null;
        }


    }

    /**
     *  This class defines a YDDatabaseObject_Properties object.
     */
    class YDDatabaseObject_Properties extends YDBase {

        /**
         *  This class constructor.
         */
        function YDDatabaseObject_Properties() {
            $this->YDBase();
        }

        /**
         *  This function sets the value of a property.
         *
         *  @param $name        The property name.
         *  @param $value        The property value.
         *
         *  @returns  A reference to the property.
         */
        function & set( $name, $value ) {
            $this->$name = $value;
            return $this->$name;
        }

        /**
         *  This function checks if a property is set.
         *
         *  @param $name        The property name.
         *  @param $null        (optional) If true, it returns properties that have null value.
         *
         *  @returns  If a property is set.
         */
        function exists( $name, $null=false ) {
            return $null ? array_key_exists( $name, $this ) : isset( $this->$name );
        }

        /**
         *  This function returns a property value.
         *
         *  @param $name        The property name.
         *
         *  @returns  The property value.
         */
        function get( $name ) {
            return $this->exists( $name, true ) ? $this->$name : null;
        }

    }

    /**
     *  This class defines a YDDatabaseObject_Field object.
     */
    class YDDatabaseObject_Field extends YDDatabaseObject_Properties {

        var $name;
        var $column;
        var $null;
        var $auto;
        var $type;
        var $key;
        var $protected;
        var $default;
        var $callback;

        /**
         *  The class constructor.
         *
         *  @param $name     The field name.
         *  @param $type     (optional) The field type: numeric or string. Default: numeric.
         *  @param $key      (optional) Is a key? Default: false.
         *  @param $auto     (optional) Is auto-increment? Default: false.
         *  @param $null     (optional) Can be null? Default: false.
         */
        function YDDatabaseObject_Field( $name, $type='numeric', $key=false, $auto=false, $null=false ) {

            $this->YDDatabaseObject_Properties();

            $this->setName( $name );
            $this->setType( $type );

            if ( $key ) {  $this->setKey(); }
            if ( $auto ) { $this->setAutoIncrement(); }
            if ( $null ) { $this->setNull(); }

        }

        /**
         *  This function sets the field name.
         *
         *  @param $name     The field name.
         */
        function setName( $name ) {
            $this->set( 'name', $name );
        }

        /**
         *  @returns  The field name.
         */
        function getName() {
            return $this->get( 'name' );
        }

        /**
         *  This function sets the field type.
         *
         *  @param $type     The type.
         */
        function setType( $type ) {
            $this->set( 'type', $type );
        }

        /**
         *  @returns  The field type.
         */
        function getType() {
            return strtolower( $this->get( 'type' ) );
        }

        /**
         *  This function sets the field column name.
         *
         *  @param $name     The column name.
         */
        function setColumn( $name ) {
            $this->set( 'column', $name );
        }

        /**
         *  @returns  The field column name if defined, or the field name.
         */
        function getColumn() {
            return $this->exists( 'column' ) ? $this->get( 'column' ) : $this->getName();
        }

        /**
         *  This function sets the field as protected.
         *
         *  @param $value     The protected value.
         */
        function setProtected( $value ) {
            $this->setDefault( $value );
            $this->set( 'protected', true );
        }

        /**
         *  This function unsets a field as protected.
         */
        function unsetProtected() {
            $is = $this->isProtected();
            $this->set( 'protected', false );

            if ( $is ) {
                $this->unsetDefault();
            }
        }

        /**
         *  @returns  If the field is protected.
         */
        function isProtected() {
            return $this->get( 'protected' );
        }

        /**
         *  @returns  The protected value.
         */
        function getProtected() {
            return $this->getDefault();
        }

        /**
         *  @returns  If a field has a default value defined.
         */
        function hasDefault() {
            return $this->exists( 'default', true );
        }

        /**
         *  This function sets the default value to the field.
         *
         *  @param $value     The default value.
         */
        function setDefault( $value ) {
            if ( ! $this->isProtected() ) {
                $this->set( 'default', $value );
            }
        }

        /**
         *  This function unsets the default value of a field.
         */
        function unsetDefault() {
            if ( ! $this->isProtected() ) {
                unset( $this->default );
            }
        }

        /**
         *  @returns  The field default value.
         */
        function getDefault() {
            return $this->get( 'default' );
        }

        /**
         *  @returns  If the field is numeric.
         */
        function isNumeric() {
            return ( $this->getType() == 'numeric' ) ? true : false;
        }

        /**
         *  @returns  If the field is a string.
         */
        function isString() {
            return ( $this->getType() == 'string' ) ? true : false;
        }

        /**
         *  This function sets the field as primary key.
         */
        function setKey() {
            $this->set( 'key', true );
        }

        /**
         *  This function unsets the field as primary key.
         */
        function unsetKey() {
            $this->set( 'key', false );
        }

        /**
         *  @returns  If the field is a primary key.
         */
        function isKey() {
            return $this->get( 'key' );
        }

        /**
         *  This function sets the field as null.
         */
        function setNull() {
            $this->set( 'null', true );
        }

        /**
         *  This function unsets the field as null.
         */
        function unsetNull() {
            $this->set( 'null', false );
        }

        /**
         *  @returns  If the field can be null.
         */
        function isNull() {
            return $this->get( 'null' );
        }

        /**
         *  This function sets the field as auto-increment.
         */
        function setAutoIncrement() {
            $this->set( 'auto', true );
        }

        /**
         *  This function unsets the field as auto-increment.
         */
        function unsetAutoIncrement() {
            $this->set( 'auto', false );
        }

        /**
         *  @returns  If the field is auto-increment.
         */
        function isAutoIncrement() {
            return $this->get( 'auto' );
        }

        /**
         *  This function sets the callback for the field.
         *
         *  @param $callback  The callback method name.
         */
        function setCallback( $callback ) {
            $this->set( 'callback', $callback );
        }

        /**
         *  @returns  The callback method name.
         */
        function getCallback() {
            return strtolower( $this->get( 'callback' ) );
        }


    }

    /**
     *  This class defines a YDDatabaseObject_Relation object.
     */
    class YDDatabaseObject_Relation extends YDDatabaseObject_Properties {

        var $name;
        var $manytomany;

        var $local;
        var $foreign;
        var $cross;

        /**
         *  The class constructor.
         *
         *  @param $name           The relation name.
         *  @param $local_class    The local class name.
         *  @param $manytomany     (optional) Is a many-to-many relationship? Default: false.
         *  @param $foreign_class  (optional) The foreign class name.
         *                           If empty, the $name parameter will be used.
         *  @param $cross_class    (optional) For many-to-many relations, the cross class name.
         *                            If empty, "cross_" . $name will be used.
         */
        function YDDatabaseObject_Relation( $name, $local_class, $manytomany=false, $foreign_class='', $cross_class='' ) {

            $this->local   = new YDDatabaseObject_Properties();
            $this->foreign = new YDDatabaseObject_Properties();
            $this->cross   = new YDDatabaseObject_Properties();

            $this->setName( $name );

            if ( $manytomany ) { $this->setManyToMany(); }

            if ( ! strlen( $foreign_class ) ) { $foreign_class = $this->getName();    }
            if ( ! strlen( $cross_class   ) ) { $cross_class   = $local_class . '_' . $foreign_class; }

            $this->setForeignClass( $foreign_class );
            $this->setCrossClass( $cross_class );

        }

        /**
         *  This function set the relation name.
         *
         *  @param $name  The relation name.
         */
        function setName( $name ) {
            $this->set( 'name', $name );
        }

        /**
         *  @returns  The relation name.
         */
        function getName() {
            return $this->get( 'name' );
        }

        /**
         *  @returns  If a relation is a many-to-many relation.
         */
        function isManyToMany() {
            return $this->get( 'manytomany' );
        }

        /**
         *  This function set the relation type as many-to-many.
         */
        function setManyToMany() {
            $this->set( 'manytomany', true );
        }

        /**
         *  This function set the local field.
         *
         *  @param $name  The field name.
         */
        function setLocalField( $name ) {
            $this->local->set( 'field', $name );
        }

        /**
         *  @returns  The local field.
         */
        function getLocalField() {
            return $this->local->get( 'field' );
        }

        /**
         *  @returns  If exists a foreign class defined.
         */
        function hasForeignClass() {
            return $this->foreign->exists( 'class' );
        }

        /**
         *  This funciton set the foreign class.
         *
         *  @param $name  The class name.
         */
        function setForeignClass( $name ) {
            $this->foreign->set( 'class', $name );
        }

        /**
         *  @returns  The foreign class name.
         */
        function getForeignClass() {
            return $this->foreign->get( 'class' );
        }

        /**
         *  This funciton set the foreign field name.
         *
         *  @param $name  The field name.
         */
        function setForeignField( $name ) {
            $this->foreign->set( 'field', $name );
        }

        /**
         *  @returns  The foreign field name.
         */
        function getForeignField() {
            return $this->foreign->get( 'field' );
        }

        /**
         *  This funciton set the foreign join type.
         *
         *  @param $type  The foreign join type. E.g. INNER, LEFT, RIGHT, etc.
         */
        function setForeignJoin( $type ) {
            $this->foreign->set( 'join', $type );
        }

        /**
         *  @returns  The foreign join type. Default: INNER.
         */
        function getForeignJoin() {
            return $this->foreign->exists( 'join' ) ? $this->foreign->get( 'join' ) : 'INNER';
        }

        /**
         *  This funciton set the foreign var.
         *
         *  @param $name  The variable name.
         */
        function setForeignVar( $name ) {
            $this->foreign->set( 'var', $name );
        }

        /**
         *  @returns  The foreign variable name. Default: the relation name.
         */
        function getForeignVar() {
            return $this->foreign->exists( 'var' ) ? $this->foreign->get( 'var' ) : $this->getForeignClass();
        }

        /**
         *  This funciton set the foreign conditions.
         *
         *  @param $expr  The expression.
         */
        function setForeignConditions( $expr ) {
            $this->foreign->set( 'conditions', $expr );
        }

        /**
         *  @returns  The foreign conditions.
         */
        function getForeignConditions() {
            return $this->foreign->get( 'conditions' );
        }

        /**
         *  @returns  If the cross class name is defined.
         */
        function hasCrossClass() {
            if ( ! $this->isManyToMany() ) {
                return true;
            }
            return $this->cross->exists( 'class' );
        }

        /**
         *  This funciton set the cross class.
         *
         *  @param $name  The class name.
         */
        function setCrossClass( $name ) {
            $this->cross->set( 'class', $name );
        }

        /**
         *  @returns  The cross class name.
         */
        function getCrossClass() {
            return $this->cross->get( 'class' );
        }

        /**
         *  This funciton set the cross local field.
         *
         *  @param $name  The field name.
         */
        function setCrossLocalField( $name ) {
            $this->cross->set( 'local_field', $name );
        }

        /**
         *  @returns  The cross local field name.
         */
        function getCrossLocalField() {
            return $this->cross->get( 'local_field' );
        }

        /**
         *  This funciton set the cross foreign field.
         *
         *  @param $name  The field name.
         */
        function setCrossForeignField( $name ) {
            $this->cross->set( 'foreign_field', $name );
        }

        /**
         *  @returns  The cross foreign field name.
         */
        function getCrossForeignField() {
            return $this->cross->get( 'foreign_field' );
        }

        /**
         *  This funciton set the cross join type.
         *
         *  @param $type  The join type. E.g. INNER, LEFT, RIGHT, etc.
         */
        function setCrossJoin( $type ) {
            $this->cross->set( 'join', $type );
        }

        /**
         *  @returns  The cross join type. Default: INNER.
         */
        function getCrossJoin() {
            return $this->cross->exists( 'join' ) ? $this->cross->get( 'join' ) : 'INNER';
        }

        /**
         *  This funciton set the cross variable.
         *
         *  @param $name  The variable name.
         */
        function setCrossVar( $name ) {
            $this->cross->set( 'var', $name );
        }

        /**
         *  @returns  The cross variable name.
         */
        function getCrossVar() {
            return $this->cross->exists( 'var' ) ? $this->cross->get( 'var' ) : $this->getCrossClass();
        }

        /**
         *  This funciton set the cross conditions.
         *
         *  @param $expr  The expression.
         */
        function setCrossConditions( $expr ) {
            $this->cross->set( 'conditions', $expr );
        }

        /**
         *  @returns  The cross conditions.
         */
        function getCrossConditions() {
            return $this->cross->get( 'conditions' );
        }

        /**
         *  This funciton set the relation WHERE clause.
         *
         *  @param $expr  The expression.
         */
        function setWhere( $expr ) {
            $this->local->set( 'where', $expr );
        }

        /**
         *  @returns  The defined WHERE clause.
         */
        function getWhere() {
            return $this->local->get( 'where' );
        }

        /**
         *  This funciton set the relation GROUP BY clause.
         *
         *  @param $expr  The expression.
         */
        function setGroup( $expr ) {
            $this->local->set( 'group', $expr );
        }

        /**
         *  @returns  The defined GROUP BY clause.
         */
        function getGroup() {
            return $this->local->get( 'group' );
        }

        /**
         *  This funciton set the relation HAVING clause.
         *
         *  @param $expr  The expression.
         */
        function setHaving( $expr ) {
            $this->local->set( 'having', $expr );
        }

        /**
         *  @returns  The defined HAVING clause.
         */
        function getHaving() {
            return $this->local->get( 'having' );
        }

        /**
         *  This funciton set the relation ORDER BY clause.
         *
         *  @param $expr  The expression.
         */
        function setOrder( $expr ) {
            $this->local->set( 'order', $expr );
        }

        /**
         *  @returns  The defined ORDER BY clause.
         */
        function getOrder() {
            return $this->local->get( 'order' );
        }

    }


    /**
     *  This class defines a YDDatabaseObject_Select object.
     */
    class YDDatabaseObject_Select extends YDDatabaseObject_Properties {

        var $name;
        var $expr;
        var $callback;

        /**
         *  The class constructor.
         *
         *  @param $name  The select name.
         *  @param $expr  The select expression.
         */
        function YDDatabaseObject_Select( $name, $expr ) {

            $this->YDDatabaseObject_Properties();

            $this->setName( $name );
            $this->setExpression( $expr );

        }

        /**
         *  This function sets the select name.
         *
         *  @param $name  The name.
         */
        function setName( $name ) {
            $this->set( 'name', $name );
        }

        /**
         *  @returns  The select name.
         */
        function getName() {
            return $this->get( 'name' );
        }

        /**
         *  This function sets the select expression.
         *
         *  @param $expr  The expression.
         */
        function setExpression( $expr ) {
            $this->set( 'expr', $expr );
        }

        /**
         *  @returns  The select expression.
         */
        function getExpression() {
            return $this->get( 'expr' );
        }

        /**
         *  This function sets the callback method.
         *
         *  @param $callback  The callback method name.
         */
        function setCallback( $callback ) {
            $this->set( 'callback', $callback );
        }

        /**
         *  @returns  The callback method name.
         */
        function getCallback() {
            return strtolower( $this->get( 'callback' ) );
        }

    }


?>