<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );

	// Class definition
	class index extends YDRequest {

		// Class constructor
		function index() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {
			$this->redirect( 'examples/index.php' );
		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/YDFramework2/YDF2_process.php' );

?>
