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
	 *	This class defines a database object.
	 */
	class YDDatabase extends YDBase {

		/**
		 *	Class constructor for the YDDatabase class.
		 *
		 *	@param $driver	Name of the database driver.
		 *	@param $db		Database name to use for the connection.
		 *	@param $user	(optional) User name to use for the connection.
		 *	@param $pass	(optional) Password to use for the connection.
		 *	@param $host	(optional) Host name to use for the connection.
		 *
		 *	@deprecated	Use the static method getInstance to get a new YDDatabase class instance.
		 */
		function YDDatabase( $driver, $db, $user='', $pass='', $host='' ) {
			trigger_error( 'Use the static method getInstance to get a new YDDatabase class instance.', YD_ERROR );
		}

		/**
		 *	Using this static function, you can get an instance of a YDDatabaseDriver class.
		 *
		 *	@param $driver	Name of the database driver.
		 *	@param $db		Database name to use for the connection.
		 *	@param $user	(optional) User name to use for the connection.
		 *	@param $pass	(optional) Password to use for the connection.
		 *	@param $host	(optional) Host name to use for the connection.
		 */
		function getInstance( $driver, $db, $user='', $pass='', $host='' ) {

			// The list of known drivers
			$regDrivers = array();

			// Register the standard drives
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
	 *	This class defines a database driver.
	 */
	class YDDatabaseDriver extends YDBase {

		/**
		 *	This is the class constructor for the YDDatabaseDriver class.
		 *
		 *	@param $db		Name of the database.
		 *	@param $db		Database name to use for the connection.
		 *	@param $user	(optional) User name to use for the connection.
		 *	@param $pass	(optional) Password to use for the connection.
		 *	@param $host	(optional) Host name to use for the connection.
		 *	@param $options	(optional) Options to pass to the driver.
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

			// The connection object
			$this->_conn = null;

			// Date and timestamp, and quote styles
			$this->_fmtDate = 'Y-m-d';
			$this->_fmtTimeStamp = 'Y-m-d H:i:s';
			$this->_fmtQuote = "'";

		}

		/**
		 *	This function will check if the server supports this database type.
		 *
		 *	@returns	Boolean indicating if the database type is supported by the server.
		 */
		function isSupported() {
			return true;
		}

		/**
		 *	This function will return the version of the database server software.
		 *
		 *	@returns	The version of the database server software.
		 */
		function getServerVersion() {
			return 'unknown';
		}

		/**
		 *	Function that makes the actual connection.
		 */
		function connect() {
		}

		/**
		 *	This function will return the number of queries that were executed.
		 *
		 *	@returns	The number of queries that were executed.
		 */
		function getSqlCount() {
			return sizeof( $GLOBALS['YD_SQL_QUERY'] );
		}

		/**
		 *	This function will return a single value.
		 *
		 *	@param $sql		The SQL statement to use.
		 *	@param $index	(optional) The index of the column you want to use for getting the value.
		 *
		 *	@returns	A single value matching the SQL statement.
		 */
		function getValue( $sql, $index=0 ) {
			$records = $this->getRecord( $sql );
			if ( $records == null ) {
				return false;
			}
			$record = array_values( $records );
			if ( ! $record ) {
				return false;
			}
			if ( ! isset( $record[ $index ] ) ) {
				return false;
			}
			return $record[ $index ];
		}

		/**
		 *	This function will return a single value.
		 *
		 *	@param $sql		The SQL statement to use.
		 *	@param $name	The field value to return.
		 *
		 *	@returns	A single value matching the SQL statement.
		 */
		function getValueByName( $sql, $name ) {
			$record = array_values( $this->getRecords( $sql ) );
			if ( ! $record ) { return false; }
			return $record[ strtolower( $name ) ];
		}

		/**
		 *	This function will return an array of single values.
		 *
		 *	@param $sql		The SQL statement to use.
		 *	@param $name	The field value to return.
		 *
		 *	@returns	An array of single values matching the SQL statement.
		 */
		function getValuesByName( $sql, $name ) {
			$records = $this->getRecords( $sql );
			if ( ! $records ) { return false; }
			$values = array();
			foreach ( $records as $record ) { array_push( $values, $record[ strtolower( $name  ) ] ); }
			return $values;
		}

		/**
		 *	Get the values as an associative array using the indicated columns for keys and values.
		 *
		 *	@param $sql		The SQL statement to use.
		 *	@param $key		The field to use for the keys.
		 *	@param $val		The field to use for the values.
		 *	@param $prefix	(optional) The text to prepend to the key name.
		 *
		 *	@returns An associative array.
		 */
		function getAsAssocArray( $sql, $key, $val, $prefix='' ) {
			$records = $this->getRecords( $sql );
			if ( ! $records ) { return false; }
			$result = array();
			$key = strtolower( $key );
			$val = strtolower( $val );
			$prefix = strtolower( $prefix );
			foreach ( $records as $record ) {
				$result[ $prefix . $record[ $key ] ] = $record[ $val ];
			}
			return $result;
		}

		/**
		 *	This function will return a single record.
		 *
		 *	@param $sql	The SQL statement to use.
		 *
		 *	@returns	A single record matching the SQL statement.
		 */
		function getRecord( $sql ) {
		}

		/**
		 *	This function will execute the SQL statement and return the records as an associative array.
		 *
		 *	@param $sql	The SQL statement to use.
		 *
		 *	@returns	The records matching the SQL statement as an associative array.
		 */
		function getRecords( $sql ) {
		}

		/**
		 *	This function will execute the SQL statement and return the number of affected records.
		 *
		 *	@param $sql	The SQL statement to use.
		 *
		 *	@returns	The number of affected rows.
		 */
		function executeSql( $sql ) {
		}

		/**
		 *	This function will return the number of rows matched by the SQL query.
		 *
		 *	@param $sql	The SQL statement to use.
		 *
		 *	@returns	The number of rows matched by the SQL query.
		 */
		function getMatchedRowsNum( $sql ) {
		}

		/**
		 *	This function will insert the specified values in to the specified table.
		 *
		 *	@param $table	The table to insert the data into.
		 *	@param $values	Associative array with the field names and their values to insert.
		 */
		function executeInsert( $table, $values ) {
			$sql = $this->_createSqlInsert( $table, $values );
			$result = & $this->_connectAndExec( $sql );
			return true;
		}

		/**
		 *	This function will update the specified values in to the specified table using the specified where clause.
		 *
		 *	@param $table	The table to insert the data into.
		 *	@param $values	Associative array with the field names and their values to insert.
		 *	@param $where	The where statement to execute.
		 */
		function executeUpdate( $table, $values, $where='' ) {
			$sql = $this->_createSqlUpdate( $table, $values, $where );
			return $this->executeSql( $sql );
		}

		/**
		 *	This function will close the database connection.
		 */
		function close() {
		}

		/**
		 *	This function will escape a string so that it's safe to include it in an SQL statement.
		 *
		 *	@param $string	The string to escape.
		 *
		 *	@returns	The escaped string.
		 */
		function string( $string ) {
			if ( is_string( $string ) ) {
				if ( strtolower( $string ) != 'null' ) {
					return str_replace( "'", "''", $string );
				}
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
		function sqlString( $string ) {
			if ( is_string( $string ) ) {
				if ( strtolower( $string ) != 'null' ) {
					return $this->_fmtQuote . $this->string( $string ) . $this->_fmtQuote;
				}
			}
			return $string;
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
			return $this->_getDateOrTime( $date, $this->_fmtDate );
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
			return $this->_getDateOrTime( $time, $this->_fmtTimeStamp );
		}

		/**
		 *	This function will convert the argument to a date or timestamp.
		 *
		 *	@param $arg	The argument to convert.
		 *	@param $fmt		The format to return the argument in.
		 *
		 *	@returns	The argument as date or time.
		 *
		 *	@internal
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
		 *	This function will create an SQL insert statement from the specified array and table name.
		 *
		 *	@remarks
		 *		All the field names that start with an underscore will not be used.
		 *
		 *	@param $table	The table to insert the data into.
		 *	@param $values	Associative array with the field names and their values to insert.
		 *
		 *	@returns	The insert SQL statement
		 *
		 *	@internal
		 */
		function _createSqlInsert( $table, $values ) {

			// Check if there are values
			if ( empty( $table ) ) {
				trigger_error( 'No table specified for the INSERT statement.', YD_ERROR );
			}

			// Get the list of field names and values
			$ifields = array();
			$ivalues = array();
			foreach ( $values as $key=>$value ) {
				if ( substr( $key, 0, 1 ) != '_' ) {
					array_push( $ifields, $key );
					if ( is_null( $value ) ) {
						array_push( $ivalues, 'NULL' );
					} else {
						array_push( $ivalues, $this->sqlString( $value ) );
					}
				}
			}

			// Check if there are values
			if ( sizeof( $ifields ) == 0 ) {
				trigger_error( 'No values were submitted for the INSERT statement.', YD_ERROR );
			}

			// Convert the fields and values to a string
			$ifields = implode( ',', $ifields );
			$ivalues = implode( ',', $ivalues );

			// Create the SQL statement
			return 'INSERT INTO ' . $table . ' (' . $ifields . ') VALUES (' . $ivalues . ')';

		}

		/**
		 *	This function will create an SQL update statement from the specified array, table name and where clause.
		 *
		 *	@remarks
		 *		All the field names that start with an underscore will not be used.
		 *
		 *	@param $table	The table to insert the data into.
		 *	@param $values	Associative array with the field names and their values to insert.
		 *	@param $where	The where statement to execute.
		 *
		 *	@returns	The insert SQL statement
		 *
		 *	@internal
		 */
		function _createSqlUpdate( $table, $values, $where='' ) {

			// Check if there are values
			if ( empty( $table ) ) {
				trigger_error( 'No table specified for the INSERT statement.', YD_ERROR );
			}

			// Get the list of field names and values
			$uvalues = array();
			foreach ( $values as $key=>$value ) {
				if ( substr( $key, 0, 1 ) != '_' ) {
					if ( is_null( $value ) ) {
						array_push( $uvalues, $key . "=" . 'NULL' );
					} else {
						array_push( $uvalues, $key . "=" . $this->sqlString( $value ) );
					}
				}
			}

			// Check if there are values
			if ( sizeof( $uvalues ) == 0 ) {
				trigger_error( 'No values were submitted for the UPDATE statement.', YD_ERROR );
			}

			// Convert the fields and values to a string
			$uvalues = implode( ',', $uvalues );

			// Create the SQL statement
			$sql =  'UPDATE ' . $table . ' SET ' . $uvalues;
			if ( ! empty( $where ) ) { $sql .= ' WHERE ' . $where; }

			// Return the SQL
			return $sql;

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
		}

		/**
		 *	This function will convert the given record so that all the keys are lowercase.
		 *
		 *	@param $array	The array to convert.
		 *
		 *	@returns	The original array with all the field names as lowercase.
		 *
		 *	@internal
		 */
		function _lowerKeyNames( $array ) {
			if ( $array ) {
				return array_change_key_case( $array, CASE_LOWER );
			} else {
				return $array;
			}
		}

		/**
		 *	This function will log the SQL statement to the debug log and keep track of the number of queries that were
		 *	executed.
		 *
		 *	@param $sql	The SQL statement to log.
		 *
		 *	@internal
		 */
		function _logSql( $sql ) {
			array_push( $GLOBALS['YD_SQL_QUERY'], $sql );
		}

	}

	/**
	 *	This class defines a database driver for MySQL.
	 */
	class YDDatabaseDriver_mysql extends YDDatabaseDriver {

		/**
		 *	This is the class constructor for the YDDatabaseDriver_mysql class.
		 *
		 *	@param $db		Name of the database.
		 *	@param $user	(optional) User name to use for the connection.
		 *	@param $pass	(optional) Password to use for the connection.
		 *	@param $host	(optional) Host name to use for the connection.
		 *	@param $host	(optional) Host name to use for the connection.
		 *	@param $options	(optional) Options to pass to the driver.
		 */
		function YDDatabaseDriver_mysql( $db, $user='', $pass='', $host='', $options=array() ) {
			$this->YDDatabaseDriver( $db,  $user, $pass, $host, $options );
		}

		/**
		 *	This function will check if the server supports this database type.
		 *
		 *	@returns	Boolean indicating if the database type is supported by the server.
		 */
		function isSupported() {
			return extension_loaded( 'mysql' );
		}

		/**
		 *	This function will return the version of the database server software.
		 *
		 *	@returns	The version of the database server software.
		 */
		function getServerVersion() {
			$this->connect();
			return 'MySQL ' . mysql_get_server_info();
		}

		/**
		 *	Function that makes the actual connection.
		 */
		function connect() {
			if ( $this->_conn == null ) {
				$conn = @mysql_connect( $this->_host, $this->_user, $this->_pass );
				if ( ! $conn ) {
					trigger_error( mysql_error(), YD_ERROR );
				}
				if ( ! @mysql_select_db( $this->_db, $conn ) ) {
					trigger_error( mysql_error( $conn ), YD_ERROR );
				}
				$this->_conn = $conn;
			} else {
				@mysql_ping( $this->_conn );
			}
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
			$record = $this->_lowerKeyNames( mysql_fetch_assoc( $result ) );
			mysql_free_result( $result );
			return $record;
		}

		/**
		 *	This function will execute the SQL statement and return the records as an associative array.
		 *
		 *	@param $sql	The SQL statement to use.
		 *
		 *	@returns	The records matching the SQL statement as an associative array.
		 */
		function getRecords( $sql ) {
			$result = & $this->_connectAndExec( $sql );
			$dataset = array();
			while ( $line = $this->_lowerKeyNames( mysql_fetch_assoc( $result ) ) ) {
				array_push( $dataset, $line );
			}
			mysql_free_result( $result );
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
			return mysql_affected_rows( $this->_conn );
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
			return mysql_num_rows( $result );
		}

		/**
		 *	This function will insert the specified values in to the specified table.
		 *
		 *	@param $table	The table to insert the data into.
		 *	@param $values	Associative array with the field names and their values to insert.
		 *
		 *	@returns	The ID of the last insert.
		 */
		function executeInsert( $table, $values ) {
			$sql = $this->_createSqlInsert( $table, $values );
			$result = & $this->_connectAndExec( $sql );
			return mysql_insert_id( $this->_conn );
		}

		/**
		 *	This function will close the database connection.
		 */
		function close() {
			if ( $this->_conn != null ) {
				$this->_conn = null;
				@mysql_close( $this->_conn );
			}
		}

		/**
		 *	This function will escape a string so that it's safe to include it in an SQL statement.
		 *
		 *	@param $string	The string to escape.
		 *
		 *	@returns	The escaped string.
		 */
		function string( $string ) {
			if ( is_string( $string ) ) {
				if ( strtolower( $string ) != 'null' ) {
					return mysql_escape_string( $string );
				}
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
			$this->_logSql( $sql );
			$this->connect();
			$result = @mysql_query( $sql, $this->_conn );
			if ( ! $result ) { 
				trigger_error( '[' . mysql_errno( $this->_conn ) . '] ' . mysql_error( $this->_conn ), YD_ERROR );
			}
			return $result;
		}

	}

?>