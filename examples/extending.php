<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDDebugUtil.php' );

	// Class definition (custom request handler)
	class customBaseRequest extends YDRequest {

		// Class constructor
		function customBaseRequest() {
			$this->YDRequest();
		}

	}

	// Class definition (handles the actual request)
	class extending extends customBaseRequest {

		// Class constructor
		function extending() {
			$this->customBaseRequest();
		}

		// Default action
		function actionDefault() {
			echo( '<p>ancestors of this request:</p>' );
			YDDebugUtil::dump( YDObjectUtil::getAncestors( $this ) );
		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
