<?php

	require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	require_once( 'MyLoginRequest.php' );

	class index extends MyLoginRequest {

		function index() {
			$this->MyLoginRequest();
		}

		function actionDefault() {
			$this->outputTemplate();
		}

	}

	require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_process.php' );

?>
