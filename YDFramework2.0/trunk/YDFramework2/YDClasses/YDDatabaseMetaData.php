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
     *  This class allows you to query the internal metadata from a database. It allows you to list tables, fields and
     *  indexes for all supported database engines in the Yellow Duck Framework.
     */
    class YDDatabaseMetaData extends YDBase {

        /**
         *  Constructor. Set the database connection
         *
         *  @param $db             The YDDatabase object pointing to the database
         */
        function YDDatabaseMetaData( $db=null ) {
            if ( is_null( $db ) ) {
                $this->db = YDDatabase::getNamedInstance();
            } else {
                $this->db = $db;
            }
        }

        /**
         *  This function lists the tables available for the current user in the database.
         *
         *  @returns The names of the tables as an array.
         */
        function getTables() {

            // Start with an empty array
            $tables = array();

            // Check the database type
            switch ( $this->db->getDriver() ) {

                // MySQL
                case 'mysql':
                    $sql = 'show tables from ' . $this->db->getDatabase();
                    $tables = $this->db->getValuesByName( $sql, 'tables_in_' . $this->db->getDatabase() );
                    break;

                // PostgreSQL
                case 'postgres':
                    $sql = 'SELECT c.relname FROM pg_catalog.pg_class c LEFT JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace '
                         . 'WHERE c.relkind IN (\'r\',\'\') AND n.nspname NOT IN (\'pg_catalog\', \'pg_toast\') '
                         . 'AND pg_catalog.pg_table_is_visible(c.oid)';
                    $tables = $this->db->getValuesByName( $sql, 'relname' );
                    break;

                // Oracle
                case 'oracle':
                    $sql = 'select lower( table_name ) as table_name from user_tables';
                    $tables = $this->db->getValuesByName( $sql, 'table_name' );
                    break;

                // SQLite
                case 'sqlite':
                    $sql = 'select lower( name ) as name from sqlite_master where type = \'table\'';
                    $tables = $this->db->getValuesByName( $sql, 'name' );
                    break;

                // Default
                default:
                    trigger_error( $this->db->getDriver() . ' is not a supported database driver.', YD_ERROR );

            }

            // Convert the table names to lowercase
            $tables = array_map( 'strtolower', $tables );

            // Return the list of tables
            return $tables;

        }

        /**
         *  This function lists the fields available in the specified table.
         *
         *  @param $table   The name of the table.
         *
         *  @returns The names of the fields in the table as an array.
         */
        function getFields( $table ) {

            // Add the prefix to the table
            $table = $this->_prefixTable( $table );

            // Start with an empty array
            $fields = array();

            // Check the database type
            switch ( $this->db->getDriver() ) {

                // MySQL
                case 'mysql':
                    $sql = 'SHOW FIELDS FROM ' . $table;
                    $fields = $this->db->getValuesByName( $sql, 'field' );
                    break;

                // PostgreSQL
                case 'postgres':
                    $sql = 'SELECT a.attname FROM pg_catalog.pg_attribute a '
                         . 'LEFT JOIN pg_catalog.pg_class c ON a.attrelid = c.oid '
                         . 'WHERE c.relname = lower( \'' . $table . '\') AND a.attnum > 0 AND NOT a.attisdropped;';
                    $fields = $this->db->getValuesByName( $sql, 'attname' );
                    break;

                // Oracle
                case 'oracle':
                    $sql = 'select lower( column_name ) as column_name from sys.dba_tab_columns where table_name = upper( \'' . $table . '\' )';
                    $fields = $this->db->getValuesByName( $sql, 'column_name' );
                    break;

                // SQLite
                case 'sqlite':
                    $sql = 'PRAGMA table_info( \'' . $table . '\' )';
                    $fields = $this->db->getValuesByName( $sql, 'name' );
                    $fields = array_map( 'strtolower', $fields );
                    break;

                // Default
                default:
                    trigger_error( $this->db->getDriver() . ' is not a supported database driver.', YD_ERROR );

            }

            // Convert the fields to lowercase
            $fields = array_map( 'strtolower', $fields );

            // Return the list of tables
            return $fields;

        }

        /**
         *  This function lists the indexes available in the specified table.
         *
         *  @param $table   The name of the table.
         *
         *  @returns The names of the indexes in the table as an array.
         */
        function getIndexes( $table ) {

            // Add the prefix to the table
            $table = $this->_prefixTable( $table );

            // Start with an empty array
            $indexes = array();

            // Check the database type
            switch ( $this->db->getDriver() ) {

                // MySQL
                case 'mysql':
                    $sql = 'SHOW KEYS FROM ' . $table;
                    $indexes = $this->db->getValuesByName( $sql, 'key_name' );
                    break;

                // PostgreSQL
                case 'postgres':
                    $sql = 'select c.relname from pg_catalog.pg_class c '
                         . 'LEFT JOIN pg_catalog.pg_roles r ON r.oid = c.relowner '
                         . 'LEFT JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace '
                         . 'LEFT JOIN pg_catalog.pg_index i ON i.indexrelid = c.oid '
                         . 'LEFT JOIN pg_catalog.pg_class c2 ON i.indrelid = c2.oid '
                         . 'WHERE c.relkind IN (\'i\',\'\') '
                         . 'AND n.nspname NOT IN (\'pg_catalog\', \'pg_toast\') '
                         . 'AND pg_catalog.pg_table_is_visible(c.oid) '
                         . 'and c2.relname = lower( \'' . $table . '\' )';
                    $indexes = $this->db->getValuesByName( $sql, 'relname' );
                    break;

                // Oracle
                case 'oracle':
                    $sql = 'select lower( index_name ) as index_name from user_indexes where table_name = upper( \'' . $table . '\' )';
                    $indexes = $this->db->getValuesByName( $sql, 'index_name' );
                    break;

                // SQLite
                case 'sqlite':
                    $sql = 'select lower( name ) as name from sqlite_master where type = \'index\'';
                    $indexes = $this->db->getValuesByName( $sql, 'name' );
                    break;

                // Default
                default:
                    trigger_error( $this->db->getDriver() . ' is not a supported database driver.', YD_ERROR );

            }

            // Convert the list of indexes to lowercase
            $indexes = array_map( 'strtolower', $indexes );

            // Return the list of tables
            return $indexes;

        }

        /**
         *  Checks if the specified table exists or not.
         *
         *  @param  $table  Name of the table
         *
         *  @return Boolean indicating if the table exists or not.
         */
        function tableExists( $table ) {
            $table = strtolower( $this->_prefixTable( $table ) );
            $tables = $this->getTables();
            return in_array( $table, $tables );
        }

        /**
         *  Internal function to update the table name with the prefix
         *
         *  @param $table   The name of the table
         *
         *  @returns The table name with the prefix.
         *
         *  @internal
         */
        function _prefixTable( $table ) {
            return str_replace( '#_', YDConfig::get( 'YD_DB_TABLEPREFIX', '' ), $table );
        }

    }

?>