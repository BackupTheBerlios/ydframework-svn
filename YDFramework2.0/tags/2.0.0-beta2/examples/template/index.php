<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );

	// Class definition
	class index extends YDRequest {

		// Class constructor
		function index() {
			$this->YDRequest();
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
			$this->setVar( 'title', 'This is the title' );
			$this->setVar( 'books', $array );

			// Output the template
			$this->outputTemplate();

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_process.php' );

?>
