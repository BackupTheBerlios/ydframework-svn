<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );

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

			// Keep count of the number of executed SQL statements
			$this->_sqlCount = 0;

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
			return $this->_sqlCount;
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
			$record = array_values( $this->getRecord( $sql ) );
			if ( ! $record ) { return false; }
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
			$record = array_values( $this->getRecord( $sql ) );
			if ( ! $record ) { return false; }
			return $record[ $name ];
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
			$sql = $this->_createSqlUpdate( $table, $values );
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
			if ( empty( $table ) ) { YDFatalError( 'No table specified for the INSERT statement.' ); }

			// Get the list of field names and values
			$ifields = array();
			$ivalues = array();
			foreach ( $values as $key=>$value ) {
				if ( substr( $key, 0, 1 ) != '_' ) {
					array_push( $ifields, $key );
					array_push( $ivalues, $this->sqlString( $value ) );
				}
			}

			// Check if there are values
			if ( sizeof( $ifields ) == 0 ) { YDFatalError( 'No values were submitted for the INSERT statement.' ); }

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
			if ( empty( $table ) ) { YDFatalError( 'No table specified for the INSERT statement.' ); }

			// Get the list of field names and values
			$uvalues = array();
			foreach ( $values as $key=>$value ) {
				if ( substr( $key, 0, 1 ) != '_' ) {
					array_push( $uvalues, $key . "=" . $this->sqlString( $value ) );
				}
			}

			// Check if there are values
			if ( sizeof( $uvalues ) == 0 ) { YDFatalError( 'No values were submitted for the UPDATE statement.' ); }

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
			$this->_sqlCount++;
			YDDebugUtil::debug( 'SQL query #' . $this->_sqlCount . ':', $sql );
		}

	}

?>