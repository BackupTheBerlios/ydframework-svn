<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDLog.php' );
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDFileSystem.php' );

	// Plain function
	function PlainFunction() {
		YDLog::info( 'Message from PlainFunction' );
	}

	// Class definition
	class logging extends YDRequest {

		// Class constructor
		function logging() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			echo( 'Log level: ' );
			switch ( YD_LOG_LEVEL ) {
				case 4:
					echo( 'YD_DEBUG' );
					break;
				case 3:
					echo( 'YD_INFO' );
					break;
				case 2:
					echo( 'YD_WARNING' );
					break;
				default:
					echo( 'YD_ERROR' );
					break;
			}
			echo( '<br/><br/>' );

			echo( 'Clearing the logfile<br/>' );
			YDLog::clear();

			echo( 'Adding debug message<br/>' );
			YDLog::debug( 'debug message' );

			echo( 'Adding info message<br/>' );
			YDLog::info( 'info message' );

			echo( 'Adding warning message<br/>' );
			YDLog::warning( 'warning message' );

			echo( 'Adding error message<br/>' );
			YDLog::error( 'error message' );

			echo( 'Testing PlainFunction' );
			PlainFunction();

			echo( '<br/><a href="' . YD_SELF_SCRIPT . '?do=showlog">show logfile</a>' );

		}

		// Function to show the logfile
		function actionShowLog() {
			$file = new YDFSFile( YD_LOG_FILE );
			$data = $file->getContents();

			if ( substr( $data, 0, 5 ) == '<?xml' ) {
				header( 'Content-type: text/xml' );
			} else {
				header( 'Content-type: text/plain' );
			}
			echo( $data );
			die();
		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>