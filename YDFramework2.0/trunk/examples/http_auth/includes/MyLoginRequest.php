<?php

	// Standard include
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplateSmarty.php' );

	// Class definition
	class MyLoginRequest extends YDRequest {

		// Class constructor
		function MyLoginRequest() {
			$this->YDRequest();
			$this->setRequiresAuthentication( true );
			$this->template = new YDTemplate();
		}

		// Default action
		function actionDefault() {
			$this->template->display();
		}

		// Check if we are authenticated
		function isAuthenticated() {
			if ( isset( $_SERVER['PHP_AUTH_USER'] ) ) {
				if ( $_SERVER['PHP_AUTH_USER'] == 'pieter' && $_SERVER['PHP_AUTH_PW'] == 'kermit' ) {
					$_SESSION['usrName'] = $_SERVER['PHP_AUTH_USER'];
					return true;
				}
			}
			return false;
		}

		// Authentication failed
		function authenticationFailed() {
		   header( 'WWW-Authenticate: Basic realm="My Realm"' );
		   header( 'HTTP/1.0 401 Unauthorized');
		   echo( 'Text to send if user hits Cancel button' );
		   die();
		}

	}

?>
