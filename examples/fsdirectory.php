<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDFSDirectory.php' );
	require_once( 'YDDebugUtil.php' );

	// Class definition
	class fsdirectoryRequest extends YDRequest {

		// Class constructor
		function fsdirectoryRequest() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// Get the directory object for the current directory
			$dir = new YDFSDirectory( dirname( __FILE__ ) );

			// Dump the object
			echo( dirname( __FILE__ ) );
			YDDebugUtil::dump( $dir );

			// All files in the directory
			echo( 'Full file list' );
			YDDebugUtil::dump( $dir->getContents() );

			// PHP files in the directory
			echo( 'List of PHP files' );
			YDDebugUtil::dump( $dir->getContents( '*.php' ) );

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>