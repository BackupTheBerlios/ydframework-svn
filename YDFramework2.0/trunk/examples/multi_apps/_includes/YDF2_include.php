<?php

	// Include the Yellow Duck Framework
	require_once( dirname( __FILE__ ) . '/../../../YDFramework2/YDF2_init.php' );

	// Include the classes you need on all pages
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDUtil.php' );
	YDInclude( 'YDFileSystem.php' );

	// Define the constants we want to override for the Framework
	YDConfig::set( 'YD_DEBUG', 1 );

	// Here, we define our custom request class
	class MyAppRequest extends YDRequest {

		// Class constructor
		function MyAppRequest() {

			// Initialize the parent request
			$this->YDRequest();

			// Make the template object available for all requests
			$this->tpl = new YDTemplate();

			// Instantiate a timer as well
			$this->timer = new YDTimer();

		}

	}

?>