<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDFileSystem.php' );
	require_once( 'YDDebugUtil.php' );

	// Class definition
	class fsdirectory extends YDRequest {

		// Class constructor
		function fsdirectory() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// Get the directory object for the current directory
			$dir = new YDFSDirectory( dirname( __FILE__ ) );

			// Dump the object
			YDDebugUtil::dump( $dir, dirname( __FILE__ ) );

			// All files in the directory
			YDDebugUtil::dump( $dir->getContents(), '$dir->getContents()' );

			// PHP files in the directory
			YDDebugUtil::dump( $dir->getContents( '*.tpl' ), '$dir->getContents( \'*.tpl\' )' );

			// PHP files in the directory
			YDDebugUtil::dump(
				$dir->getContents( array( '*.jpg', '*.txt' ) ), '$dir->getContents( array( \'*.jpg\', \'*.txt\' ) )'
			);

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>