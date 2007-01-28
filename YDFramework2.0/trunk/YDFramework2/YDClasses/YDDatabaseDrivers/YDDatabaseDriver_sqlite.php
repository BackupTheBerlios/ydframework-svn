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
    include_once( YD_DIR_HOME_CLS . '/YDDatabase.php');

    /**
     *	This class defines a database driver for SQLite.
     *
     *  @ingroup YDDatabase
     */
    class YDDatabaseDriver_sqlite extends YDDatabaseDriver {

        /**
         *	This is the class constructor for the YDDatabaseDriver_sqlite class.
         *
         *	@param $db		Name of the database.
         *	@param $user	(optional) User name to use for the connection.
         *	@param $pass	(optional) Password to use for the connection.
         *	@param $host	(optional) Host name to use for the connection.
         *	@param $host	(optional) Host name to use for the connection.
         *	@param $options	(optional) Options to pass to the driver.
         */
        function YDDatabaseDriver_sqlite( $db, $user='', $pass='', $host='', $options=array() ) {
            $this->YDDatabaseDriver( $db,  $user, $pass, $host, $options );
            $this->_driver = 'sqlite';
        }

        /**
         *	This function will check if the server supports this database type.
         *
         *	@returns	Boolean indicating if the database type is supported by the server.
         */
        function isSupported() {
            return extension_loaded( 'sqlite' );
        }

        /**
         *	This function will return the version of the database server software.
         *
         *	@returns	The version of the database server software.
         */
        function getServerVersion() {

            // Connect
            $result = $this->connect();

            // Handle errors
            if ( ! $result && $this->_failOnError === true ) {
                trigger_error( $GLOBALS['YD_SQLITE_error'], YD_ERROR );
            }

            // Return the version
            return 'SQLite ' . sqlite_libversion();

        }

        /**
         *	Function that makes the actual connection.
         */
        function connect() {
            if ( $this->_conn == null ) {
                $conn = @sqlite_open( $this->_db, 0666, $error );
                if ( ! $conn ) {
                    return false;
                }
                $this->_conn = $conn;
            }
            return true;
        }

        /**
         *	This function will return a single record.
         *
         *	@param $sql	The SQL statement to use.
         *
         *	@returns	A single record matching the SQL statement.
         */
        function getRecord( $sql ) {
            $result = & $this->_connectAndExec( $sql );
            $type = YDConfig::get( 'YD_DB_FETCHTYPE' ) == YD_DB_FETCH_NUM ? SQLITE_NUM : SQLITE_ASSOC;
            return $this->_lowerKeyNames( sqlite_fetch_array( $result, $type ) );
        }

        /**
         *	This function will execute the SQL statement and return the records as an associative array. Optionally, you
         *	can limit the number of records that are returned. Optionally, you can also specify which record to start
         *	from.
         *
         *	@param $sql	The SQL statement to use.
         *	@param $limit	(optional) How many records to return
         *	@param $offset	(optional) Where to start from
         *
         *	@returns	The records matching the SQL statement as an associative array.
         */
        function getRecords( $sql, $limit=-1, $offset=-1 ) {
            $sql = $this->_prepareSqlForLimit( $sql, $limit, $offset );
            $result = & $this->_connectAndExec( $sql );
            $type = YDConfig::get( 'YD_DB_FETCHTYPE' ) == YD_DB_FETCH_NUM ? SQLITE_NUM : SQLITE_ASSOC;
            $dataset = array();
            while ( $line = $this->_lowerKeyNames( sqlite_fetch_array( $result, $type ) ) ) {
                array_push( $dataset, $line );
            }
            return $dataset;
        }

        /**
         *	This function will execute the SQL statement and return the number of affected records.
         *
         *	@param $sql	The SQL statement to use.
         *
         *	@returns	The number of affected rows.
         */
        function executeSql( $sql ) {
            $result = & $this->_connectAndExec( $sql );
            return sqlite_changes( $this->_conn );
        }

        /**
         *	This function will return the number of rows matched by the SQL query.
         *
         *	@param $sql	The SQL statement to use.
         *
         *	@returns	The number of rows matched by the SQL query.
         */
        function getMatchedRowsNum( $sql ) {
            $result = & $this->_connectAndExec( $sql );
            return sqlite_num_rows( $result );
        }

        /**
         *  This function will return the ID of the last insert for databases that support it. If the database doesn't
         *  support this, an error will be triggered.
         *
         *  @returns    The last insert ID of the last insert. Returns 0 If no auto-increment was generated, and false 
         *              if there was no connection
         */
        function getLastInsertID() {
            return sqlite_last_insert_rowid( $this->_conn );
        }

        /**
         *	This function will close the database connection.
         */
        function close() {
            if ( $this->_conn != null ) {
                $this->_conn = null;
                @sqlite_close( $this->_conn );
            }
        }

        /**
         *	This function will escape a string so that it's safe to include it in an SQL statement.
         *
         *	@param $string	The string to escape.
         *
         *	@returns	The escaped string.
         */
        function escape( $string ) {
            if ( is_string( $string ) ) {
                if ( strtolower( $string ) != 'null' ) {
                    return sqlite_escape_string( $string );
                }
            } else if ( is_null( $string ) ) {
                return 'null';
            }
            return $string;
        }

        /**
         *	This function will connect to the database, execute a query and will return the result handle.
         *
         *	@param $sql	The SQL statement to execute.
         *
         *	@returns	Handle to the result of the query.
         *
         *	@internal
         */
        function & _connectAndExec( $sql ) {

            // Add the table prefix
            $sql = str_replace( ' #_', ' ' . YDConfig::get( 'YD_DB_TABLEPREFIX', '' ), $sql );

            // Update the language placeholders
            $languageIndex = YDConfig::get( 'YD_DB_LANGUAGE_INDEX', null );
            if ( ! is_null( $languageIndex ) ) {
                $sql = str_replace( '_@', '_' . $languageIndex, $sql );
            }

            // Handle errors
            if ( ! $result && $this->_failOnError === true ) {
                trigger_error( $GLOBALS['YD_SQLITE_error'], YD_ERROR );
            }

            // Record the start time
            $timer = new YDTimer();

            // Execute the query
            $result = @sqlite_query( $sql, $this->_conn );

            // Handle errors
            if ( $result === false && $this->_failOnError === true ) {
                echo( '<b>Stacktrace:</b> <pre>' . YDDebugUtil::getStackTrace() . '</pre>' );
                echo( '<b>SQL Statement:</b> <pre>' . $this->formatSql( $sql ) . '</pre>' );
                trigger_error( sqlite_error_string( sqlite_last_error( $this->_conn ) ), YD_ERROR );
            }

            // Log the statement
            $this->_logSql( $sql, $timer->getElapsed() );

            // Return the result
            return $result;

        }

    }

?>