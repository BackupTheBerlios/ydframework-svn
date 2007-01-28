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
     *	This class defines a database driver for Oracle8i using the OCI8 interface.
     *
     *  @ingroup YDDatabase
     */
    class YDDatabaseDriver_oracle extends YDDatabaseDriver {

        /**
         *	This is the class constructor for the YDDatabaseDriver_oracle class.
         *
         *	@param $db		Name of the database.
         *	@param $user	(optional) User name to use for the connection.
         *	@param $pass	(optional) Password to use for the connection.
         *	@param $host	(optional) Host name to use for the connection.
         *	@param $host	(optional) Host name to use for the connection.
         *	@param $options	(optional) Options to pass to the driver.
         */
        function YDDatabaseDriver_oracle( $db, $user='', $pass='', $host='', $options=array() ) {
            $this->YDDatabaseDriver( $db,  $user, $pass, $host, $options );
            $this->_NLS_DATE_FORMAT = 'RRRR-MM-DD HH24:MI:SS';
            $this->_fmtDate = 'Y-m-d';
            $this->_fmtTimeStamp = 'Y-m-d, h:i:s A';
            $this->_driver = 'oracle';
        }

        /**
         *	This function will check if the server supports this database type.
         *
         *	@returns	Boolean indicating if the database type is supported by the server.
         */
        function isSupported() {
            return extension_loaded( 'oci8' );
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
                $error = ocierror();
                trigger_error( $error['message'], YD_ERROR );
            }

            // Return the version
            return 'Oracle ' . ociserverversion( $this->_conn );

        }

        /**
         *	Function that makes the actual connection.
         *
         *  @returns    Boolean indicating if the connection was succesfull or not.
         */
        function connect() {
            if ( $this->_conn == null ) {
                $conn = @OCILogon( $this->_user, $this->_pass, $this->_db );
                if ( ! $conn ) {
                    return false;
                }
                $this->_conn = $conn;
                $stmt = OCIParse(
                    $this->_conn, 'ALTER SESSION SET NLS_DATE_FORMAT=\'' . $this->_NLS_DATE_FORMAT . '\''
                );
                @OCIExecute( $stmt );
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
            $result = $this->_connectAndExec( $sql );
            $type = YDConfig::get( 'YD_DB_FETCHTYPE' ) == YD_DB_FETCH_NUM ? OCI_NUM : OCI_ASSOC;
            ocifetchinto( $result, $record, $type );
            OCIFreeStatement( $result );
            return $this->_lowerKeyNames( $record );
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
            $result = $this->_connectAndExec( $sql );
            $type = YDConfig::get( 'YD_DB_FETCHTYPE' ) == YD_DB_FETCH_NUM ? OCI_NUM : OCI_ASSOC;
            $dataset = array();
            while ( ocifetchinto( $result, $line, $type ) ) {
                array_push( $dataset, $this->_lowerKeyNames( $line ) );
            }
            OCIFreeStatement( $result );
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
            $result = $this->_connectAndExec( $sql );
            return ocirowcount( $result );
        }

        /**
         *	This function will return the number of rows matched by the SQL query.
         *
         *	@param $sql	The SQL statement to use.
         *
         *	@returns	The number of rows matched by the SQL query.
         *
         *  @remark     Don't use this for SQL select statements as the returned value will not be correct.
         */
        function getMatchedRowsNum( $sql ) {
            $result = $this->_connectAndExec( $sql );
            return ocirowcount( $result );
        }

        /**
         *	This function will close the database connection.
         */
        function close() {
            if ( $this->_conn != null ) {
                $this->_conn = null;
                @OCILogoff( $this->_conn );
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
                    return str_replace( "'", "''", $string );
                }
            } else if ( is_null( $string ) ) {
                return 'null';
            }
            return $string;
        }

        /**
         *	This function will escape a string so that it's safe to include it in an SQL statement and will surround it
         *	with the quotes appropriate for the database backend.
         *
         *	@param $string	The string to escape.
         *
         *	@returns	The escaped string surrounded by quotes.
         */
        function escapeSql( $string ) {
            if ( is_null( $string ) || strtolower( $string ) == 'null' ) {
                return 'null';
            }
            if ( strtolower( substr( $string, 0, 7 ) ) == 'to_date' ) {
                return $string;
            }
            return $this->_fmtQuote . $this->escape( $string ) . $this->_fmtQuote;
        }

        /**
         *	Format the $date in the format the database accepts. The $date parameter can be a Unix integer timestamp or
         *	an ISO format Y-m-d. Uses the fmtDate field, which holds the format to use. If null or false or '' is passed
         *	in, it will be converted to an SQL null.
         *
         *	@param $date	(optional) Unix integer timestamp or an ISO format Y-m-d. If you give it the string value
         *					__NOW__, the current time will be used.
         *
         *	@returns	The properly formatted date for the database.
         */
        function getDate( $date='' ) {
            $date = $this->_getDateOrTime( $date, $this->_fmtDate );
            if ( $date == 'null' ) {
                return $date;
            } else {
                return 'TO_DATE ( \'' . $date . '\', \'YYYY-MM-DD\' )';
            }
        }

        /**
         *	Format the timestamp $ts in the format the database accepts; this can be a Unix integer timestamp or an ISO
         *	format Y-m-d H:i:s. Uses the fmtTimeStamp field, which holds the format to use. If null or false or '' is
         *	passed in, it will be converted to an SQL null.
         *
         *	@param $time	(optional) Unix integer timestamp or an ISO format Y-m-d H:i:s. If you give it the string
         *					value __NOW__, the current time will be used.
         *
         *	@returns	The properly formatted timestamp for the database.
         */
        function getTime( $time='' ) {
            $time = $this->_getDateOrTime( $time, $this->_fmtTimeStamp );
            if ( $time == 'null' ) {
                return $time;
            } else {
                return 'TO_DATE ( \'' . $time . '\', \'RRRR-MM-DD, HH:MI:SS AM\')';
            }
        }

        /**
         *	This function will connect to the database, execute a query and will return the result handle.
         *
         *	@param $sql	The SQL statement to execute.
         *
         *  @returns    Handle to the result of the query. In case of an error, this function triggers an error.
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

            // Connect
            $result = $this->connect();

            // Handle errors
            if ( ! $result && $this->_failOnError === true ) {
                $error = ocierror();
                trigger_error( $error['message'], YD_ERROR );
            }

            // Record the start time
            $timer = new YDTimer();

            // Create statement
            $stmt = OCIParse( $this->_conn, $sql );

            // Handle errors
            if ( ! $stmt && $this->_failOnError === true ) {
                $error = ocierror( $stmt );
                trigger_error( $error['message'], YD_ERROR );
            }

            // Execute
            $result = @OCIExecute( $stmt );

            // Handle errors
            if ( $result === false && $this->_failOnError === true ) {
                $error = ocierror( $stmt );
                if ( ! empty( $error['sqltext'] ) ) {
                    $error['message'] .= ' (SQL: ' . $error['sqltext'] . ')';
                }
                echo( '<b>Stacktrace:</b> <pre>' . YDDebugUtil::getStackTrace() . '</pre>' );
                echo( '<b>SQL Statement:</b> <pre>' . $this->formatSql( $sql ) . '</pre>' );
                trigger_error( $error['message'], YD_ERROR );
            }

            // Log the statement
            $this->_logSql( $sql, $timer->getElapsed() );

            // Return the result
            return $stmt;

        }

        /**
         *	This function will preprare an SQL SELECT statement for the getRecordsLimit and getRecordsPaged functions.
         *
         *	@param $sql	The SQL statement to prepare
         *	@param $limit	(optional) How many records to return
         *	@param $offset	(optional) Where to start from
         *
         *	@internal
         *
         *	@todo
         *		Still fails with SELECT * as SELECT rownum as rn, * is not supported
         */
        function _prepareSqlForLimit( $sql, $limit=-1, $offset=-1 ) {

            // if no limit and offset, return the original SQL statement
            if ( $offset < 0 && $limit < 0 ) return $sql;
             
            // if no offset but there's a limit return everything before 1+OFFSET+LIMIT 
            // if offset is defined test limit
            if ( $offset < 0 ) { $end = 1 + $offset + $limit;
                $sql_append = 'rn <= ' . $end . ';';
            } else {

               // offset is the line
               $offset ++;
          
               // if no limit return everything bigget than OFFSET
               // if limit is defined return everything between OFFSET and OFFSET+LIMIT
               if ( $limit < 0 ) {
                   $sql_append = 'rn => ' . $offset . ';';
               } else {
                   $end = $offset + $limit;
                   $sql_append = 'rn between ' . $offset . ' and ' . $end . ';';
               }
            }

            // Change the select statement 
            if ( substr( strtolower( $sql ), 0, 6 ) == 'select' ) {
                $sql = 'SELECT /*+FIRST_ROWS*/ rownum as rn, ' . substr( $sql, 7 );
            }
          
            // Return the changed SQL statement 
            return 'SELECT * FROM (' . $sql . ') WHERE ' . $sql_append;

        }

    }

?>