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
			$db = new YDDatabase( 'mysql', 'root', '', 'test', 'localhost' );
			$data = $db->getRecords( 'show processlist' );
			$this->setVar( 'processList', $data );
			$data = $db->getRecords( 'show status' );
			$this->setVar( 'status', $data );
			$data = $db->getRecords( 'show variables' );
			$this->setVar( 'variables', $data );
			$db->close();

			// Output the template
			$this->outputTemplate();

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
