<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDDatabase.php' );
	require_once( 'YDDebugUtil.php' );

	// Class definition
	class database1Request extends YDRequest {

		// Class constructor
		function database1Request() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// Get the data
			$db = new YDDatabase( 'mysql', 'test', 'root', '', 'localhost' );
			$this->setVar( 'processList', $db->getRecords( 'show processlist' ) );
			$this->setVar( 'status', $db->getRecords( 'show status' ) );
			$this->setVar( 'variables', $db->getRecords( 'show variables' ) );
			$this->setVar( 'version', $db->getServerVersion() );
			$this->setVar( 'sqlcount', $db->getSqlCount() );
			$db->close();

			// Output the template
			$this->outputTemplate();

			// Test string escaping
			YDDebugUtil::dump( $db->string( "Pieter's Framework" ), '$db->string' );

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
