<?php

	require_once( 'YDRequest.php' );
	require_once( 'YDTemplate.php' );

	class MyLoginRequest extends YDRequest {

		function MyLoginRequest() {
			$this->YDRequest();
			$this->setRequiresAuthentication( true );
			$this->template = new YDTemplate();
		}

		function actionDefault() {
			$this->template->display();
		}

		function isAuthenticated() {
			if ( isset( $_SERVER['PHP_AUTH_USER'] ) ) {
				if ( $_SERVER['PHP_AUTH_USER'] == 'pieter' && $_SERVER['PHP_AUTH_PW'] == 'kermit' ) {
					$_SESSION['usrName'] = $_SERVER['PHP_AUTH_USER'];
					return true;
				}
			}
			return false;
		}

		function authenticationFailed() {
		   header( 'WWW-Authenticate: Basic realm="My Realm"' );
		   header( 'HTTP/1.0 401 Unauthorized');
		   echo( 'Text to send if user hits Cancel button' );
		   die();
		}

	}

?>
