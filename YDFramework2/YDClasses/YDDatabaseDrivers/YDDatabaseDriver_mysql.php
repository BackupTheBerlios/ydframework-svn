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
		 */
		function YDDatabaseDriver_mysql( $user, $pass, $db, $host='' ) {

			// Initialize YDDatabaseDriver
			$this->YDDatabaseDriver(  $user, $pass, $db, $host='' );

		}

	}

?>