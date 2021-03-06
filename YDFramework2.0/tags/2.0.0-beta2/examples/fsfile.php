<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDFileSystem.php' );

	// Class definition
	class fsfile extends YDRequest {

		// Class constructor
		function fsfile() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// Get the file object for the current file
			$file = new YDFSFile( __FILE__ );

			// Dump the object
			echo( __FILE__ );
			YDDebugUtil::dump( $file );

			// Dump the object
			echo( '<br>Basename: ' . $file->getBasename() );
			echo( '<br>Extension: ' . $file->getExtension() );
			echo( '<br>Path: ' . $file->getPath() );
			echo( '<br>LastModified: ' . $file->getLastModified() );
			echo( '<br>File size: ' . $file->getSize() );

			// Contents
			YDDebugUtil::dump( $file->getContents(), '$file->getContents()' );

			// Contents
			YDDebugUtil::dump( $file->getContents( 2, 3 ), '$file->getContents( 2, 3 )' );

			// Get the file object for the current file
			$file = new YDFSFile( 'nofile.php' );

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>