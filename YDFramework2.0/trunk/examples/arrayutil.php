<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );

	// Class definition
	class arrayutil extends YDRequest {

		// Class constructor
		function arrayutil() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// Convert to nested
			$array = array(
				array( 'id' => 1, 'name' => 'Pieter', 'group' => 'admin' ),
				array( 'id' => 2, 'name' => 'Fiona', 'group' => 'admin' ),
				array( 'id' => 3, 'name' => 'Bert', 'group' => 'user' ),
				array( 'id' => 3, 'name' => 'Jan', 'group' => 'guest' ),
			);
			YDDebugUtil::dump( $array, 'Original array' );

			// Convert to nested array
			YDDebugUtil::dump(
				YDArrayUtil::convertToNested( $array, 'group' ), 'YDArrayUtil::convertToNested( $array, \'group\' )'
			);

			// The original array
			$array = array( 1, 2, 3, 4, 5, 6, 7 );
			YDDebugUtil::dump( $array, 'Original array' );

			// Convert to a three column table
			YDDebugUtil::dump( YDArrayUtil::convertToTable( $array, 3 ), 'YDArrayUtil::convertToTable( $array, 3 )' );

			// Convert to a three column table
			YDDebugUtil::dump(
				YDArrayUtil::convertToTable( $array, 3, true ), 'YDArrayUtil::convertToTable( $array, 3, true )'
			);

			// Convert to a three column table
			YDDebugUtil::dump( YDArrayUtil::convertToTable( $array, 2 ), 'YDArrayUtil::convertToTable( $array, 2 )' );

			// Convert to a three column table
			YDDebugUtil::dump(
				YDArrayUtil::convertToTable( $array, 2, true ), 'YDArrayUtil::convertToTable( $array, 2, true )'
			);

			// Test for errors
			YDDebugUtil::dump( 
				YDArrayUtil::convertToTable( $array, 'a', true ), 'YDArrayUtil::convertToTable( $array, "a", true )' 
			);

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>