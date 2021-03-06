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
	 *	This class defines a database driver for PostgreSQL.
	 */
	class YDDatabaseDriver_postgres extends YDDatabaseDriver {

		/**
		 *	This is the class constructor for the YDDatabaseDriver_postgres class.
		 *
		 *	@param $db		Name of the database.
		 *	@param $user	(optional) User name to use for the connection.
		 *	@param $pass	(optional) Password to use for the connection.
		 *	@param $host	(optional) Host name to use for the connection.
		 *	@param $host	(optional) Host name to use for the connection.
		 *	@param $options	(optional) Options to pass to the driver (associative array).
		 */
		function YDDatabaseDriver_postgres( $db, $user='', $pass='', $host='', $options=array() ) {
			$this->YDDatabaseDriver( $db,  $user, $pass, $host, $options );
			$this->_fmtTimeStamp = 'Y-m-d G:i:s';
		}

		/**
		 *	This function will check if the server supports this database type.
		 *
		 *	@returns	Boolean indicating if the database type is supported by the server.
		 */
		function isSupported() {
			return extension_loaded( 'pgsql' );
		}

		/**
		 *	This function will return the version of the database server software.
		 *
		 *	@returns	The version of the database server software.
		 */
		function getServerVersion() {
			return $this->getValue( 'select version()' );
		}

		/**
		 *	Function that makes the actual connection.
		 */
		function connect() {
			if ( $this->_conn == null ) {
				$attribs = $this->_options;
				$attribs['dbname'] = $this->_db;
				$attribs['user'] = $this->_user;
				$attribs['password'] = $this->_pass;
				$attribs['host'] = $this->_host;
				$connstr = array();
				foreach ( $attribs as $key=>$value ) {
					if ( ! empty( $value ) ) {
						array_push( $connstr, $key . '=' . $value );
					}
				}
				$connstr = implode( ' ', $connstr );
				$conn = pg_connect( $connstr );
				if ( ! $conn ) {
					trigger_error( pg_last_error( $conn ), YD_ERROR );
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
			return $this->_lowerKeyNames( pg_fetch_assoc( $result ) );
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
			while ( $line = $this->_lowerKeyNames( pg_fetch_assoc( $result ) ) ) {
				array_push( $dataset, $line );
			}
			pg_free_result( $result );
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
			return pg_affected_rows( $result );
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
			return pg_num_rows( $result );
		}

		/**
		 *	This function will close the database connection.
		 */
		function close() {
			if ( $this->_conn != null ) {
				$this->_conn = null;
				@pg_close( $this->_conn );
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
					return pg_escape_string( $string );
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
			$result = @pg_query( $this->_conn, $sql );
			if ( ! $result ) {
				trigger_error( pg_last_error( $conn ), YD_ERROR );
			}
			return $result;
		}

	}

?>