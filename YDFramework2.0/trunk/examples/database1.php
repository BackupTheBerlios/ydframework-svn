<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDDatabase.php' );
	YDInclude( 'YDTemplate.php' );

	// Class definition
	class database1 extends YDRequest {

		// Class constructor
		function database1() {
			$this->YDRequest();
			$this->template = new YDTemplate();
		}

		// Default action
		function actionDefault() {

			// Get the data
			$db = YDDatabase::getInstance( 'mysql', 'test', 'root', '', 'localhost' );
			$this->template->assign( 'processList', $db->getRecords( 'show processlist' ) );
			$this->template->assign( 'status', $db->getRecords( 'show status' ) );
			$this->template->assign( 'variables', $db->getRecords( 'show variables' ) );
			$this->template->assign( 'version', $db->getServerVersion() );
			$this->template->assign( 'sqlcount', $db->getSqlCount() );

			// Output the template
			$this->template->display();

			// Test string escaping
			YDDebugUtil::dump( $db->string( "Pieter's Framework" ), '$db->string' );

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
	YDInclude( 'YDF2_process.php' );

?>
