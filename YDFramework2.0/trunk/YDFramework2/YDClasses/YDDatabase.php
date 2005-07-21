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
    YDInclude( 'YDUrl.php' );
    YDInclude( 'YDUtil.php' );
    YDInclude( 'YDRecordSet.php' );

    // Constants
    define( 'YD_DB_FETCH_ASSOC', 1 );
    define( 'YD_DB_FETCH_NUM', 2 );

    // Configure the default for this class
    YDConfig::set( 'YD_DB_FETCHTYPE', YD_DB_FETCH_ASSOC, false );

    /**
     *  This class defines a database object.
     */
    class YDDatabase extends YDBase {

        /**
         *  Using this static function, you can get an instance of a YDDatabaseDriver class.
         *
         *  @param $driver  Name of the database driver or array containing drivername, file name and class name.
         *  @param $db      Database name to use for the connection.
         *  @param $user    (optional) User name to use for the connection.
         *  @param $pass    (optional) Password to use for the connection.
         *  @param $host    (optional) Host name to use for the connection.
         */
        function getInstance( $driver, $db, $user='', $pass='', $host='' ) {

            // The list of known drivers
            $regDrivers = array();

            // Register new driver
            if( is_array( $driver ) && isset( $driver['drivername'] ) ) {

                // Register a new driver
                $regDrivers[ strtolower( $driver['drivername'] ) ] = $driver;
                $driver = $driver[ 'drivername' ];

            } else {

                // Register the standard drivers
                $regDrivers[ strtolower( 'mysql' ) ] = array(
                    'class' => 'YDDatabaseDriver_mysql', 'file' => ''
                );
                $regDrivers[ strtolower( 'oracle' ) ] = array(
                    'class' => 'YDDatabaseDriver_oracle', 'file' => 'YDDatabaseDriver_oracle.php'
                );
                $regDrivers[ strtolower( 'postgres' ) ] = array(
                    'class' => 'YDDatabaseDriver_postgres', 'file' => 'YDDatabaseDriver_postgres.php'
                );
                $regDrivers[ strtolower( 'sqlite' ) ] = array(
                    'class' => 'YDDatabaseDriver_sqlite', 'file' => 'YDDatabaseDriver_sqlite.php'
                );

                // Check if the driver exists
                if ( ! array_key_exists( strtolower( $driver ), $regDrivers ) ) {
                    trigger_error( 'Unsupported database type: "' . $driver . '".', YD_ERROR );
                }

            }

            // Include the driver
            if ( ! empty( $regDrivers[ strtolower( $driver ) ]['file'] ) ) {
                YDInclude( $regDrivers[ strtolower( $driver ) ]['file'] );
            }

            // Check if the driver is supported
            if ( ! call_user_func( array( $regDrivers[ strtolower( $driver ) ]['class'], 'isSupported' ) ) ) {
                trigger_error( 'Unsupported database type: "' . $driver . '". Extension is not loaded.', YD_ERROR );
            }

            // Make a new connection object and return it
            $className = $regDrivers[ strtolower( $driver ) ]['class'];
            return new $className( $db, $user, $pass, $host );

        }

    }

    /**
     *  This class defines a database driver.
     */
    class YDDatabaseDriver extends YDBase {

        /**
         *  This is the class constructor for the YDDatabaseDriver class.
         *
         *  @param $db      Database name to use for the connection.
         *  @param $user    (optional) User name to use for the connection.
         *  @param $pass    (optional) Password to use for the connection.
         *  @param $host    (optional) Host name to use for the connection.
         *  @param $options (optional) Options to pass to the driver.
         */
        function YDDatabaseDriver( $db, $user='', $pass='', $host='', $options=array() ) {

            // Initialize YDBase
            $this->YDBase();

            // Initialize the variables
            $this->_db = $db;
            $this->_user = $user;
            $this->_pass = $pass;
            $this->_host = $host;
            $this->_options = $options;
            $this->_driver = 'unknown';

            // The connection object
            $this->_conn = null;

            // Date and timestamp, and quote styles
            $this->_fmtDate = 'Y-m-d';
            $this->_fmtTimeStamp = 'Y-m-d H:i:s';
            $this->_fmtQuote = "'";

        }
        
        /**
         *  This funciton returns the database name.
         *
         *  @returns    The database name.
         */
        function getDatabase() {
            return $this->_db;
        }
        
        /**
         *  This funciton returns the user name of the connection.
         *
         *  @returns    The user name.
         */
        function getUser() {
            return $this->_user;
        }
        
        /**
         *  This funciton returns the password of the connection.
         *
         *  @returns    The password.
         */
        function getPassword() {
            return $this->_pass;
        }
        
        /**
         *  This funciton returns the host name of the connection.
         *
         *  @returns    The host name.
         */
        function getHost() {
            return $this->_host;
        }
        
        /**
         *  This funciton returns the options of the connection.
         *
         *  @returns    The array of options
         */
        function getOptions() {
            return $this->_options;
        }

        /**
         *  This funciton returns the driver in use.
         *
         *  @returns    The driver name in use.
         */
        function getDriver() {
            return $this->_driver;
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
         *  This function will return the version of the database server software.
         *
         *  @returns    The version of the database server software.
         */
        function getServerVersion() {
            return 'unknown';
        }

        /**
         *  Function that makes the actual connection.
         */
        function connect() {
        }

        /**
         *  This function will return the number of queries that were executed.
         *
         *  @returns    The number of queries that were executed.
         */
        function getSqlCount() {
            return sizeof( $GLOBALS['YD_SQL_QUERY'] );
        }

        /**
         *  This function will return a single value.
         *
         *  @param $sql     The SQL statement to use.
         *  @param $index   (optional) The index of the column you want to use for getting the value.
         *
         *  @returns    A single value matching the SQL statement. False when no record was found.
         */
        function getValue( $sql, $index=0 ) {
            $records = $this->getRecord( $sql );
            if (! $records ) {
                return false;
            }
            $record = array_values( $records );
            if ( ! isset( $record[ $index ] ) ) {
                return false;
            }
            return $record[ $index ];
        }

        /**
         *  This function will return a single value.
         *
         *  @param $sql     The SQL statement to use.
         *  @param $name    The field value to return.
         *
         *  @returns    A single value matching the SQL statement, false when no value was found.
         */
        function getValueByName( $sql, $name ) {
            $record = array_values( $this->getRecords( $sql ) );
            if ( ! isset( $record[0][ strtolower( $name ) ] ) ) {
                return false;
            }
            return $record[0][ strtolower( $name ) ];
        }

        /**
         *  This function will return an array of single values.
         *
         *  @param $sql     The SQL statement to use.
         *  @param $name    The field value to return.
         *
         *  @returns    An array of single values matching the SQL statement.
         */
        function getValuesByName( $sql, $name ) {
            $records = $this->getRecords( $sql );
            if ( ! $records ) {
                return array();
            }
            $values = array();
            foreach ( $records as $record ) {
                array_push( $values, $record[ strtolower( $name  ) ] );
            }
            return $values;
        }

        /**
         *  Get the values as an associative array using the indicated columns for keys and values.
         *
         *  @param $sql     The SQL statement to use.
         *  @param $key     The field to use for the keys.
         *  @param $val     The field or array of fileds to use for the values.
         *  @param $prefix  (optional) The text to prepend to the key name.
         *
         *  @returns An associative array.
         */
        function getAsAssocArray( $sql, $key, $val, $prefix='' ) {

            $records = $this->getRecords( $sql );
            if ( ! $records ) {
                return array();
            }
            $result = array();
            $key = strtolower( $key );
            $prefix = strtolower( $prefix );
           
            if ( is_array( $val ) ) {
                foreach ( $records as $record ) {
                    $result[ $prefix . $record[ $key ] ] = array();
                    foreach( $val as $v ) {
                        $v = strtolower( $v );
                        $result[ $prefix . $record[ $key ] ][ $v ] = $record[ $v ];
                    }
                }
            }
            else{
                $val = strtolower( $val );
                foreach ( $records as $record ) {
                    $result[ $prefix . $record[ $key ] ] = $record[ $val ];
                }
            }
           
            return $result; 

        }

        /**
         *  This function will return a single record.
         *
         *  @param $sql The SQL statement to use.
         *
         *  @returns    A single record matching the SQL statement.
         */
        function getRecord( $sql ) {
        }

        /**
         *  This function will execute the SQL statement and return the records as an associative array. Optionally, you
         *  can limit the number of records that are returned. Optionally, you can also specify which record to start
         *  from.
         *
         *  @param $sql The SQL statement to use.
         *  @param $limit   (optional) How many records to return
         *  @param $offset  (optional) Where to start from
         *
         *  @returns    The records matching the SQL statement as an associative array.
         */
        function getRecords( $sql, $limit=-1, $offset=-1 ) {
        }

        /**
         *  This function executes the SQL statement and returns the records as a YDRecordSet object, which contains
         *  meta information about the recordset as well as the recordset itself. Optionally, you can limit the number
         *  of records that are returned. Optionally, you can also specify which record to start from. This is the
         *  preferred way when you are using paged resultsets.
         *
         *  @param  $sql        The list of records as an array (as returned by the YDDatabaseDriver::getRecords
         *                      function.
         *  @param  $page       (optional) The page you want to retrieve. If omitted all records will be returned.
         *  @param  $pagesize   (optional) The maximum number of rows for each page. If a page number is given, the
         *                      default will be to return a maximum of 20 rows. If no page number is given, the pagesize
         *                      will be the same as the total number of rows in the recordset.
         *  @param  $pagevar    (optional) The name of the query string variable indicating the page. Defaults to "page"
         *  @param  $sizevar    (optional) The name of the query string variable indicating the page size. Defaults to
         *                      "size"

         *
         *  @returns    The records matching the SQL statement as a YDRecordSet object.
         *
         *  @todo
         *      Performance needs to be improved. This is a quick and dirty solution right now.
         */
        function getRecordsAsSet( $sql, $page=-1, $pagesize=-1, $pagevar = 'page', $sizevar = 'size' ) {

            // Get all records
            $records = $this->getRecords( $sql );

            // Return the YDRecordSet
            return new YDRecordSet( $records, $page, $pagesize, $pagevar, $sizevar );

        }

        /**
         *  This function will execute the SQL statement and return the number of affected records.
         *
         *  @param $sql The SQL statement to use.
         *
         *  @returns    The number of affected rows.
         */
        function executeSql( $sql ) {
        }

        /**
         *  This function will return the number of rows matched by the SQL query.
         *
         *  @param $sql The SQL statement to use.
         *
         *  @returns    The number of rows matched by the SQL query.
         */
        function getMatchedRowsNum( $sql ) {
        }

        /**
         *  This function will return the ID of the last insert for databases that support it. If the database doesn't
         *  support this, an error will be triggered.
         *
         *  @returns    The last insert ID of the last insert.
         */
        function getLastInsertID() {
            trigger_error( 'The database driver ' . $this->_driver . ' does not support getLastInsertID.' );
        }

        /**
         *  This function will insert the specified values in to the specified table.
         *
         *  @param $table   The table to insert the data into.
         *  @param $values  Associative array with the field names and their values to insert.
         *  @param $filter  If true, all field names that start with underscore will be left out. Default: true.
         *
         *  @returns    The number of affected rows.
         */
        function executeInsert( $table, $values, $filter=true ) {
            $sql = $this->_createSqlInsert( $table, $values, $filter );
            return $this->executeSql( $sql );
        }

        /**
         *  This function will update the specified values in to the specified table using the specified where clause.
         *
         *  @param $table   The table to update the data froms.
         *  @param $values  Associative array with the field names and their values to insert.
         *  @param $where   (optional) The where statement to execute.
         *  @param $filter  If true, all field names that start with underscore will be left out. Default: true.
         *
         *  @returns    The number of affected rows.
         */
        function executeUpdate( $table, $values, $where='', $filter=true ) {
            $sql = $this->_createSqlUpdate( $table, $values, $where, $filter );
            return $this->executeSql( $sql );
        }

        /**
         *  This function will delete the records matching the where string from the specified database table.
         *
         *  @param $table   The table to delete the data from.
         *  @param $where   (optional) The where statement to execute.
         *
         *  @returns    The number of affected rows.
         */
        function executeDelete( $table, $where='' ) {
            $sql = 'DELETE FROM ' . $table;
            if ( ! empty( $where ) ) {
                $sql .= ' WHERE ' . trim( $where );
            }
            return $this->executeSql( $sql );
        }

        /**
         *  This function will close the database connection.
         */
        function close() {
        }

        /**
         *  This function will escape a string so that it's safe to include it in an SQL statement.
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
         *  This function will escape a string so that it's safe to include it in an SQL statement and will surround it
         *  with the quotes appropriate for the database backend.
         *
         *  @param $string  The string to escape.
         *
         *  @returns    The escaped string surrounded by quotes.
         */
        function sqlString( $string ) {
            if ( is_string( $string ) ) {
                if ( strtolower( $string ) != 'null' ) {
                    return $this->_fmtQuote . $this->string( $string ) . $this->_fmtQuote;
                }
            } else if ( is_null( $string ) ) {
                return 'null';
            }
            return $string;
        }

        /**
         *  Format the $date in the format the database accepts. The $date parameter can be a Unix integer timestamp or
         *  an ISO format Y-m-d. Uses the fmtDate field, which holds the format to use. If null or false or '' is passed
         *  in, it will be converted to an SQL null.
         *
         *  @param $date    (optional) Unix integer timestamp or an ISO format Y-m-d. If you give it the string value
         *                  __NOW__, the current time will be used.
         *
         *  @returns    The properly formatted date for the database.
         */
        function getDate( $date='' ) {
            return $this->_getDateOrTime( $date, $this->_fmtDate );
        }

        /**
         *  Format the timestamp $ts in the format the database accepts; this can be a Unix integer timestamp or an ISO
         *  format Y-m-d H:i:s. Uses the fmtTimeStamp field, which holds the format to use. If null or false or '' is
         *  passed in, it will be converted to an SQL null.
         *
         *  @param $time    (optional) Unix integer timestamp or an ISO format Y-m-d H:i:s. If you give it the string
         *                  value __NOW__, the current time will be used.
         *
         *  @returns    The properly formatted timestamp for the database.
         */
        function getTime( $time='' ) {
            return $this->_getDateOrTime( $time, $this->_fmtTimeStamp );
        }

        /**
         *  This function will convert the argument to a date or timestamp.
         *
         *  @param $arg The argument to convert.
         *  @param $fmt     The format to return the argument in.
         *
         *  @returns    The argument as date or time.
         *
         *  @internal
         */
        function _getDateOrTime( $arg='', $fmt='' ) {
            if ( empty( $arg ) || $arg == false || $arg == null ) {
                return 'null';
            }
            if ( $arg == '__NOW__' ) {
                $arg = time();
            }
            if ( is_string( $arg ) ) {
                $arg = strtotime( $arg );
            }
            return date( $fmt, $arg );
        }

        /**
         *  This function will create an SQL insert statement from the specified array and table name.
         *
         *  @remarks
         *      All the field names that start with an underscore will not be used.
         *
         *  @param $table   The table to insert the data into.
         *  @param $values  Associative array with the field names and their values to insert.
         *  @param $filter  If true, all field names that start with underscore will be left out. Default: true.
         *
         *  @returns    The insert SQL statement
         *
         *  @internal
         */
        function _createSqlInsert( $table, $values, $filter=true ) {

            // Check if there are values
            if ( empty( $table ) ) {
                trigger_error( 'No table specified for the INSERT statement.', YD_ERROR );
            }

            // Get the list of field names and values
            $ifields = array();
            $ivalues = array();
            foreach ( $values as $key=>$value ) {
                if ( $filter ) {
                    if ( substr( $key, 0, 1 ) != '_' ) {
                        array_push( $ifields, $key );
                        array_push( $ivalues, $this->sqlString( $value ) );
                    }
                } else {
                    array_push( $ifields, $key );
                    array_push( $ivalues, $this->sqlString( $value ) );
                }
            }

            // Check if there are values
            if ( sizeof( $ifields ) == 0 ) {
                trigger_error( 'No values were submitted for the INSERT statement.', YD_ERROR );
            }

            // Convert the fields and values to a string
            $ifields = implode( ', ', $ifields );
            $ivalues = implode( ', ', $ivalues );

            // Create the SQL statement
            return 'INSERT INTO ' . $table . ' (' . $ifields . ') VALUES (' . $ivalues . ')';

        }

        /**
         *  This function will create an SQL update statement from the specified array, table name and where clause.
         *
         *  @remarks
         *      All the field names that start with an underscore will not be used.
         *
         *  @param $table   The table to insert the data into.
         *  @param $values  Associative array with the field names and their values to insert.
         *  @param $where   The where statement to execute.
         *  @param $filter  If true, all field names that start with underscore will be left out. Default: true.
         *
         *  @returns    The insert SQL statement
         *
         *  @internal
         */
        function _createSqlUpdate( $table, $values, $where='', $filter=true ) {

            // Check if there are values
            if ( empty( $table ) ) {
                trigger_error( 'No table specified for the INSERT statement.', YD_ERROR );
            }

            // Get the list of field names and values
            $uvalues = array();
            foreach ( $values as $key=>$value ) {
                if ( $filter ) {
                    if ( substr( $key, 0, 1 ) != '_' ) {
                        array_push( $uvalues, $key . "=" . $this->sqlString( $value ) );
                    }
                } else {
                    array_push( $uvalues, $key . "=" . $this->sqlString( $value ) );
                }
            }

            // Check if there are values
            if ( sizeof( $uvalues ) == 0 ) {
                trigger_error( 'No values were submitted for the UPDATE statement.', YD_ERROR );
            }

            // Convert the fields and values to a string
            $uvalues = implode( ', ', $uvalues );

            // Create the SQL statement
            $sql =  'UPDATE ' . $table . ' SET ' . $uvalues;
            if ( ! empty( $where ) ) { $sql .= ' WHERE ' . $where; }

            // Return the SQL
            return $sql;

        }

        /**
         *  This function will connect to the database, execute a query and will return the result handle.
         *
         *  @param $sql The SQL statement to execute.
         *
         *  @returns    Handle to the result of the query.
         *
         *  @internal
         */
        function & _connectAndExec( $sql ) {
        }

        /**
         *  This function will convert the given record so that all the keys are lowercase.
         *
         *  @param $array   The array to convert.
         *
         *  @returns    The original array with all the field names as lowercase.
         *
         *  @internal
         */
        function _lowerKeyNames( $array ) {
            if ( $array ) {
                return array_change_key_case( $array, CASE_LOWER );
            } else {
                return $array;
            }
        }

        /**
         *  This function will log the SQL statement to the debug log and keep track of the number of queries that were
         *  executed.
         *
         *  @param $sql The SQL statement to log.
         *
         *  @internal
         */
        function _logSql( $sql ) {
            array_push( $GLOBALS['YD_SQL_QUERY'], YDStringUtil::removeWhiteSpace( $sql ) );
        }

        /**
         *  This function will preprare an SQL SELECT statement for the getRecordsLimit and getRecordsPaged functions.
         *
         *  @param $sql The SQL statement to prepare
         *  @param $limit   (optional) How many records to return
         *  @param $offset  (optional) Where to start from
         *
         *  @internal
         */
        function _prepareSqlForLimit( $sql, $limit=-1, $offset=-1 ) { 

            // If no limit and offset, return the original SQL statement 
            if ( $limit < 0 && $offset < 0 ) {
                return $sql;
            }

            // if offset is defined but limit isn't return everything after offset 
            // if limit is defined test offset 
            if ( $limit < 0 ) {
                $sql_append = $offset . ',18446744073709551615';
            } else {
             
               // if offset is not defined return everything before limit 
               // otherwise use offset and limit 
               if ( $offset < 0 ) {
                   $sql_append = $limit;
               } else {
                   $sql_append = $offset . ',' . $limit;
               }

            } 

            // Return the changed SQL statement 
            return $sql . ' LIMIT ' . $sql_append;
        }

    }

    /**
     *  This class defines a database driver for MySQL.
     */
    class YDDatabaseDriver_mysql extends YDDatabaseDriver {

        /**
         *  This is the class constructor for the YDDatabaseDriver_mysql class.
         *
         *  @param $db      Name of the database.
         *  @param $user    (optional) User name to use for the connection.
         *  @param $pass    (optional) Password to use for the connection.
         *  @param $host    (optional) Host name to use for the connection.
         *  @param $host    (optional) Host name to use for the connection.
         *  @param $options (optional) Options to pass to the driver.
         */
        function YDDatabaseDriver_mysql( $db, $user='', $pass='', $host='', $options=array() ) {
            $this->YDDatabaseDriver( $db,  $user, $pass, $host, $options );
            $this->_driver = 'mysql';
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
         *  This function will return the version of the database server software.
         *
         *  @returns    The version of the database server software.
         */
        function getServerVersion() {
            $this->connect();
            return 'MySQL ' . mysql_get_server_info();
        }

        /**
         *  Function that makes the actual connection.
         */
        function connect() {
            $conn = @mysql_connect( $this->_host, $this->_user, $this->_pass );
            if ( ! $conn ) {
                trigger_error( mysql_error(), YD_ERROR );
            }
            if ( ! @mysql_select_db( $this->_db, $conn ) ) {
                trigger_error( mysql_error( $conn ), YD_ERROR );
            }
            $this->_conn = $conn;
        }

        /**
         *  This function will return a single record.
         *
         *  @param $sql The SQL statement to use.
         *
         *  @returns    A single record matching the SQL statement.
         */
        function getRecord( $sql ) {
            $result = & $this->_connectAndExec( $sql );
            $type = YDConfig::get( 'YD_DB_FETCHTYPE' ) == YD_DB_FETCH_NUM ? MYSQL_NUM : MYSQL_ASSOC;
            $record = $this->_lowerKeyNames( mysql_fetch_array( $result, $type ) );
            mysql_free_result( $result );
            return $record;
        }

        /**
         *  This function will execute the SQL statement and return the records as an associative array. Optionally, you
         *  can limit the number of records that are returned. Optionally, you can also specify which record to start
         *  from.
         *
         *  @param $sql The SQL statement to use.
         *  @param $limit   (optional) How many records to return
         *  @param $offset  (optional) Where to start from
         *
         *  @returns    The records matching the SQL statement as an associative array.
         */
        function getRecords( $sql, $limit=-1, $offset=-1 ) {
            $sql = $this->_prepareSqlForLimit( $sql, $limit, $offset );
            $result = & $this->_connectAndExec( $sql );
            $type = YDConfig::get( 'YD_DB_FETCHTYPE' ) == YD_DB_FETCH_NUM ? MYSQL_NUM : MYSQL_ASSOC;
            $dataset = array();
            while ( $line = $this->_lowerKeyNames( mysql_fetch_array( $result, $type ) ) ) {
                array_push( $dataset, $line );
            }
            mysql_free_result( $result );
            return $dataset;
        }

        /**
         *  This function will execute the SQL statement and return the number of affected records.
         *
         *  @param $sql The SQL statement to use.
         *
         *  @returns    The number of affected rows.
         */
        function executeSql( $sql ) {
            $result = & $this->_connectAndExec( $sql );
            return mysql_affected_rows( $this->_conn );
        }

        /**
         *  This function will return the number of rows matched by the SQL query.
         *
         *  @param $sql The SQL statement to use.
         *
         *  @returns    The number of rows matched by the SQL query.
         */
        function getMatchedRowsNum( $sql ) {
            $result = & $this->_connectAndExec( $sql );
            return mysql_num_rows( $result );
        }

        /**
         *  This function will return the ID of the last insert for databases that support it. If the database doesn't
         *  support this, an error will be triggered.
         *
         *  @returns    The last insert ID of the last insert.
         */
        function getLastInsertID() {
            return mysql_insert_id( $this->_conn );
        }

        /**
         *  This function will close the database connection.
         */
        function close() {
            if ( $this->_conn != null ) {
                $this->_conn = null;
                @mysql_close( $this->_conn );
            }
        }

        /**
         *  This function will escape a string so that it's safe to include it in an SQL statement.
         *
         *  @param $string  The string to escape.
         *
         *  @returns    The escaped string.
         */
        function string( $string ) {
            if ( is_string( $string ) ) {
                if ( strtolower( $string ) != 'null' ) {
                    $this->connect();
                    return mysql_real_escape_string( $string );
                }
            } else if ( is_null( $string ) ) {
                return 'null';
            }
            return $string;
        }

        /**
         *  This function will connect to the database, execute a query and will return the result handle.
         *
         *  @param $sql The SQL statement to execute.
         *
         *  @returns    Handle to the result of the query.
         *
         *  @internal
         */
        function & _connectAndExec( $sql ) {
            $this->_logSql( $sql );
            $this->connect();
            $result = @mysql_query( $sql, $this->_conn );
            if ( $result === false ) {
                trigger_error( '[' . mysql_errno( $this->_conn ) . '] ' . mysql_error( $this->_conn ), YD_ERROR );
            }
            return $result;
        }

    }

?>