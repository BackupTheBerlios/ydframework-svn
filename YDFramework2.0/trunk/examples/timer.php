<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDUtil.php' );

	// Class definition
	class timer extends YDRequest {

		// Class constructor
		function timer() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {
			YDGlobalTimerMarker( 'First marker' );
			sleep( 0.4 );
			YDGlobalTimerMarker( 'Second marker' );
			sleep( 0.4 );
			YDGlobalTimerMarker( 'Third marker' );
		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>