<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Include our own request
	require_once( 'MyLoginRequest.php' );

	// Class definition
	class userinfo extends MyLoginRequest {

		// Class constructor
		function userinfo() {
			$this->MyLoginRequest();
		}

		// Default action
		function actionDefault() {
			$this->outputTemplate();
		}

	}

	// Standard include
	require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_process.php' );

?>
