<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

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
			$clsName = basename( $this->_file, YD_SCR_EXT );
			$this->clsInst = new $clsName();

			// Check if the object a YDRequest object
			if ( ! YDObjectUtil::isSubClass( $this->clsInst, 'YDRequest' ) ) {
				$ancestors = YDObjectUtil::getAncestors( $this->clsInst );
				trigger_error(
					'Class "' . $clsName . '" should be derived from the YDRequest class. Currently, this class has the '
					. 'following ancestors: ' . implode( ' -&gt; ', $ancestors ), YD_ERROR
				);
			}

			// Check if the class is properly initialized
			if ( $this->clsInst->isInitialized() != true ) {
				trigger_error(
					'Class "' . $clsName . '" is not initialized properly. Make  sure loaded the base class YDRequest '
					. 'and initialized it.', YD_ERROR
				);
			}

			// Only if authentication is required
			if ( $this->clsInst->getRequiresAuthentication() ) {
				$result = $this->clsInst->isAuthenticated();
				if ( $result ) {
					$this->clsInst->authenticationSucceeded();
				} else {
					$this->clsInst->authenticationFailed();
					$this->finish();
				}
			}

			// Check if the current action is allowed or not
			$result = $this->clsInst->isActionAllowed();

			// Execute actionNotAllowed if failed
			if ( $result == false ) {
				$this->clsInst->actionNotAllowed();
				$this->finish();
			}

			// Process the request
			$this->clsInst->process();
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

			// Show debugging info if needed
			if ( YD_DEBUG == 1 ) {

				// Create the debug messages
				$debug = "\n\n";
				$debug .= 'Processing time: ' . $elapsed . ' ms' . "\n\n";
				$debug .= 'Total size include files: ' . $includeFilesSize . "\n\n";
				$debug .= 'Included files: ' . "\n\n\t" . implode( "\n\t", $includeFiles ) . "\n\n";

				// If there is a database instance
				$debug .= 'Number of SQL queries: ' . sizeof( $GLOBALS['YD_SQL_QUERY'] ) . "\n\n";

				// Add the queries if any
				if ( sizeof( $GLOBALS['YD_SQL_QUERY'] ) > 0 ) {
					$debug .= 'Executed SQL queries: ' . "\n\n";
					foreach ( $GLOBALS['YD_SQL_QUERY'] as $key=>$query ) {
						$debug .= "\t" . ($key+1) . ': ' . trim( $query ) . "\n\n";
					}
				}

				// Output the debug message
				YDDebugUtil::debug( $debug );

			} else {
				
				// Short version
				echo( "\n" . '<!-- ' . $elapsed . ' ms / ' . $includeFilesSize . ' -->' );

			}

			// Stop the execution of the request
			die();

		}

	}

?>
