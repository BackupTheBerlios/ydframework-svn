<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );

	// Class definition
	class constants extends YDRequest {

		// Class constructor
		function constants() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {
			$this->outputTemplate();
		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>