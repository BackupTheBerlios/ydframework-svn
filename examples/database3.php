<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDDatabase.php' );
	require_once( 'YDDebugUtil.php' );

	// Class definition
	class database3 extends YDRequest {

		// Class constructor
		function database3() {
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

			// Output some queries
			YDDebugUtil::dump( $db->getValue( 'select sysdate from dual' ), 'sysdate' );

			// Test string escaping
			YDDebugUtil::dump( $db->string( "Pieter's Framework" ), '$db->string' );

			// Show number of queries
			YDDebugUtil::dump( $db->getSqlCount(), 'Number of queries' );

			// Test timestamps
			YDDebugUtil::dump( $db->getDate(), 'getDate()' );
			YDDebugUtil::dump( $db->getTime(), 'getTime()' );
			YDDebugUtil::dump( $db->getDate( '__NOW__' ), 'getDate( \'__NOW__\' )' );
			YDDebugUtil::dump( $db->getTime( '__NOW__' ), 'getTime( \'__NOW__\' )' );
			YDDebugUtil::dump( $db->getDate( '28-FEB-1977' ), 'getDate( \'28-FEB-1977\' )' );
			YDDebugUtil::dump( $db->getTime( '28-FEB-1977' ), 'getTime( \'28-FEB-1977\' )' );
			YDDebugUtil::dump( $db->sqlString( $db->getDate() ), 'sqlString( getDate() )' );
			YDDebugUtil::dump( $db->sqlString( $db->getTime() ), 'sqlString( getTime() )' );
			YDDebugUtil::dump( $db->sqlString( $db->getDate( '__NOW__' ) ), 'sqlString( getDate( \'__NOW__\' ) )' );
			YDDebugUtil::dump( $db->sqlString( $db->getTime( '__NOW__' ) ), 'sqlString( getTime( \'__NOW__\' ) )' );

			// Test errors
			YDDebugUtil::dump( $db->getRecords( 'xx' ), 'should return error' );

			// Close the database connection
			$db->close();

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
