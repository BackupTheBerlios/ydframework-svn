<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDBBCode.php' );
	require_once( 'YDFileSystem.php' );

	// Class definition
	class bbcode extends YDRequest {

		// Class constructor
		function bbcode() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {
			
			// The original data
			$file = new YDFSFile( 'bbcode.txt' );
			$data = $file->getContents();

			// The converter
			$conv = new YDBBCode();

			// Show the converted data
			echo( '<pre>' . htmlentities( $data ) . '</pre>' );
			echo( '<pre>' . htmlentities( $conv->toHtml( $data ) ) . '</pre>' );
			echo( '<p>' . $conv->toHtml( $data, true, false ) . '</p>' );

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
