<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );

	/**
	 *	This class defines a database object.
	 */
	class YDDatabase extends YDBase {

		/**
		 *	This is the class constructor for the YDDatabase class.
		 *
		 *	@param $driver	Name of the database driver.
		 *	@param $db		Database name to use for the connection.
		 *	@param $user	(optional) User name to use for the connection.
		 *	@param $pass	(optional) Password to use for the connection.
		 *	@param $host	(optional) Host name to use for the connection.
		 *	@param $options	(optional) Options to pass to the driver.
		 */
		function YDDatabase( $driver, $db, $user='', $pass='', $host='' ) {

			// Initialize YDBase
			$this->YDBase();

			// The list of known drivers
			$this->_regDrivers = array();

			// Register the standard drives
			$this->registerDriver( 'mysql', 'YDDatabaseDriver_mysql', 'YDDatabaseDriver_mysql.php' );
			$this->registerDriver( 'oracle', 'YDDatabaseDriver_oracle', 'YDDatabaseDriver_oracle.php' );
			$this->registerDriver( 'postgres', 'YDDatabaseDriver_postgres', 'YDDatabaseDriver_postgres.php' );
			$this->registerDriver( 'sqlite', 'YDDatabaseDriver_sqlite', 'YDDatabaseDriver_sqlite.php' );

			// Check if the driver exists
			if ( ! array_key_exists( strtolower( $driver ), $this->_regDrivers ) ) {
				YDFatalError( 'Unsupported database type: "' . $driver . '".' );
			}

			// Include the driver
			if ( ! empty( $this->_regDrivers[ strtolower( $driver ) ]['file'] ) ) {
				require_once( $this->_regDrivers[ strtolower( $driver ) ]['file'] );
			}

			// Check if the driver is supported
			if ( ! call_user_func( array( $this->_regDrivers[ strtolower( $driver ) ]['class'], 'isSupported' ) ) ) {
				YDFatalError( 'Unsupported database type: "' . $driver . '". Extension is not loaded.' );
			}

			// Make a new connection object and return it
			$className = $this->_regDrivers[ strtolower( $driver ) ]['class'];
			$this = new $className( $db, $user, $pass, $host );

		}

		/**
		 *	This function will register a new database driver.
		 *
		 *	@param $name	Name of the driver.
		 *	@param $class	The class name of the driver definition.
		 *	@param $file	(optional) The file containing the class definition for this driver.
		 */
		function registerDriver( $name, $class, $file='' ) {
			$this->_regDrivers[ strtolower( $name ) ] = array( 'class' => $class, 'file' => $file );
		}

	}

?>