<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDForm.php' );
	require_once( 'YDEmail.php' );
	require_once( 'YDTemplate.php' );

	// Class definition
	class constantsRequest extends YDRequest {

		// Class constructor
		function constantsRequest() {
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