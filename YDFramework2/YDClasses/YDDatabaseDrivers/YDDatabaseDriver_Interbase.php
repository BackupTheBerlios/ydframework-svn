YDDatabaseDriver_Interbase<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );

	/**
	 */
	class YDDatabaseDriver_Interbase extends YDBase {

		/**
		 */
		function YDDatabaseDriver_Interbase() {

			// Initialize YDBase
			$this->YDBase();

		}

	}

?>