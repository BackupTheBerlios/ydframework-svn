<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	
	// Class definition
	class locale extends YDRequest {

		// Class constructor
		function locale() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// Define the date format used for testing
			$fmt = '%A, %d %B %Y';
		
			// Default locale is always en
			YDDebugUtil::dump( YDLocale::get(), 'Current locale:' );
			YDDebugUtil::dump( strftime( $fmt ), 'strftime test' );

			// Set the locale to English
			YDLocale::set( 'eng' );
			YDDebugUtil::dump( YDLocale::get(), 'Current locale:' );
			YDDebugUtil::dump( strftime( $fmt ), 'strftime test' );

			// Set the locale to Dutch
			YDLocale::set( 'nl' );
			YDDebugUtil::dump( YDLocale::get(), 'Current locale:' );
			YDDebugUtil::dump( strftime( $fmt ), 'strftime test' );

			// Set the locale to Portugese
			YDLocale::set( 'ptg' );
			YDDebugUtil::dump( YDLocale::get(), 'Current locale:' );
			YDDebugUtil::dump( strftime( $fmt ), 'strftime test' );

			// Set the locale to Italian
			YDLocale::set( 'ita' );
			YDDebugUtil::dump( YDLocale::get(), 'Current locale:' );
			YDDebugUtil::dump( strftime( $fmt ), 'strftime test' );

		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>
