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
		 *	Function that makes the actual connection.
		 */
		function connect() {
		}

		function getValue( $sql ) {
		}

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

		// mysql_affected_rows
		function executeSql( $sql ) {
		}

		// mysql_num_rows
		function getMatchedRowsNum( $sql ) {
		}

		function executeInsert( $table, $values ) {
		}

		function executeUpdate( $table, $values, $where ) {
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

	}

?>