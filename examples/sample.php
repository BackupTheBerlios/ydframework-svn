<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );

	// Class definition
	class sampleRequest extends YDRequest {

		// Class constructor
		function sampleRequest() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {
			$this->setVar( 'title', 'sample1Request::actionDefault' );
			$this->outputTemplate();
		}

		// Edit action
		function actionEdit() {
			$this->setVar( 'title', 'sample1Request::actionEdit' );
			$this->outputTemplate();
		}

		// Browser information
		function actionBrowserInfo() {
			$this->setVar( 'browser', $this->browser );
			$this->outputTemplate();
		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
