<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );
	require_once( 'YDDebugUtil.php' );
	require_once( 'YDObjectUtil.php' );

	/**
	 *	This is the executor class that contains all the logic for executing requests. It will instantiate the request
	 *	class and execute the right functions to get the request processed correctly.
	 */
	class YDExecutor extends YDBase {

		/**
		 *	The class constructor for the YDExecutor class.
		 *
		 *	@param $file	Full path to the file that contains the request.
		 */
		function YDExecutor( $file ) {
			$this->_file = $file;
		}

		/**
		 *	The execute function will start the YDExecutor class and process the request.
		 */
		function execute() {

			// Do nothing if we already processed a request
			if ( defined( 'YD_REQ_PROCESSED' ) ) {
				return;
			}

			// Construct the name of the request class
			$clsName = basename( $this->_file, YD_SCR_EXT ) . 'Request';
			$clsInst = new $clsName();

			// Check if the object a YDRequest object
			if ( ! YDObjectUtil::isSubClass( $clsInst, 'YDRequest' ) ) {
				$ancestors = YDObjectUtil::getAncestors( $clsInst );
				YDFatalError(
					'Class "' . $clsName . '" should be derived from the YDRequest class. Currently, this class has the '
					. 'following ancestors: ' . implode( ' -&gt; ', $ancestors )
				);
			}

			// Check if the class is properly initialized
			if ( $clsInst->isInitialized() != true ) {
				YDFatalError(
					'Class "' . $clsName . '" is not initialized properly. Make  sure loaded the base class YDRequest '
					. 'and initialized it.'
				);
			}

			// Only if authentication is required
			if ( $clsInst->getRequiresAuthentication() ) {
				$result = $clsInst->isAuthenticated();
				if ( $result ) {
					$clsInst->authenticationSucceeded();
				} else {
					$clsInst->authenticationFailed();
					$this->finish();
				}
			}

			// Check if the current action is allowed or not
			$result = $clsInst->isActionAllowed();

			// Execute actionNotAllowed if failed
			if ( $result == false ) {
				$clsInst->actionNotAllowed();
				$this->finish();
			}

			// Process the request
			$clsInst->process();
			$this->finish();

		}

		/**
		 *	This is the function we use for finishing the request.
		 *
		 *	@internal
		 */
		function finish() {

			// Mark that the request is processed
			define( 'YD_REQ_PROCESSED', 1 );

			// Show debugging info if needed
			if ( YD_DEBUG == 1 ) {

				// Include the string utilities
				require_once( 'YDStringUtil.php' );

				// Stop the timer
				$elapsed = $GLOBALS['timer']->getElapsed();

				// Total size of include files
				$includeFiles = get_included_files();

				// Calculate the total size
				$includeFilesSize = 0;
				$includeFilesWithSize = array();
				foreach ( $includeFiles as $key=>$includeFile ) {
					$includeFilesSize += filesize( $includeFile );
					$includeFilesWithSize[ filesize( $includeFile ) ] = realpath( $includeFile );
				}
				$includeFilesSize = YDStringUtil::formatFileSize( $includeFilesSize );

				// Sort the list of include files by file size
				krsort( $includeFilesWithSize );

				// Convert to a string
				$includeFiles = array();
				foreach ( $includeFilesWithSize as $size=>$file ) {
					array_push( $includeFiles, YDStringUtil::formatFileSize( $size ) . "\t  " . $file );
				}

				// Create the debug messages
				$debug = "\n\n";
				$debug .= 'Processing time: ' . $elapsed . ' ms' . "\n\n";
				$debug .= 'Total size include files: ' . $includeFilesSize . "\n\n";
				$debug .= 'Included files: ' . "\n\n\t" . implode( "\n\t", $includeFiles ) . "\n\n";

				// Output the debug message
				YDDebugUtil::debug( $debug );

			}

			// Stop the execution of the request
			die();

		}

	}

?>
