<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplateSmarty.php' );

	// Class definition
	class index extends YDRequest {

		// Class constructor
		function index() {
			$this->YDRequest();
			$this->template = new YDTemplate();
		}

		// Default action
		function actionDefault() {
			$this->template->display();
		}

		// Function to show the highlighted source file
		function actionSource() {

			// Get the basename of the file
			$file = $_GET['id'];

			// Filepath should start with the directory of this file
			$basePath = strtolower( dirname( __FILE__ ) );
			$filePath = strtolower( dirname( realpath( $_GET['id'] ) ) );

			// Check if the file is in the right directory
			if ( substr( $filePath, 0, strlen( $basePath ) ) != $basePath ) {
				$this->forward( 'default' );
				return;
			}

			// Check if the file exists
			if ( ! is_file( $file ) ) {
				$this->forward( 'default' );
				return;
			}

			// Show the highlighted source
			$this->template->assign( 'file', $file );
			$this->template->assign( 'source', highlight_file( $file, 1 ) );

			// Output the template
			$this->template->display();

		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>
