<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDDatabase.php' );
	require_once( 'YDDebugUtil.php' );

	// Class definition
	class database2 extends YDRequest {

		// Class constructor
		function database2() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// Get the data
			$db = new YDDatabase( 'sqlite', 'database2.db' );

			// Output the server version
			YDDebugUtil::dump( $db->getServerVersion(), 'Version:' );

			// Output some queries
			YDDebugUtil::dump( $db->getRecords( 'select * from escalations' ), 'escalations' );
			YDDebugUtil::dump( $db->getRecords( 'select * from sqlite_master' ), 'sqlite_master' );

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
