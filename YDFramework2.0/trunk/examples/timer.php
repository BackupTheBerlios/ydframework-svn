<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDBBCode.php' );
	YDInclude( 'YDFileSystem.php' );
	YDInclude( 'YDUtil.php' );

	// Class definition
	class timer extends YDRequest {

		// Class constructor
		function timer() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// The original data
			YDGlobalTimerMarker( 'Reading file' );
			$file = new YDFSFile( 'bbcode.txt' );
			$data = $file->getContents();
			YDGlobalTimerMarker( 'Finished reading file' );

			// The converter
			YDGlobalTimerMarker( 'YDBBCode object' );
			$conv = new YDBBCode();

			// Show the converted data
			YDGlobalTimerMarker( 'Start of conversion' );
			echo( '<pre>' . htmlentities( $data ) . '</pre>' );
			echo( '<pre>' . htmlentities( $conv->toHtml( $data ) ) . '</pre>' );
			echo( '<p>' . $conv->toHtml( $data, true, false ) . '</p>' );

		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>