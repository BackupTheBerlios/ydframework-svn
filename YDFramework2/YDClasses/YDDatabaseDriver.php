<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
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
		 *	@param $user	(optional) User name to use for the connection.
		 *	@param $pass	(optional) Password to use for the connection.
		 *	@param $host	(optional) Host name to use for the connection.
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
		 *	@param $sql	The SQL statement to use.
		 *
		 *	@returns	A single value matching the SQL statement.
		 */
		function getValue( $sql ) {
			$record = $this->getRecord( $sql );
			if ( ! $record ) { return false; }
			return $record[0];
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
			return str_replace( "'", "''", $string );
		}

		/**
		 *	This function will create an SQL insert statement from the specified array and table name.
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
			$fields = array();
			$values = array();
			foreach ( $values as $key=>$value ) {
				array_push( $fields, $key );
				array_push( $values, "'" . $this->string( $value ) . "'" );
			}

			// Check if there are values
			if ( sizeof( $fields ) == 0 ) { YDFatalError( 'No values were submitted for the INSERT statement.' ); }

			// Convert the fields and values to a string
			$fields = implode( ',', $fields );
			$values = implode( ',', $values );

			// Create the SQL statement
			return 'INSERT INTO ' . $table . ' (' . $fields . ') VALUES (' . $values . ')';

		}

		/**
		 *	This function will create an SQL update statement from the specified array, table name and where clause.
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
			$values = array();
			foreach ( $values as $key=>$value ) {
				array_push( $values, $key . "='" . $this->string( $value ) . "'" );
			}

			// Check if there are values
			if ( sizeof( $values ) == 0 ) { YDFatalError( 'No values were submitted for the UPDATE statement.' ); }

			// Convert the fields and values to a string
			$values = implode( ',', $values );

			// Create the SQL statement
			$sql =  'UPDATE ' . $table . 'SET ' . $fields;
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