<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );

	// Class definition
	class index extends YDRequest {

		// Class constructor
		function index() {
			$this->YDRequest();
			$this->template = new YDTemplate();
		}

		// Default action
		function actionDefault() {

			// The source array
			$array = array(
				array(
					'author' => 'Stephen King',
					'title' => 'The Stand'
				),
				array(
					'author' => 'Neal Stephenson',
					'title' => 'Cryptonomicon'
				),
				array(
					'author' => 'Milton Friedman',
					'title' => 'Free to Choose'
				)
			);

			// Set the template variables
			$this->template->assign( 'title', 'This is the title' );
			$this->template->assign( 'books', $array );

			// Output the template
			$this->template->display();

		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>
