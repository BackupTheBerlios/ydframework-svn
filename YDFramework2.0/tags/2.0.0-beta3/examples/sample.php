<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDTemplate.php' );

	// Class definition
	class sample extends YDRequest {

		// Class constructor
		function sample() {
			$this->YDRequest();
			$this->template = new YDTemplate();
		}

		// Default action
		function actionDefault() {
			$this->template->assign( 'title', 'sample1Request::actionDefault' );
			$this->template->display();
		}

		// Edit action
		function actionEdit() {
			$this->template->assign( 'title', 'sample1Request::actionEdit' );
			$this->template->display();
		}

		// Browser information
		function actionBrowserInfo() {
			$this->template->assign( 'browser', $this->browser );
			$this->template->display();
		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
