<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDDatabaseDriver.php' );

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
				if ( ! $conn ) { YDFatalError( mysql_error() ); }
				if ( ! @mysql_select_db( $this->_db, $conn ) ) { YDFatalError( mysql_error( $conn ) ); }
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
			return mysql_num_rows( $this->_conn );
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
			return mysql_escape_string( $string );
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
			if ( ! $result ) { YDFatalError( mysql_error( $conn ) ); }
			return $result;
		}

	}

?>