<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDDatabase.php' );
	require_once( 'YDDebugUtil.php' );

	// Class definition
	class database3Request extends YDRequest {

		// Class constructor
		function database3Request() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// Get the data
			$db = new YDDatabase( 'oracle', 'CREODB_STATLER', 'prinergy_rpt', 'prinergy_rpt' );

			// Output the server version
			YDDebugUtil::dump( $db->getServerVersion(), 'Version:' );

			// Output some queries
			YDDebugUtil::dump( $db->getRecords( 'select * from RPT_Customer_V' ), 'RPT_Customer_V' );

			// Test string escaping
			YDDebugUtil::dump( $db->string( "Pieter's Framework" ), '$db->string' );

			// Show number of queries
			YDDebugUtil::dump( $db->getSqlCount(), 'Number of queries' );

			// Close the database connection
			$db->close();

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
