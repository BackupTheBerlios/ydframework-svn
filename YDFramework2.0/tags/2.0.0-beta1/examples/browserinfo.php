<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDBrowserInfo.php' );

	// Class definition
	class browserinfo extends YDRequest {

		// Class constructor
		function browserinfo() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {
			$this->setVar( 'browser', new YDBrowserInfo() );
			$this->outputTemplate();
		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
