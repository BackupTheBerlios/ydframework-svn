<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );

	// Class definition
	class auth_ipcheck extends YDRequest {

		// Class constructor
		function auth_ipcheck() {
			$this->YDRequest();
			$this->setRequiresAuthentication( true );
		}

		// Default action
		function actionDefault() {
			$this->setVar( 'allowed', true );
			$this->outputTemplate();
		}

		// Default action
		function actionTest() {
			$this->outputTemplate();
		}

		// Check the authentication
		function isAuthenticated() {
			if ( $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ) {
				return true;
			} else {
				return false;
			}
		}

		// Redirect to the login if the authentication failed
		function authenticationFailed() {
			echo( '<b>ACCESS DENIED</b><br>Only localhost access is allowed.' );
		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
