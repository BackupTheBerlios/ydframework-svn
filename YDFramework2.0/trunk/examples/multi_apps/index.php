<?php

	// Include our custom init file
	require_once( dirname( __FILE__ ) . '/_includes/YDF2_include.php' );

	// Define the request class
	class index extends MyAppRequest {

		// Class constructor
		function index() {

			// Initialize the parent
			$this->MyAppRequest();

		}

		// The default action
		function actionDefault() {

			// Assign a variable to the template
			$this->tpl->assign( 'title', __FILE__ );

			// Display the template
			$this->tpl->display();

		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>