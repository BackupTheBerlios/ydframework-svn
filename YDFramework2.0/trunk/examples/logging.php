<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDLog.php' );
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDFileSystem.php' );

	// Include all log messages
	YDConfig::set( 'YD_LOG_LEVEL', 4 );
	YDConfig::set( 'YD_LOG_WRAPLINES', true );

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
			switch ( YDConfig::get( 'YD_LOG_LEVEL' ) ) {
				case YD_LOG_DEBUG:
					echo( 'YD_LOG_DEBUG' );
					break;
				case YD_LOG_INFO:
					echo( 'YD_LOG_INFO' );
					break;
				case YD_LOG_WARNING:
					echo( 'YD_LOG_WARNING' );
					break;
				default:
					echo( 'YD_LOG_ERROR' );
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

			echo( 'Testing PlainFunction<br/>' );
			PlainFunction();

			echo( 'Adding very long info message<br/>' );
			YDLog::info( 'this is a very long info message and should break up over several lines in the logfile. Each line should be a separate info message in the logfile if everything goes correctly.' );

			echo( '<br/><a href="' . YD_SELF_SCRIPT . '?do=showlog">show logfile</a>' );

		}

		// Function to show the logfile
		function actionShowLog() {
			$file = new YDFSFile( YDConfig::get( 'YD_LOG_FILE' ) );
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