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

	YDInclude( 'YDDatabase.php' );

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
		 *	This function will return the version of the database server software.
		 *
		 *	@returns	The version of the database server software.
		 */
		function getServerVersion() {
			$this->connect();
			return 'SQLite ' . sqlite_libversion();
		}

		/**
		 *	Function that makes the actual connection.
		 */
		function connect() {
			if ( $this->_conn == null ) {
				$conn = @sqlite_open( $this->_db, 0666, $error );
				if ( ! $conn ) {
					trigger_error( $error, YD_ERROR );
				}
				$this->_conn = $conn;
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
			return $this->_lowerKeyNames( sqlite_fetch_array( $result, SQLITE_ASSOC ) );
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
			while ( $line = $this->_lowerKeyNames( sqlite_fetch_array( $result, SQLITE_ASSOC ) ) ) {
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
		function string( $string ) {
			if ( is_string( $string ) ) {
				if ( strtolower( $string ) != 'null' ) {
					return sqlite_escape_string( $string );
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
			$result = @sqlite_query( $sql, $this->_conn );
			if ( ! $result ) {
				trigger_error( sqlite_error_string( sqlite_last_error( $this->_conn ) ), YD_ERROR );
			}
			return $result;
		}

	}

?>