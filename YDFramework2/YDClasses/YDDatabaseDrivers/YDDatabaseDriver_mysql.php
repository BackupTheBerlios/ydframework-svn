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
		 *	Function that makes the actual connection.
		 */
		function connect() {
			if ( $this->_conn == null ) {
				$conn = @mysql_connect( $this->_host, $this->_user, $this->_pass );
				if ( ! $conn ) { YDFatalError( mysql_error() ); }
				if ( ! @mysql_select_db( $this->_db, $conn ) ) { YDFatalError( mysql_error( $conn ) ); }
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
			$dataset = array();
			$result = mysql_query( $sql, $this->_conn );
			if ( ! $result ) { YDFatalError( mysql_error( $conn ) ); }
			while ( $line = mysql_fetch_assoc( $result ) ) {
				array_push( $dataset, $line );
			}
			mysql_free_result( $result );
			return $dataset;
		}

		/**
		 *	This function will close the database connection.
		 */
		function close() {
			if ( $this->_conn != null ) {
				@mysql_close( $this->_conn );
			}
		}

	}

?>