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
		 */
		function YDDatabaseDriver( $user, $pass, $db, $host='' ) {

			// Initialize YDBase
			$this->YDBase();

			// Initialize the variables
			$this->_user = $user;
			$this->_pass = $pass;
			$this->_db = $db;
			$this->_host = $host;

		}

		/**
		 *	Function that makes the actual connection.
		 */
		function connect() {
		}

	}

?>