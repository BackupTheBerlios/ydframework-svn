<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );

	// Class definition
	class phpinfo extends YDRequest {

		// Class constructor
		function phpinfo() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {
			phpinfo();
		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
