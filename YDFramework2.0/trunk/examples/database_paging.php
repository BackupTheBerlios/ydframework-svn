<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDDatabase.php' );
	YDInclude( 'YDTemplate.php' );

	// Class definition
	class database_paging extends YDRequest {

		// Class constructor
		function database_paging() {
			$this->YDRequest();
			$this->template = new YDTemplate();
		}

		// Default action
		function actionDefault() {

			// Get the pagesize and current page from the URL
			// We use the @ mark to supress any error messsages
			$page = @ $_GET['page'];
			$size = @ $_GET['size'];

			// Get the database connection
			$db = YDDatabase::getInstance( 'mysql', 'test', 'root', '', 'localhost' );

			// Get the records
			$recordset = $db->getRecordsAsSet( 'show status', $page, $size );
			//$recordset->dump();

			// Close the database connection
			$db->close();

			// Setup the template
			$this->template->assign( 'recordset', $recordset );

			// Display the template
			$this->template->display();

		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>