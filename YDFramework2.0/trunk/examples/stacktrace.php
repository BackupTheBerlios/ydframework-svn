<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );

	// Class definition
	class stacktrace extends YDRequest {

		// Class constructor
		function stacktrace() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// Get the debug URI
			if ( strpos( YD_SELF_URI, 'YD_DEBUG=1' ) === false ) {
				echo( 'You need to run in <a href="' . YD_SELF_SCRIPT . '?YD_DEBUG=1">debugging mode</a> to see something.' );
			} else {
				echo( 'If you go <a href="' . YD_SELF_SCRIPT . '">out of the debugging mode</a>, the stack trace will be hidden.' );
			}

			// Get the file object for the current file
			YDStackTrace();

		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>