<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDDatabaseDriver.php' );

	/**
	 *	This class defines a database driver for Oracle8i using the OCI8 interface.
	 *
	 *	@remark
	 *		This class is by no means stable and still very experimental. Use it at your own risk only!
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
					YDFatalError( $error['message'] );
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
				YDFatalError( $error['message'] );
			}
			$result = @OCIExecute( $stmt );
			if ( ! $result ) {
				$error = ocierror( $stmt );
				if ( ! empty( $error['sqltext'] ) ) { $error['message'] .= ' (SQL: ' . $error['sqltext'] . ')'; }
				YDFatalError( $error['message'] );
			}
			return $stmt;
		}

	}

?>