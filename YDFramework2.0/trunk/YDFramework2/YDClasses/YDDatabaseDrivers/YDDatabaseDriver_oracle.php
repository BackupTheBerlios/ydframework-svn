<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	YDInclude( 'YDDatabase.php' );

	/**
	 *	This class defines a database driver for Oracle8i using the OCI8 interface.
	 */
	class YDDatabaseDriver_oracle extends YDDatabaseDriver {

		/**
		 *	This is the class constructor for the YDDatabaseDriver_oracle class.
		 *
		 *	@param $db		Name of the database.
		 *	@param $user	(optional) User name to use for the connection.
		 *	@param $pass	(optional) Password to use for the connection.
		 *	@param $host	(optional) Host name to use for the connection.
		 *	@param $host	(optional) Host name to use for the connection.
		 *	@param $options	(optional) Options to pass to the driver.
		 */
		function YDDatabaseDriver_oracle( $db, $user='', $pass='', $host='', $options=array() ) {
			$this->YDDatabaseDriver( $db,  $user, $pass, $host, $options );
			$this->_NLS_DATE_FORMAT = 'RRRR-MM-DD HH24:MI:SS';
			$this->_fmtDate = 'Y-m-d';
			$this->_fmtTimeStamp = 'Y-m-d, h:i:s A';
		}

		/**
		 *	This function will check if the server supports this database type.
		 *
		 *	@returns	Boolean indicating if the database type is supported by the server.
		 */
		function isSupported() {
			return extension_loaded( 'oci8' );
		}

		/**
		 *	This function will return the version of the database server software.
		 *
		 *	@returns	The version of the database server software.
		 */
		function getServerVersion() {
			$this->connect();
			return 'Oracle ' . ociserverversion( $this->_conn );
		}

		/**
		 *	Function that makes the actual connection.
		 */
		function connect() {
			if ( $this->_conn == null ) {
				$conn = @OCILogon( $this->_user, $this->_pass, $this->_db );
				if ( ! $conn ) { 
					$error = ocierror( $conn );
					trigger_error( $error['message'], YD_ERROR );
				}
				$this->_conn = $conn;
				$stmt = OCIParse(
					$this->_conn, 'ALTER SESSION SET NLS_DATE_FORMAT=\'' . $this->_NLS_DATE_FORMAT . '\''
				);
				@OCIExecute( $stmt );
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
			$result = $this->_connectAndExec( $sql );
			ocifetchinto( $result, $record, OCI_ASSOC );
			OCIFreeStatement( $result );
			return $this->_lowerKeyNames( $record );
		}

		/**
		 *	This function will execute the SQL statement and return the records as an associative array.
		 *
		 *	@param $sql	The SQL statement to use.
		 *
		 *	@returns	The records matching the SQL statement as an associative array.
		 */
		function getRecords( $sql ) {
			$result = $this->_connectAndExec( $sql );
			$dataset = array();
			while ( ocifetchinto( $result, $line, OCI_ASSOC ) ) {
				array_push( $dataset, $this->_lowerKeyNames( $line ) );
			}
			OCIFreeStatement( $result );
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
			$result = $this->_connectAndExec( $sql );
			return ocirowcount( $result );
		}

		/**
		 *	This function will return the number of rows matched by the SQL query.
		 *
		 *	@param $sql	The SQL statement to use.
		 *
		 *	@returns	The number of rows matched by the SQL query.
		 */
		function getMatchedRowsNum( $sql ) {
			$result = $this->_connectAndExec( $sql );
			return ocirowcount( $result );
		}

		/**
		 *	This function will close the database connection.
		 */
		function close() {
			if ( $this->_conn != null ) {
				$this->_conn = null;
				@OCILogoff( $this->_conn );
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
			if ( strtolower( $string ) == 'null' ) {
				return 'null';
			}
			if ( strtolower( substr( $string, 0, 7 ) ) == 'to_date' ) {
				return $string;
			}
			return $this->_fmtQuote . $this->string( $string ) . $this->_fmtQuote;
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
			$date = $this->_getDateOrTime( $date, $this->_fmtDate );
			if ( $date == 'null' ) {
				return $date;
			} else {
				return 'TO_DATE ( \'' . $date . '\', \'YYYY-MM-DD\' )';
			}
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
			$time = $this->_getDateOrTime( $time, $this->_fmtTimeStamp );
			if ( $time == 'null' ) {
				return $time;
			} else {
				return 'TO_DATE ( \'' . $time . '\', \'RRRR-MM-DD, HH:MI:SS AM\')';
			}
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
		function _connectAndExec( $sql ) {
			$this->_logSql( $sql );
			$this->connect();
			$stmt = OCIParse( $this->_conn, $sql );
			if ( ! $stmt ) {
				$error = ocierror( $stmt );
				trigger_error( $error['message'], YD_ERROR );
			}
			$result = @OCIExecute( $stmt );
			if ( ! $result ) {
				$error = ocierror( $stmt );
				if ( ! empty( $error['sqltext'] ) ) { $error['message'] .= ' (SQL: ' . $error['sqltext'] . ')'; }
				trigger_error( $error['message'], YD_ERROR );
			}
			return $stmt;
		}

	}

?>