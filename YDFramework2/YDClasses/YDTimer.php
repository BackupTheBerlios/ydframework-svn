<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );

	/**
	 *	This is a general timer class that starts counting when it's instantiated, and which returns the elapsed time as
	 *	soon as the finish method is called.
	 */
	class YDTimer extends YDBase {

		/**
		 *	This is the class constructor of the YDTimer class.
		 */
		function YDTimer() {

			// Initialize YDBase
			$this->YDBase();

			// Get the start time
			$this->startTime = $this->_getMicroTime();

		}

		/**
		 *	This function returns the current microtime as a double.
		 *
		 *	@returns	Double containing the current time.
		 *
		 *	@internal
		 */
		function _getMicroTime() {
			$time = explode ( ' ', microtime() );
			return ( doubleval( $time[0] ) + $time[1] );
		}

		/**
		 *	This function will return the number of seconds elapsed since the timer was instantiated.
		 *
		 *	@returns	The total elapsed time
		 */
		function getElapsed() {
			$endTime = $this->_getMicroTime();
			return intval( ( $endTime - $this->startTime ) * 1000 );
		}

	}

?>