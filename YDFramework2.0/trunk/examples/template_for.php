<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDDatabase.php' );

	// Class definition
	class template_for extends YDRequest {

		// Class constructor
		function template_for() {

			// Initialize the parent
			$this->YDRequest();

			// Initialize the template
			$this->tpl = new YDTemplate();

		}

		// Default action
		function actionDefault() {

			// Display the template
			$this->tpl->display();

		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>
