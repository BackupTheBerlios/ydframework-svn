<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDDatabaseDriver.php' );

	/**
	 *	This class defines a database driver for SQLite.
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
		 *	Function that makes the actual connection.
		 */
		function connect() {
			if ( $this->_conn == null ) {
				$conn = @sqlite_open( $this->_db, 0666, $error );
				if ( ! $conn ) { YDFatalError( $error ); }
				$this->_conn = $conn;
			}
		}

		/**
		 *	This function will execute the SQL statement and return the records as an associative array.
		 *
		 *	@param $sql	The SQL statement to use.
		 *
		 *	@returns	The records matching the SQL statement as an associative array.
		 */
		function getRecords( $sql ) {
			$this->connect();
			$result = sqlite_query( $sql, $this->_conn );
			if ( ! $result ) { YDFatalError( sqlite_error_string( sqlite_last_error( $conn ) ) ); }
			return sqlite_fetch_all( $result, SQLITE_ASSOC );
		}

		/**
		 *	This function will return a single record.
		 *
		 *	@param $sql	The SQL statement to use.
		 *
		 *	@returns	A single record matching the SQL statement.
		 */
		function getRecord( $sql ) {
			$this->connect();
			$dataset = array();
			$result = sqlite_query( $sql, $this->_conn );
			if ( ! $result ) { YDFatalError( sqlite_error_string( sqlite_last_error( $conn ) ) ); }
			return sqlite_fetch_array( $result, SQLITE_ASSOC );
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
		function string( $string ) {
			return sqlite_escape_string( $string );
		}

	}

?>