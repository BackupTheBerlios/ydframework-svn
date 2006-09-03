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
     *  Use the class name prefix and sufix for the filenames
     *  Default: false
     */
    YDConfig::set( 'YD_DBOBJECT_FILE',  false, false );
    
    /**
     *  This config defines if a DELETE query with no conditions can be executed.
     *  Default: false.
     */
    YDConfig::set( 'YD_DBOBJECT_DELETE', false, false );

    /**
     *  This config defines if a UPDATE query with no conditions can be executed.
     *  Default: false.
     */
    YDConfig::set( 'YD_DBOBJECT_UPDATE', false, false );

    /**
     *  This config defines if an action should be executed or not.
     *  Default: true.
     */
    YDConfig::set( 'YD_DBOBJECT_EXECUTE', true, false );
    
    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDDatabase.php' );
    include_once( YD_DIR_HOME_ADD . '/YDDatabaseQuery/YDDatabaseQuery.php' );

    /**
     *  This class defines a YDDatabaseObject object.
     */
    class YDDatabaseObject extends YDAddOnModule {

        var $_db        = null;
        var $_table     = null;
        var $_fields    = null;
        var $_relations = null;
        var $_selects   = null;
        var $_callbacks = null;
        var $_query     = null;

        var $_count     = 0;
        var $_results   = array();
        var $_last      = array();
        var $_all       = true;

        /**
         *  The class constructor.
         */
        function YDDatabaseObject() {

            $this->YDAddOnModule();

            // Setup the module
            $this->_author = 'David Bittencourt';
            $this->_version = '4.28';
            $this->_copyright = '(c) 2005 David Bittencourt, muitocomplicado@hotmail.com';
            $this->_description = 'This class defines a YDDatabaseObject object.';

            $this->_fields    = new YDBase();
            $this->_selects   = new YDBase();
            $this->_relations = new YDBase();
            $this->_callbacks = new YDBase();
            
            $this->_callbacks->set( 'find',   array( 'before' => array(), 'after' => array() ) );
            $this->_callbacks->set( 'delete', array( 'before' => array(), 'after' => array() ) );
            $this->_callbacks->set( 'update', array( 'before' => array(), 'after' => array() ) );
            $this->_callbacks->set( 'insert', array( 'before' => array(), 'after' => array() ) );
            $this->_callbacks->set( 'reset',  array( 'before' => array(), 'after' => array() ) );

        }
        
        /**
         *  This function returns an instance of a YDDatabaseObject class.
         *
         *  @param $class  (optional) The class name.
         *  @param $params (optional) The params to use when instantiating the instance.
         *
         *  @returns    An instance of a YDDatabaseObject class.
         *
         *  @static
         */
        function getInstance( $class='', $params=false ) {

            $path     = YDConfig::get( 'YD_DBOBJECT_PATH' ) . '/';
            $ext      = YDConfig::get( 'YD_DBOBJECT_EXT' );
            $prefix   = YDConfig::get( 'YD_DBOBJECT_PREFIX' );
            $sufix    = YDConfig::get( 'YD_DBOBJECT_SUFIX' );

            if ( ! strlen( $class ) ) {
                $class = $this->getClassName();
            } else {
                
                if ( (boolean) YDConfig::get( 'YD_DBOBJECT_FILE' ) ) {
                    $path .= $prefix . $class . $sufix . $ext;
                } else {
                    $path .= $class . $ext;
                }
                $class = $prefix . $class . $sufix;
                
                if ( ! class_exists( $class ) ) {
                    YDInclude( $path );
                }
            }
            
            if ( $params !== false ) { 
                $obj = new $class( $params ); 
            } else { 
                $obj = new $class(); 
            } 
            
            if ( ! $obj->_fields || ! $obj->_table || ! $obj->_db ) {
                trigger_error(  'The "' . $class . '" class is not correctly configured.', YD_ERROR );
            }
            
            return $obj;

        }

        /**
         *  This function register the database connection.
         *
         *  @param $db  The YDDatabase object pointing to the database or the named instance.
         *
         *  @returns    A reference to the YDDatabase object.
         */
        function & registerDatabase( $db=null ) {
        
            if ( is_null( $db ) ) {
                $this->_db = YDDatabase::getNamedInstance();
            } else if ( is_string( $db ) ) {
                $this->_db = YDDatabase::getNamedInstance( $db );
            } else {
                $this->_db = $db;
            }
            
            $this->_query = YDDatabaseQuery::getInstance( $this->_db );
            return $this->_db;
            
        }

        /**
         *  This function registers the table name.
         *
         *  @param $name  The table name.
         *
         *  @returns  A reference to the alias.
         */
        function & registerTable( $name ) {
            
            $this->_table = $name;
            $this->_alias = $name;
            
            return $this->_alias;
            
        }

        /**
         *  This function registers a field.
         *
         *  @param $name  The field name.
         *  @param $null  (optional) The field can be null? Default: false.
         *
         *  @returns      A reference to the field object.
         */
        function & registerField( $name, $null=false ) {
            
            $name = strtolower( $name );
            
            if ( $this->_fields->exists( $name ) || $this->_selects->exists( $name ) ) {
                trigger_error(  $this->getClassName() . ' -
                                The field name "' . $name . '" is already defined.', YD_ERROR );
            }

            return $this->_fields->set( $name, new YDDatabaseObject_Field( $name, false, false, $null ) );
        }
        
        /**
         *  This function registers a key.
         *
         *  @param $name  The field name.
         *  @param $auto  (optional) The key is a auto-increment field? Default: false.
         *
         *  @returns      A reference to the field object.
         */
        function & registerKey( $name, $auto=false ) {
            
            $name = strtolower( $name );
            
            if ( $this->_fields->exists( $name ) || $this->_selects->exists( $name ) ) {
                trigger_error(  $this->getClassName() . ' -
                                The field name "' . $name . '" is already defined.', YD_ERROR );
            }

            return $this->_fields->set( $name, new YDDatabaseObject_Field( $name, true, $auto, false ) );
        }

        /**
         *  This function registers a relation.
         *
         *  @param $name           The relation name.
         *  @param $manytomany     (optional) Is a many-to-many relationship? Default: false - 1-1 or 1-n.
         *  @param $foreign_class  (optional) The foreign class name.
         *                           If empty, the $name parameter will be used.
         *  @param $cross_class    (optional) For many-to-many relations, the cross class name.
         *                            If empty, $local_table . '_' . $foreign_table
         *
         *  @returns      A reference to the relation object.
         */
        function & registerRelation( $name, $manytomany=false, $foreign_class='', $cross_class='' ) {

            $name = strtolower( $name );

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
         *  @param $name      The field name.
         *  @param $expr      The select expression.
         *  @param $all       (optional) Indicates if the select should be added in all queries. Default: true.
         *
         *  @returns      A reference to the select object.
         */
        function & registerSelect( $name, $expr, $all=true ) {
            
            $name = strtolower( $name );
            
            if ( $this->_fields->exists( $name ) || $this->_selects->exists( $name ) ) {
                trigger_error(  $this->getClassName() . ' -
                                The select name "' . $name . '" is already defined.', YD_ERROR );
            }
            return $this->_selects->set( $name, new YDDatabaseObject_Select( $name, $expr, $all ) );
        }

        /**
         *  This function unregisters a select statement.
         *
         *  @param $name  The field name.
         */
        function unregisterSelect( $name ) {
            
            $name = strtolower( $name );
            
            if ( ! $this->_selects->exists( $name ) ) {
                trigger_error(  $this->getClassName() . ' -
                                The select "' . $name . '" is not defined.', YD_ERROR );
            }
            unset( $this->_selects->$name );
        }

        /**
         *  This function registers a protected field.
         *
         *  @param $name       The field name.
         *  @param $value      The protected value.
         *  @param $comparison (optional) The comparison keyword. Accepts: = == <= < >= > != <>
         */
        function registerProtected( $name, $value, $comparison='=' ) {
            
            $name = strtolower( $name );
            
            if ( ! $this->_fields->exists( $name ) ) {
                trigger_error(  $this->getClassName() . ' -
                                The field "' . $name . '" is not defined.', YD_ERROR );
            }
            $this->_fields->$name->setProtected( $value );
            $this->_fields->$name->setComparison( $comparison );
        }

        /**
         *  This function unregisters a protected field.
         *
         *  @param $name   The field name.
         */
        function unregisterProtected( $name ) {
            
            $name = strtolower( $name );
            
            if ( ! $this->_fields->exists( $name ) ) {
                trigger_error(  $this->getClassName() . ' -
                                The field "' . $name . '" is not defined.', YD_ERROR );
            }
            $this->_fields->$name->unsetProtected();
        }

        /**
         *  This function registers a callback method.
         *
         *  @param $method  The method name.
         *  @param $action  The action or array of actions that trigger the call.
         *  @param $before  (optional) Execute callback before the action. Default: false.
         */
        function registerCallback( $method, $action, $before=false ) {
            
            $sub = $before ? 'before' : 'after';
            
            if ( ! is_array( $action ) ) {
                $action = array( $action );
            }
            
            foreach ( $action as $ac ) {
                
                if ( ! $this->_callbacks->exists( strtolower( $ac ) ) ) {
                    trigger_error(  $this->getClassName() . ' -
                                    Incorrect action for callback "' . $method . '".', YD_ERROR );
                }
            
                $act = & $this->_callbacks->$ac;
                $act[ $sub ][ $method ] = '';
            }
            
        }
        
        /**
         *  This function unregisters a callback method.
         *
         *  @param $method  The method name.
         *  @param $action  (optional) The action that triggers the call. If null, from all actions. Default: null.
         */
        function unregisterCallback( $method, $action=null ) {
            
            if ( ! is_null( $action ) && ! $this->_callbacks->exists( strtolower( $action ) ) ) {
                trigger_error(  $this->getClassName() . ' -
                                Incorrect action for unregistering callback method "' . $method . '".', YD_ERROR );
            }
            
            $actions = array( $action );
            if ( is_null( $action ) ) {
                $actions = array_keys( get_object_vars( $this->_callbacks ) );
            }
            
            foreach ( $actions as $action ) {
                $act = & $this->_callbacks->$action;
                unset( $act[ 'before' ][ $method ] );
                unset( $act[ 'after' ][ $method ] );
            }
        }

        /**
         *  This function resets all protected values in the object.
         */
        function resetProtected() {

            $protected = $this->_getFieldsByMethod( 'isProtected' );

            foreach ( $protected as $field ) {
            
                $p_compar = $field->getComparison();
                $p_value  = $field->getProtected();
                
                $field = $field->getName();
                
                switch ( $p_compar ) {
                    
                    case '<':
                        if ( $this->get( $field ) >= $p_value ) {
                            unset( $this->$field );
                        }
                        break;
                    case '<=':
                        if ( $this->get( $field ) > $p_value ) {
                            unset( $this->$field );
                        }
                        break;
                    case '>':
                        if ( $this->get( $field ) <= $p_value ) {
                            unset( $this->$field );
                        }
                        break;
                    case '>=':
                        if ( $this->get( $field ) < $p_value ) {
                            unset( $this->$field );
                        }
                        break;
                    case '<>':
                    case '!=':
                        if ( $this->get( $field ) == $p_value ) {
                            unset( $this->$field );
                        }
                        break;
                    case '=':
                    case '==':
                    default:
                        $this->set( $field, $p_value );
                        break;
                        
                }
            
            }

        }
        
        /**
         *  @returns  The table name of the class.
         */
        function getTable() {
            return $this->_table;
        }
        
        /**
         *  @returns  The table alias.
         */
        function getAlias() {
            return $this->_alias;
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
            $null = null;
            return $null;
        }
        
        /**
         *  This function returns the column name of a defined field.
         *
         *  @param $field  The field name.
         *  @param $alias  (optional) If true, returns the table alias 
         *                 with the column name. Default: false.
         *
         *  @returns  The column name with or without the table alias.
         */
        function getColumn( $field, $alias=false ) {
            
            if ( $f =& $this->getField( $field ) ) {
            
                $str = '';
                $r = '';
            
                if ( $alias ) {
                    $r = $this->_query->getReserved();
                    $str = $r . $this->getAlias() . $r . '.';
                }
                
                return $str . $r . $f->getColumn() . $r;
            
            }
            
            return;
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
            $null = null;
            return $null;
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
            $null = null;
            return $null;
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
                    $filtered[] = $object ? $field : $name;
                }
            }
            return $filtered;
        }
        
        /**
         *  This function prepares a query based on the object values.
         *
         *  @param $only_keys  (optional) Use only the keys for the WHERE clause. Default: true.
         *  @param $update     (optional) Default: false.
         *
         *  @internal
         */
        function _prepareQuery( $only_keys=true, $update=false ) {
            
            $this->resetProtected();

            $alias   = $this->getAlias();
            
            $keys    = $this->_getFieldsByMethod( 'isKey' );
            $fields  = get_object_vars( $this->_fields );
            $selects = get_object_vars( $this->_selects );
            
            $r = $this->_query->getReserved();

            // prepare select
            if ( $this->_query->getSelect() == '*' && $this->_all ) {
                foreach ( $fields as $field ) {
                    $this->select( $field->getName() );
                }
                foreach ( $selects as $select ) {
                    if ( $select->isAllQueries() ) {
                        $this->select( $select->getName() );
                    }
                }
            }
            
            // prepare group by
            $groups = & $this->_query->group;
            foreach ( $groups as $n => $group ) {
                if ( $expr = $this->getColumn( $group['expr'], true ) ) {
                    $groups[ $n ]['expr'] = $expr;
                }
            }

            // prepare order by
            $orders = & $this->_query->order;
            foreach ( $orders as $n => $order ) {
                if ( $expr = $this->getColumn( $order['expr'], true ) ) {
                    $orders[ $n ]['expr'] = $expr;
                }
            }

            // prepare where
            if ( $only_keys && ! sizeof( $keys ) ) {
                $only_keys = false;
            }

            if ( $only_keys ) {
                foreach ( $keys as $field ) {
                    if ( ! $this->exists( $field->getName() ) ) {
                        $only_keys = false;
                        break;
                    }
                }
                
            }
            
            if ( ! $update || ( $update && $only_keys ) ) {
            
                if ( $only_keys ) {
                    $fields = $keys;
                }
                
                foreach ( $fields as $field ) {

                    $name = $field->getName();
                    
                    if ( ! $this->exists( $name ) ) {
                        continue;
                    }

                    $value = $this->get( $name );

                    if ( is_null( $value ) ) {
                        $value = ' IS NULL ';
                    } else if ( is_array( $value ) ) {
                        $value_string = ' IN ( ';
                        $first = true;
                        foreach ( $value as $val ) {
                            if ( ! $first ) {
                                $value_string .= ', ';
                            }
                            if ( is_null( $val ) ) {
                                $value_string .= 'NULL';
                            } else {
                                $value_string .= $this->_query->escapeSql( $val );
                            }
                            $first = false;
                        }
                        $value_string .= ' )';
                        $value = $value_string;
                    } else {
                        $value = " = " . $this->_query->escapeSql( $value );
                    }

                    $this->where( $this->getColumn( $name, true ) . $value );
                }
                
            }
            
            // Replace #. with table alias in WHERE, HAVING, SELECT, ORDER and GROUP
            foreach ( array( 'where', 'having', 'select', 'order', 'group' ) as $statement ) {
                $stas = & $this->_query->$statement;
                foreach ( $stas as $n => $sta ) {
                    $expr = $sta['expr'];
                    $expr = str_replace( '#.', $r . $alias . $r . '.', $expr );
                    $stas[ $n ]['expr'] = $expr;
                }
            }
            
        }

        /**
         *  This function loads relations objects into the YDDatabaseObject.
         *  You can add relations names as parameters to load the relations.
         */
        function load() {

            $args = func_get_args();

            if ( ! sizeof( $args ) ) {
                $this->loadAll();
            }
            
            foreach ( $args as $relation ) {
        
                if ( ! $this->_relations->exists( $relation ) ) {
                    trigger_error(  $this->getClassName() . ' -
                                    The relation "' . $relation . '" is not defined.', YD_ERROR );
                }

                $rel     = & $this->getRelation( $relation );
                $f_var   = $rel->getForeignVar();
                $f_class = $rel->getForeignClass();

                if ( ! $this->exists( $f_var ) ) {
                    $this->set( $f_var, YDDatabaseObject::getInstance( $f_class ) );
                    $this->$f_var->_alias = $f_var;
                }

                if ( $rel->isManyToMany() ) {

                    $c_var   = $rel->getCrossVar();
                    $c_class = $rel->getCrossClass();

                    if ( ! $this->exists( $c_var ) ) {
                        $this->set( $c_var, YDDatabaseObject::getInstance( $c_class ) );
                        $this->$c_var->_alias = $c_var;
                    }

                }
            
            }

        }

        /**
         *  This function loads all relations objects into the YDDatabaseObject.
         */
        function loadAll() {

            $relations = array_keys( get_object_vars( $this->_relations ) );

            foreach ( $relations as $relation ) {
                $this->load( $relation );
            }

        }

        /**
         *  This function executes a single query for all relations that meet the conditions
         *  established by the object values.
         *
         *  @returns  The number of rows found.
         */
        function findAll() {

            $relations = array_keys( get_object_vars( $this->_relations ) );

            return call_user_func_array( array ( & $this, 'find' ), $relations );
        
        }

        /**
         *  This function returns all the relation objects values arrays in
         *  a single array.
         *
         *  @param $only_current (optional) Returns only the current object values. Default: false.
         *  @param $only_fields  (optional) Returns only defined fields. Default: false.
         *  @param $columns      (optional) Returns the columns names instead of the fields names.
         *  @param $prefix       (optional) If true all foreign values will be prefixed with
         *                                  the foreign and cross var. Default: true.
         *
         *  @returns  The single array with all values.
         */
        function getValues( $only_current=false, $only_fields=false, $columns=false, $prefix=true ) {

            $results = $this->_getValues( $only_fields, $columns );
            
            if ( ! $only_current ) {
            
                $relations = $this->_last;

                foreach ( $relations as $relation ) {
    
                    $rel = & $this->getRelation( $relation );
    
                    $f_var = $rel->getForeignVar();
                    $values = $this->$f_var->_getValues( $only_fields, $columns, $prefix ? $f_var . '_' : '' );
    
                    $results = array_merge( $results, $values );
    
                    if ( $rel->isManyToMany() ) {
    
                        $c_var = $rel->getCrossVar();
                        $values = $this->$c_var->_getValues( $only_fields, $columns, $prefix ? $c_var . '_' : '' );
    
                        $results = array_merge( $results, $values );
                    }
    
                }

            }

            return $results;

        }

        /**
         *  This function retrieves all the relation results that weren't fetched.
         *
         *  @param $only_current (optional) Returns only the current object results. Default: false.
         *  @param $only_fields  (optional) Returns only defined fields. Default: false.
         *  @param $columns      (optional) Returns the columns names instead of the fields names.
         *  @param $prefix       (optional) Adds the relation's vars as prefixes to the keys. Default: false.
         *
         *  @returns  An array with the results.
         */
        function getResults( $only_current=false, $only_fields=false, $columns=false, $prefix=true ) {
            
            $results = array();
            $relations_arr = array();
            
            $relations_arr['_'] = & $this;
            
            if ( ! $only_current ) {
                
                $relations = $this->_last;

                foreach ( $relations as $relation ) {

                    $rel = & $this->getRelation( $relation );

                    $f_var = $rel->getForeignVar();
                    $relations_arr[ $f_var ] = & $this->$f_var;

                    if ( $rel->isManyToMany() ) {
                        $c_var = $rel->getCrossVar();
                        $relations_arr[ $c_var ] = & $this->$c_var;
                    }

                }

            }
            
            foreach ( $this->_results as $k => $res ) {
                
                $result = array();

                foreach ( $relations_arr as $name => $relation ) {
                    
                    $res = $relation->_results[ $k ];
                    
                    if ( ! sizeof( $res ) ) {
                        continue;
                    }
                    
                    $protected = $relation->_getFieldsByMethod( 'isProtected' );

                    foreach ( $protected as $field ) {
            
                        $p_compar = $field->getComparison();
                        $p_value  = $field->getProtected();
                        
                        $field = $field->getName();
                        
                        switch ( $p_compar ) {
                            
                            case '<':
                                if ( array_key_exists( $field, $res ) && $res[ $field ] >= $p_value ) {
                                    unset( $res[ $field ] );
                                }
                                break;
                            case '<=':
                                if ( array_key_exists( $field, $res ) && $res[ $field ] > $p_value ) {
                                    unset( $res[ $field ] );
                                }
                                break;
                            case '>':
                                if ( array_key_exists( $field, $res ) && $res[ $field ] <= $p_value ) {
                                    unset( $res[ $field ] );
                                }
                                break;
                            case '>=':
                                if ( array_key_exists( $field, $res ) && $res[ $field ] < $p_value ) {
                                    unset( $res[ $field ] );
                                }
                                break;
                            case '<>':
                            case '!=':
                                if ( array_key_exists( $field, $res ) && $res[ $field ] == $p_value ) {
                                    unset( $res[ $field ] );
                                }
                                break;
                            case '=':
                            case '==':
                            default:
                                $res[ $field ] = $p_value;
                                break;
                                
                        }
            
                    }
                    
                    if ( ! sizeof( $res ) ) {
                        continue;
                    }
                    
                    $pfx = '';
                    if ( $prefix ) {
                        $pfx = $name . '_';
                    }
                    
                    if ( $pfx == '_' || $pfx == '__' ) {
                        $pfx = '';
                    }
                    
                    foreach ( $res as $field => $value ) {
                        
                        if ( substr( $field, 0, 1 ) == '_' ) {
                            unset( $res[ $field ] );
                            continue;
                        }
                        
                        if ( $prefix && $pfx != '' ) {
                            $res[ $pfx . $field ] = $value;
                            unset( $res[ $field ] );
                        }
                        
                        if ( $only_fields && ! $this->_fields->exists( $field ) ) {
                            unset( $res[ $pfx . $field ] );
                        }
                        
                        if ( $columns && $this->_fields->exists( $field ) ) {
                            $res[ $pfx . $this->getColumn( $field ) ] = $value;
                            unset( $res[ $pfx . $field ] );
                        }
                        
                    }

                    $result = array_merge( $result, $res );
                    
                }
                
                $results[] = $result;
                
            }
            
            // Clear the results
            foreach ( $relations_arr as $relation ) {
                $relation->resetResults();
            }

            return $results;

        }

        /**
         *  This function resets the information defined in relation objects.
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
                
                if ( isset( $this->$f_var ) ) {
                    $this->$f_var->reset();
                }

                if ( $rel->isManyToMany() ) {

                    $c_var = $rel->getCrossVar();
                    
                    if ( isset( $this->$c_var ) ) {
                        $this->$c_var->reset();
                    }

                }
            }

        }

        /**
         *  This function executes an INSERT query based on the values of the object.
         *
         *  @param $values_noquote  (optional) Associative array that defines values that
         *                          will not be quoted as strings, e.g. NOW().
         *
         *  @returns  The last autoincrement value if any.
         */
        function insert( $values_noquote=array() ) {

            // before insert callbacks
            $res = $this->_executeCallbacks( 'insert', true );
            
            if ( $res !== true && $res !== null ) {
                return $res;
            }

            $values = $this->getValues( true, true, true );

            if ( ! sizeof( $values ) && ! sizeof( $values_noquote ) ) {
                return;
            }
            
            $this->_query->insert();
            $this->_query->into( $this->getTable() );
            $this->_query->values( $values );
            $this->_query->values( $values_noquote, true, false );
            $sql = $this->_query->getQuery();

            YDDebugUtil::debug( YDStringUtil::removeWhiteSpace( $sql ) );
            
            if ( ! YDConfig::get( 'YD_DBOBJECT_EXECUTE' ) ) {
                return $sql;
            }
            
            $result = $this->_db->executeSql( $sql );
            $success = ( $result == -1 || $result === false ) ? false : true;
            
            $auto_field = current( $this->_getFieldsByMethod( 'isAutoIncrement' ) );
            
            if ( is_numeric( $result ) && $auto_field ) {
                $result = $this->_db->getLastInsertID();
                $this->set( $auto_field->getName(), $result );
            }
            
            // after insert callbacks
            $res = $this->_executeCallbacks( 'insert', false, $success );
            
            if ( $res !== true && $res !== null ) {
                return $res;
            }
            
            $this->resetQuery();

            return $result;

        }

        /**
         *  This function executes an UPDATE query based on the values of the object
         *  and any value set by where.
         *
         *  @param $values_noquote  (optional) Associative array that defines values that
         *                          will not be quoted as strings, e.g. NOW().
         *
         *  @returns  The number of rows affected.
         */
        function update( $values_noquote=array() ) {

            // before update callbacks
            $res = $this->_executeCallbacks( 'update', true );
            
            if ( $res !== true && $res !== null ) {
                return $res;
            }
            
            $this->_prepareQuery( true, true );

            $keys   = $this->_getFieldsByMethod( 'isKey' );
            $values = $this->getValues( true, true, true );
            $where  = $this->_query->getWhere( false );
            
            // remove the keys from the values
            if ( sizeof( $keys ) ) {
                foreach ( $keys as $field ) {
                    unset( $values[ $field->getName() ] );
                }
            }
            
            // return if no values to set
            if ( ! sizeof( $values ) && ! sizeof( $values_noquote ) ) {
                return;
            }

            if ( ! strlen( trim( $where ) ) &&  ! YDConfig::get( 'YD_DBOBJECT_UPDATE' ) ) {
                trigger_error(  $this->getClassName() . ' -
                                Your UPDATE query has no conditions and will not be executed.', YD_NOTICE );
                return;
            }
            
            $this->_query->update();
            $this->_query->table( $this->getTable() );
            $this->_query->set( $values );
            $this->_query->set( $values_noquote, true, false );
            $sql = $this->_query->getQuery();

            YDDebugUtil::debug( YDStringUtil::removeWhiteSpace( $sql ) );
            
            if ( ! YDConfig::get( 'YD_DBOBJECT_EXECUTE' ) ) {
                return $sql;
            }
            
            $result = $this->_db->executeSql( $sql );
            $success = ( $result == -1 || $result === false ) ? false : true;
            
            // after update callbacks
            $res = $this->_executeCallbacks( 'update', false, $success );
            
            if ( $res !== true && $res !== null ) {
                return $res;
            }
            
            $this->resetQuery();

            return $this->_count = (int) $result;
        }

        /**
         *  This function executes an DELETE query based on the values of the object
         *  or any condition set by where and order.
         *
         *  @returns  The number of rows affected.
         */
        function delete() {

            // before delete callbacks
            $res = $this->_executeCallbacks( 'delete', true );
            
            if ( $res !== true && $res !== null ) {
                return $res;
            }

            $this->_prepareQuery();
            
            $where = $this->_query->getWhere( false );

            if ( ! strlen( trim( $where ) ) &&  ! YDConfig::get( 'YD_DBOBJECT_DELETE' ) ) {
                trigger_error(  $this->getClassName() . ' -
                                Your DELETE query has no conditions and will not be executed.', YD_NOTICE );
                return;
            }
            
            $this->_query->delete();
            $this->_query->table( $this->getTable() );
            $sql = $this->_query->getQuery();
            
            YDDebugUtil::debug( YDStringUtil::removeWhiteSpace( $sql ) );
            
            if ( ! YDConfig::get( 'YD_DBOBJECT_EXECUTE' ) ) {
                return $sql;
            }
            
            $result = $this->_db->executeSql( $sql );
            $success = ( $result == -1 || $result === false ) ? false : true;

            // after delete callbacks
            $res = $this->_executeCallbacks( 'delete', false, $success );
            
            if ( $res !== true && $res !== null ) {
                return $res;
            }

            $this->resetQuery();

            return $this->_count = (int) $result;

        }

        /**
         *  This function finds the rows that match the object field's values
         *  an any other condition added with where, group, having, etc.
         *
         *  You can add values to the method's parameters to search the relations.
         *
         *  If you want to limit this query, use the setLimit method.
         *
         *  @returns  The number of records found.
         */
        function find() {

            $args = func_get_args();

            $relations = array();
            if ( sizeof( $args ) ) {
                $relations = $args;
            }
            
            if ( empty( $relations ) && $this->_all == false ) {
                $this->_all = true;
            }
            $all = $this->_all;
            
            $this->_last = $relations;
            
            // before find callbacks
            $res = $this->_executeCallbacks( 'find', true );
            
            if ( $res !== true && $res !== null ) {
                return $res;
            }

            $r = $this->_query->getReserved();
            $this->_query->select();
            $this->_query->resetFrom();

            // Add local table
            $l_table = $this->getTable();
            $l_alias = $this->getAlias();
            
            $this->_query->table( $l_table, $l_alias );
            
            // Prepare query in local object
            $this->_prepareQuery( true );
            
            // Add the local slice
            $pos = 0;
            $slices = array();
            $slices[ $pos ] = '';
            $pos = $pos + sizeof( $this->_query->select );
            
            // If there are any relations

            foreach ( $relations as $relation ) {

                // Load relation if not loaded yet
                $this->load( $relation );
                $rel = & $this->getRelation( $relation );

                // Local key
                $l_key = $rel->getLocalKey();
                if ( ! $l_key ) {
                    $l_key = current( $this->_getFieldsByMethod( 'isKey', true ) );
                    $l_key = $l_key->name;
                }
                
                if ( ! $l_column = $this->getColumn( $l_key, true ) ) {
                    $l_column = $l_key;
                }

                // Foreign table
                $f_var   = $rel->getForeignVar();
                $f_table = $this->$f_var->getTable();
                
                if ( $this->$f_var->_all == true ) {
                    $all = true;
                }
                
                // Foreign table alias
                $this->$f_var->_alias = $f_var;
                $f_alias = $f_var;
                
                // Foreign table key
                $f_key   = $rel->getForeignKey();

                if ( ! $f_key ) {
                    $f_key = current( $this->$f_var->_getFieldsByMethod( 'isKey', true ) );
                    $f_key = $f_key->name;
                }
                
                if ( ! $f_column = $this->$f_var->getColumn( $f_key, true ) ) {
                    $f_column = $f_key;
                }
                
                // Prepare the query in the foreign object
                $this->$f_var->_prepareQuery( true );

                // Add the foreign slice
                $slices[ $pos ] = $f_var;
                $pos = $pos + sizeof( $this->$f_var->_query->select );

                // Merge current selects with foreign selects
                $this->_query->select = array_merge( $this->_query->select, $this->$f_var->_query->select );

                // Not many to many
                if ( ! $rel->isManyToMany() ) {

                    // Join foreign table
                    $this->_query->join( $rel->getForeignJoin(), $f_table, $f_alias );
                    //$this->_query->on( $r . $l_alias . $r . '.' . $r . $l_column . $r . ' = ' .
                    //                   $r . $f_alias . $r . '.' . $r . $f_column . $r );
                    $this->_query->on( $l_column . ' = ' . $f_column );

                } else {

                    // Many to many relation
                    
                    // Cross table
                    $c_var   = $rel->getCrossVar();
                    $c_table = $this->$c_var->getTable();
                    
                    if ( $this->$c_var->_all == true ) {
                        $all = true;
                    }
                    
                    // Cross table alias
                    $this->$c_var->_alias = $c_var;
                    $c_alias = $c_var;

                    // Default cross foreign and local key
                    $c_fkey  = $f_table . '_' . $f_key;
                    $c_lkey  = $l_table . '_' . $l_key;

                    // User-defined cross foreign key
                    if ( $rel->getCrossForeignKey() ) {
                        $c_fkey = $rel->getCrossForeignKey();
                    }
                    
                    // User-defined cross local key
                    if ( $rel->getCrossLocalKey() ) {
                        $c_lkey = $rel->getCrossLocalKey();
                    }
                    
                    // Cross foreign field column name
                    if ( ! $c_fcolumn = $this->$c_var->getColumn( $c_fkey, true ) ) {
                        $c_fcolumn = $c_fkey;
                    }
                    
                    // Cross local field column name
                    if ( ! $c_lcolumn = $this->$c_var->getColumn( $c_lkey, true ) ) {
                        $c_lcolumn = $c_lkey;
                    }

                    // Prepare the query in the cross object
                    $this->$c_var->_prepareQuery( true );

                    // Add the cross slice
                    $slices[ $pos ] = $c_var;
                    $pos = $pos + sizeof( $this->$c_var->_query->select );

                    // Merge current selects with cross selects
                    $this->_query->select = array_merge( $this->_query->select, $this->$c_var->_query->select );

                    // Join cross table
                    $this->_query->join( $rel->getCrossJoin(), $c_table, $c_alias );
                    //$this->_query->on( $r . $l_alias . $r . '.' . $r . $l_column  . $r . ' = ' .
                    //                   $r . $c_alias . $r . '.' . $r . $c_lcolumn . $r );
                    $this->_query->on( $l_column . ' = ' . $c_lcolumn );
                    
                    // Cross table additional conditions
                    if ( $where = $rel->getCrossConditions() ) {
                        $this->_query->on( $where );
                    }
                    if ( $where = $this->$c_var->_query->getWhere( false ) ) {
                        $this->_query->on( $where );
                    }

                    // Join foreign table
                    $this->_query->join( $rel->getForeignJoin(), $f_table, $f_alias );
                    //$this->_query->on( $r . $c_alias . $r . '.' . $r . $c_fcolumn . $r . ' = ' .
                    //                   $r . $f_alias . $r . '.' . $r . $f_column  . $r );
                    $this->_query->on( $c_fcolumn . ' = ' . $f_column );
                    
                }

                // Foreign table additional conditions
                if ( $where = $rel->getForeignConditions() ) {
                    $this->_query->on( $where );
                }
                if ( $where = $this->$f_var->_query->getWhere( false ) ) {
                    $this->_query->on( $where );
                }

                // Additional statements
                if ( $where = $rel->getWhere() ) {
                    $this->_query->where( $where );
                }
                if ( $group = $rel->getGroup() ) {
                    $this->_query->group( $group );
                }
                if ( $having = $rel->getHaving() ) {
                    $this->_query->having( $having );
                }
                if ( $order = $rel->getOrder() ) {
                    $this->_query->order( $order );
                }

            }
            
            if ( $all == false ) {
                trigger_error(  $this->getClassName() . ' -
                                The SELECT statement is empty', YD_ERROR );
            }
            
            if ( ! YDConfig::get( 'YD_DBOBJECT_EXECUTE' ) ) {
                return $this->_query->getQuery();
            }
            
            $result = $this->findSql( $this->_query->getQuery(), $slices );
            $success = ( $result == -1 || $result === false ) ? false : true;
            
            // after find callbacks
            $res = $this->_executeCallbacks( 'find', false, $success );
            
            if ( $res !== true && $res !== null ) {
                return $res;
            }
            
            return $result;

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

            $results = $this->_db->getRecords( $sql );
            
            YDConfig::set( 'YD_DB_FETCHTYPE', $fetch );

            if ( ! sizeof( $slices ) ) {
                $slices = array( 0 => '' );
            }

            reset( $slices );

            $var = current( $slices );
            
            if ( $var != '' ) {
            
                $this->resetResults();
                $this->_count = $results ? sizeof( $results ) : 0;
                
                if ( $this->_count > 0 ) {
                    $this->_results = array_fill( 0, $this->_count, array() );
                }
                
                $this->resetQuery();

                if ( $this->_count == 1 ) {
                    $this->setValues( $this->_results[0] );
                }
                
            }

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

                $select = $obj->_query->select;

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
         *  This function returns an associative array with the
         *  modified fields and it's old and new values.
         *
         *  @param $exclude  (optional) Array with fields to exclude from the comparison.
         *
         *  @returns  An array with the modified fields and the old and new values.
         */
        function getModified( $exclude=array() ) {
        
            $all = get_object_vars( $this->_fields );
            
            $fields = array();
            $keys = array();
			foreach ( $all as $field ) {
            
                $name = $field->getName();
                
				if ( $field->isKey() ) {
                    if ( ! array_key_exists( $name, $this ) || ! strlen( $this->$name ) ) {
    					return false;
    				}
                    $keys[] = $name;
                    continue;
                }
                
                if ( ! in_array( $name, $exclude ) ) {
                    $fields[] = $name;
                }
                
			}
            
            $obj = $this->getInstance();
            foreach ( $keys as $key ) {
    			$obj->$key = $this->$key;
            }
    		$obj->find();
    			
			if ( $obj->count() != 1 ) {
				return false;
			}
			
			$old = $obj->getValuesAsAssocArray( $fields );
			$new = $this->getValuesAsAssocArray( $fields );
            
            $modified = array();
			foreach ( $new as $field => $value ) {
				if ( array_key_exists( $field, $old ) && $old[ $field ] == $value ) {
					continue;
				} 
				$modified[ $field ] = array( 'old' => $old[ $field ], 'new' => $new[ $field ] );
			}
			
			return $modified;
        
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
         *  Adds a select field to the query. The registered fields and selects can
         *  be added at each parameter of the method.
         */
        function select() {

            $args = func_get_args();

            if ( ! sizeof( $args ) ) {
                $this->_query->resetSelect();
                $this->_all = false;
                return;
            }
            
            $this->_all = true;

            foreach ( $args as $field ) {
            
                if ( $field == '*' ) {
                     $this->_query->resetSelect();
                     continue;
                }

                if ( ! $this->_fields->exists( $field ) && ! $this->_selects->exists( $field ) ) {
                    trigger_error(  $this->getClassName() . ' -
                                    Field "' . $field . '" is not registered.', YD_ERROR );
                }
                if ( $select = & $this->getSelect( $field ) ) {
                    $expr = $select->getExpression();
                } else {
                    $expr = $this->getColumn( $field, true );
                }
                $this->_query->expr( $expr, $field );
            }

        }

        /**
         *  Adds a condition to the WHERE clause.
         *
         *  @param $expr   The condition expression.
         *  @param $logic  (optional) Logic operator (e.g. AND, OR, XOR). Default: AND.
         */
        function where( $expr, $logic='AND' ) {
            $this->_query->where( $expr, $logic );
        }

        /**
         *  Adds an expression to the GROUP BY clause.
         *
         *  @param $expr     The column name.
         *  @param $desc     (optional) If true adds a DESC string to the expression. Default: false.
         *  @param $reserved (optional) Indicates if the expression is a reserved keyword. Default: false.
         */
        function group( $expr, $desc=false, $reserved=false ) {
            $this->_query->group( $expr, $desc, $reserved );
        }

        /**
         *  Alias of group. Adds an expression to the GROUP BY clause.
         *
         *  @param $expr     The column name.
         *  @param $desc     (optional) If true adds a DESC string to the column. Default: false.
         *  @param $reserved (optional) Indicates if the expression is a reserved keyword. Default: false.
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
            $this->_query->having( $expr, $logic );
        }

        /**
         *  Adds a column to the ORDER BY clause.
         *
         *  @param $expr     Column name.
         *  @param $desc     (optional) If true adds a DESC string to the column. Default: false.
         *  @param $reserved (optional) Indicates if the expression is a reserved keyword. Default: false.
         */
        function order( $expr, $desc=false, $reserved=false ) {
            $this->_query->order( $expr, $desc, $reserved );
        }

        /**
         *  Alias of order. Adds a column to the ORDER BY clause.
         *
         *  @param $expr     Column name.
         *  @param $desc     (optional) If true adds a DESC string to the column. Default: false.
         *  @param $reserved (optional) Indicates if the expression is a reserved keyword. Default: false.
         */
        function orderby( $expr, $desc=false, $reserved=false ) {
            $this->order( $expr, $desc, $reserved );
        }

        /**
         *  This function sets the limit for the queries.
         *
         *  @param $limit  (optional) The limit number;
         */
        function limit( $limit=-1 ) {
            $this->_query->limit( $limit );
        }

        /**
         *  This function sets the offset for SELECT queries only.
         *
         *  @param $offset  (optional) The offset.
         */
        function offset( $offset=-1 ) {
            $this->_query->offset( $offset );
        }

        /**
         *  This function fetch the results and adds it's values to the object.
         *
         *  @param $only_current (optional) Fetch only the current object values. Default: false.
         *  
         *  @returns  True if any result has been fetched, otherwise false.
         */
        function fetch( $only_current=false ) {
            
            if ( ! $only_current ) {
                
                $relations = $this->_last;
                
                foreach ( $relations as $relation ) {
                    
                    $rel = & $this->getRelation( $relation );
    
                    $f_var = $rel->getForeignVar();
                    $this->$f_var->fetch( true );
                    
                    if ( $rel->isManyToMany() ) {
                        
                        $c_var = $rel->getCrossVar();
                        $this->$c_var->fetch( true );
    
                    }
    
                }
                
            }
            
            $res = array_shift( $this->_results );
            if ( ! is_null( $res ) ) {
                $this->setValues( $res );
                return true;
            }
            return false;

        }

        /**
         *  This function resets all information in the object and relation objects.
         */
        function resetAll() {
            $this->reset( true );
        }

        /**
         *  This function resets the object's values, any additional query value and
         *  any result remaining to be fetch.
         */
        function reset( $all=false ) {
            
            // before reset callbacks
            $res = $this->_executeCallbacks( 'reset', true );
            
            if ( $res !== true && $res !== null ) {
                return $res;
            }
            
			if ( $all ) {
				$this->resetObject();
				$this->resetRelations();
			} else {
				$this->resetValues();
			}
            $this->resetQuery();
            $this->resetResults();
            $this->resetCount();
            
            // after reset callbacks
            $res = $this->_executeCallbacks( 'reset', false );
            
            if ( $res !== true && $res !== null ) {
                return $res;
            }
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
            $this->_count = 0;
        }

        /**
         *  This function resets the object's field values.
         */
        function resetValues() {
            
			$this->setValues( array() );
			
			$selects = array_keys( get_object_vars( $this->_selects ) );

            foreach ( $selects as $select ) {
                unset( $this->$select );
            }
			
        }
        
        /**
         *  This function resets the object.
         */
        function resetObject() {
            
            $relation_vars = array();
			foreach ( $this->_relations as $rel ) {
				$relation_vars[] = $rel->getForeignVar();
				if ( $rel->isManyToMany() ) {
					$relation_vars[] = $rel->getCrossVar();
				}
			}
			
			$vars = get_object_vars( $this );
            
            foreach ( $vars as $var => $value ) {
                
                if ( substr( $var, 0, 1 ) != '_' && ! in_array( $var, $relation_vars ) ) {
                    unset( $this->$var );
                }
                
            }
            
        }
		
		/**
         *  This function unloads all relations.
         */
		function resetRelations() {
			
			$relations = array_keys( get_object_vars( $this->_relations ) );

            foreach ( $relations as $relation ) {
                $this->resetRelation( $relation );
            }
			
		}

        /**
         *  This function resets any query addition.
         */
        function resetQuery() {
            $this->_query->reset();
            $this->_all = true;
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
        function _getValues( $only_fields=false, $columns=false, $prefix='' ) {

            $this->resetProtected();
            
            $values = get_object_vars( $this );
            
            $new = array();
            foreach ( $values as $field => $value ) {
                if ( $this->exists( $field ) && ! is_object( $this->$field ) && substr( $field, 0, 1 ) != '_' ) {
                    if ( ! $only_fields  || ( $only_fields && $this->_fields->exists( $field ) ) ) {
                        $key = $prefix . $field;
                        if ( $columns && $this->_fields->exists( $field ) ) {
                            $key = $prefix . $this->getColumn( $field );
                        }
                        $new[ $key ] = $value;
                    }
                }
            }
            
            return $new;

        }

        /**
         *  This function retrieves an associative array of values of the 
         *  indicated fields.
         *
         *  @param $val          (optional) The fields to retrieve. Default: all.
         *  @param $only_current (optional) Returns only the current object values. Default: false.
         *  @param $only_fields  (optional) Returns only defined fields. Default: false.
         *  @param $columns      (optional) Returns the columns names instead of the fields names.
         *  @param $prefix       (optional) If true all foreign values will be prefixed with
         *                                  the foreign and cross var. Default: true.
         *
         *  @returns  An associative array with the values.
         */
        function getValuesAsAssocArray( $val=array(), $only_current=false, $only_fields=false, $columns=false, $prefix=true ) {
            
            $values = $this->getValues( $only_current, $only_fields, $columns, $prefix );
            $output = array();
            
            if ( empty( $val ) ) {
                $val = array_keys( $values );
            }
            
            if ( ! is_array( $val ) ) {
                $val = array( $val );
            }
            
            foreach ( $val as $v ) {
                if ( array_key_exists( $v, $values ) ) {
                    $output[ $v ] = $values[ $v ];
                }
            }
            
            return $output;
            
        }
        
        /**
         *  This function retrieves all the results that weren't fetched as an
         *  associative array using the indicated fields for keys and values
         *
         *  @param $key          (optional) The fields to use for the keys. Default: all keys.
         *  @param $val          (optional) The fields to use for the values. Default: all fields.
         *  @param $only_current (optional) Returns only the current object values. Default: false.
         *  @param $only_fields  (optional) Returns only defined fields. Default: false.
         *  @param $columns      (optional) Returns the columns names instead of the fields names.
         *  @param $prefix       (optional) If true all foreign values will be prefixed with
         *                                  the foreign and cross var. Default: true.
         *
         *  @returns  An associative array with the results.
         */
        function getResultsAsAssocArray( $key=array(), $val=array(), $only_current=false, $only_fields=false, $columns=false, $prefix=true ) {

            if ( ! is_array( $val ) ) {
                if ( empty( $val ) ) {
                    $val = array();
                } else {
                    $val = array( $val );
                }
            }
            
            if ( empty( $key ) ) {
                $key = $this->_getFieldsByMethod( 'isKey', false );
            }
            
            if ( ! is_array( $key ) ) {
                $key = array( $key );
            }
            
            $results = $this->getResults( $only_current, $only_fields, $columns, $prefix );
            $output = array();
            $curr = null;
            
            foreach ( $results as $res ) {
                
                $curr = & $output;
                
                if ( ! empty( $key ) ) {
                    foreach ( $key as $field_key ) {
                        
                        if ( ! array_key_exists( $field_key, $res ) ) {
                            trigger_error(  $this->getClassName() . ' -
                                            "' . $field_key . '" is not defined in the results array', YD_ERROR );
                        } else {
                            if ( ! array_key_exists( $res[ $field_key ], $curr ) ) {
                                $curr[ $res[ $field_key ] ] = array();
                            }
                            $curr = & $curr[ $res[ $field_key ] ];
                            // unset( $res[ $field_key ] );
                        }
                    }
                } else {
                    $curr = & $curr[];
                }
                
                if ( empty( $val ) ) {
                    if ( sizeof( $res ) ) {
                        foreach ( $res as $field => $field_val ) {
                            $curr[ $field ] = $field_val;
                        }
                    } else {
                        $curr = array();
                    }
                } else if ( sizeof( $val ) == 1 ) {
                    if ( array_key_exists( $val[0], $res ) ) {
                        $curr = $res[ $val[0] ];
                    }
                } else {
                    foreach ( $val as $field_val ) {
                        if ( array_key_exists( $field_val, $res ) ) {
                            $curr[ $field_val ] = $res[ $field_val ];
                        }
                    }
                }
                
            }

            return $output;

        }

        /**
         *  This function receives and adds an associative array of values to
         *  the object. The array keys are the Table fields.
         *
         *  @param $values  The associative array of values.
         */
        function setValues( $values ) {

            foreach ( get_object_vars( $this ) as $field => $val ) {
                if ( $this->_fields->exists( $field ) ) {
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
        
        /**
         *  This function executes all callbacks defined for the action.
         *
         *  @param  $action  The action name.
         *  @param  $before  (optional) Execute the before actions. Default: false.
         *  @param  $success (optional) Boolean indicating if the action was successful. Default: null - no result.
         *
         *  @internal
         */
        function _executeCallbacks( $action, $before=false, $success=null ) {
            
            if ( ! $this->_callbacks->exists( strtolower( $action ) ) ) {
                trigger_error(  $this->getClassName() . ' -
                                Incorrect action in _executeCallbacks.', YD_ERROR );
            }
            
            $sub = $before ? 'before' : 'after';
            $actions = & $this->_callbacks->$action;
            
            foreach ( $actions[ $sub ] as $callback => $n ) {
                
                if ( ! $this->hasMethod( $callback ) ) {
                    trigger_error(  $this->getClassName() . ' -
                                    The ' . $action . ' callback method "' . $callback . '" is not defined.', YD_ERROR );
                }
                $res = call_user_func( array( & $this, $callback ), $action, $before, $success );
                
                if ( $res !== true && $res !== null ) {
                    return array( 
                        'class'    => $this->getClassName(),
                        'callback' => $callback,
                        'action'   => $action,
                        'before'   => $before,
                        'success'  => $success,
                        'return'   => $res
                    );
                }
                
            }
            
            return true;
            
        }
        
        /**
         *  This function indicates if an SQL error should die the script or not.
         *
         *  @param $val     (optional) True if you want the script to die on an error, false otherwise.
         */
        function setFailOnError( $val=true ) { 
            $this->_db->setFailOnError( $val );
        }

    }

    /**
     *  This class defines a YDDatabaseObject_Field object.
     */
    class YDDatabaseObject_Field extends YDBase {

        var $name;
        var $column;
        var $null;
        var $auto;
        var $key;
        var $protected;
        var $value;
        var $callback;
        var $comparison;

        /**
         *  The class constructor.
         *
         *  @param $name     The field name.
         *  @param $key      (optional) Is a key? Default: false.
         *  @param $auto     (optional) Is auto-increment? Default: false.
         *  @param $null     (optional) Can be null? Default: false.
         */
        function YDDatabaseObject_Field( $name, $key=false, $auto=false, $null=false ) {

            $this->YDBase();

            $this->setName( $name );

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
            $this->set( 'protected', true );
            $this->set( 'value', $value );
        }

        /**
         *  This function unsets a field as protected.
         */
        function unsetProtected() {
            $is = $this->isProtected();
            $this->set( 'protected', false );

            if ( $is ) {
                unset( $this->value );
                unset( $this->comparison );
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
            return $this->get( 'value' );
        }

        /**
         *  This function sets the comparison keyword.
         *
         *  @param $keyword  (optional) The comparison keyword. Default: =
         */
        function setComparison( $keyword='=' ) {
            $this->set( 'comparison', $keyword );
        }
        
        /**
         *  @returns  The comparison keyword.
         */
        function getComparison() {
            return $this->get( 'comparison' );
        }
        
        /**
         *  This function unsets the comparison keyword.
         */
        function unsetComparison() {
            $this->set( 'comparison', '=' );
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
    class YDDatabaseObject_Relation extends YDBase {

        var $name;
        var $manytomany;

        var $local;
        var $foreign;
        var $cross;
        
        var $foreign_obj = null;
        var $cross_obj = null;

        /**
         *  The class constructor.
         *
         *  @param $name           The relation name.
         *  @param $local_class    The local class name.
         *  @param $manytomany     (optional) Is a many-to-many relationship? Default: false.
         *  @param $foreign_class  (optional) The foreign class name.
         *                         If empty, the $name parameter will be used.
         *  @param $cross_class    (optional) For many-to-many relations, the cross class name.
         *                         If empty, $local_class . "_" . $foreign_class will be used.
         */
        function YDDatabaseObject_Relation( $name, $local_class, $manytomany=false, $foreign_class='', $cross_class='' ) {

            $this->YDBase();

            $this->local   = new YDBase();
            $this->foreign = new YDBase();
            $this->cross   = new YDBase();

            $this->setName( $name );
            $this->setLocalClass( $local_class );

            if ( $manytomany ) { $this->setManyToMany(); }

            if ( strlen( $foreign_class ) ) { $this->setForeignClass( $foreign_class ); }
            if ( strlen( $cross_class   ) ) { $this->setCrossClass( $cross_class ); }

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
         *  This function set the local class name.
         *
         *  @param $name  The class name.
         */
        function setLocalClass( $name ) {
            $this->local->set( 'class', $name );
        }

        /**
         *  @returns  The local class name.
         */
        function getLocalClass() {
            $this->local->get( 'class' );
        }

        /**
         *  This function set the local key.
         *
         *  @param $name  The field name.
         */
        function setLocalKey( $name ) {
            $this->local->set( 'key', $name );
        }

        /**
         *  @returns  The local key.
         */
        function getLocalKey() {
            return $this->local->get( 'key' );
        }

        /**
         *  @returns  If exists a foreign class defined.
         */
        function hasForeignClass() {
            return $this->foreign->exists( 'class' );
        }

        /**
         *  This function set the foreign class.
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
            if ( $class = $this->foreign->get( 'class' ) ) {
                return $class;
            }
            return $this->getName();
        }

        /**
         *  @returns  The foreign table name.
         */
        function getForeignTable() {
            
            if ( ! isset( $this->foreign_obj ) ) {
                $this->foreign_obj = YDDatabaseObject::getInstance( $this->getForeignClass() );
            }
            return $this->foreign_obj->getTable();
        
        }
        
        /**
         *  @returns  The foreign table alias name.
         */
        function getForeignAlias() {
            return $this->getForeignVar();
        
        }
        
        /**
         *  @returns  A reference to the foreign class instance.
         */
        function & getForeignInstance() {
            
            if ( ! isset( $this->foreign_obj ) ) {
                $this->foreign_obj = YDDatabaseObject::getInstance( $this->getForeignClass() );
            }
            return $this->foreign_obj;
        
        }
        
        /**
         *  This function returns a reference to a foreign field object.
         *
         *  @param  $field  The field name.
         *
         *  @returns  A reference a the foreign field.
         */
        function & getForeignField( $field ) {
            
            if ( ! isset( $this->foreign_obj ) ) {
                $this->foreign_obj = YDDatabaseObject::getInstance( $this->getForeignClass() );
            }
            return $this->foreign_obj->getField( $field );
        
        }
        
        /**
         *  This function returns the column name of a defined field.
         *
         *  @param $field  The field name.
         *  @param $alias  (optional) If true, returns the table alias 
         *                 with the column name. Default: false.
         *
         *  @returns  The column name with or without the table alias.
         */
        function getForeignColumn( $field, $alias=false ) {
            
            if ( ! isset( $this->foreign_obj ) ) {
                $this->foreign_obj = YDDatabaseObject::getInstance( $this->getForeignClass() );
            }
            return $this->foreign_obj->getColumn( $field, $alias );
        
        }
        
        /**
         *  This function set the foreign field name.
         *
         *  @param $name  The field name.
         */
        function setForeignKey( $name ) {
            $this->foreign->set( 'key', $name );
        }

        /**
         *  @returns  The foreign field name.
         */
        function getForeignKey() {
            return $this->foreign->get( 'key' );
        }

        /**
         *  This function set the foreign join type.
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
         *  This function set the foreign var.
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
         *  This function set the foreign conditions.
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
         *  This function set the cross class.
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
            if ( $cross_class = $this->cross->get( 'class' ) ) {
                return $cross_class;
            }
            return $this->getLocalClass() . '_' . $this->getForeignClass();
        }

        /**
         *  @returns  The cross table name.
         */
        function getCrossTable() {
            
            if ( ! isset( $this->cross_obj ) ) {
                $this->cross_obj = YDDatabaseObject::getInstance( $this->getCrossClass() );
            }
            return $this->cross_obj->getTable();
        
        }
        
        /**
         *  @returns  The cross table alias name.
         */
        function getCrossAlias() {
            return $this->getCrossVar();
        
        }
        
        /**
         *  @returns  A reference to the cross class instance.
         */
        function & getCrossInstance() {
            
            if ( ! isset( $this->cross_obj ) ) {
                $this->cross_obj = YDDatabaseObject::getInstance( $this->getCrossClass() );
            }
            return $this->cross_obj;
        
        }

        /**
         *  This function returns a reference to a cross field object.
         *
         *  @param  $field  The field name.
         *
         *  @returns  A reference a the cross field.
         */
        function & getCrossField( $field ) {
            
            if ( ! isset( $this->cross_obj ) ) {
                $this->cross_obj = YDDatabaseObject::getInstance( $this->getCrossClass() );
            }
            return $this->cross_obj->getField( $field );
        
        }
        
        /**
         *  This function returns the column name of a defined field.
         *
         *  @param $field  The field name.
         *  @param $alias  (optional) If true, returns the table alias 
         *                 with the column name. Default: false.
         *
         *  @returns  The column name with or without the table alias.
         */
        function getCrossColumn( $field, $alias=false ) {
            
            if ( ! isset( $this->cross_obj ) ) {
                $this->cross_obj = YDDatabaseObject::getInstance( $this->getCrossClass() );
            }
            return $this->cross_obj->getColumn( $field, $alias );
        
        }
        
        /**
         *  This function set the cross local key.
         *
         *  @param $name  The field name.
         */
        function setCrossLocalKey( $name ) {
            $this->cross->set( 'local_key', $name );
        }

        /**
         *  @returns  The cross local key name.
         */
        function getCrossLocalKey() {
            return $this->cross->get( 'local_key' );
        }

        /**
         *  This function set the cross foreign key.
         *
         *  @param $name  The field name.
         */
        function setCrossForeignKey( $name ) {
            $this->cross->set( 'foreign_key', $name );
        }

        /**
         *  @returns  The cross foreign key name.
         */
        function getCrossForeignKey() {
            return $this->cross->get( 'foreign_key' );
        }

        /**
         *  This function set the cross join type.
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
         *  This function set the cross variable.
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
         *  This function set the cross conditions.
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
         *  This function set the relation WHERE clause.
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
         *  This function set the relation GROUP BY clause.
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
         *  This function set the relation HAVING clause.
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
         *  This function set the relation ORDER BY clause.
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
    class YDDatabaseObject_Select extends YDBase {

        var $name;
        var $expr;
        var $all;
        var $reserved;
        var $callback;

        /**
         *  The class constructor.
         *
         *  @param $name  The select name.
         *  @param $expr  The select expression.
         *  @param $all   (optional) Indicates if the select should be added in all queries. Default: true.
         */
        function YDDatabaseObject_Select( $name, $expr, $all=true ) {

            $this->YDBase();

            $this->setName( $name );
            $this->setExpression( $expr );
            $this->setAllQueries( $all );

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
         *  This function defines if the select should be added in all queries.
         *
         *  @param $all  Boolean indicating if the select should be added in all queries.
         */
        function setAllQueries( $all ) {
            $this->set( 'all', $all );
        }

        /**
         *  @returns  True, if the select should be added in all queries, false otherwise.
         */
        function isAllQueries() {
            return $this->get( 'all' );
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