<?php

	require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	require_once( 'MyLoginRequest.php' );

	class index extends MyLoginRequest {

		function index() {
			$this->MyLoginRequest();
		}

		function actionDefault() {
			$this->template->display();
		}

	}

	require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_process.php' );

?>
