<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );

	/**
	 */
	class YDDatabase extends YDBase {

		/**
		 *	This is the class constructor of the YDDatabase class.
		 */
		function YDDatabase() {

			// Initialize YDBase
			$this->YDBase();

		}

	}

?>