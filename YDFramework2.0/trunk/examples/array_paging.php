<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Define the default pagesize
	define( 'YD_DB_DEFAULTPAGESIZE', 15 );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDDatabase.php' );
	YDInclude( 'YDFileSystem.php' );
	YDInclude( 'YDTemplate.php' );

	// Class definition
	class array_paging extends YDRequest {

		// Class constructor
		function array_paging() {
			$this->YDRequest();
			$this->template = new YDTemplate();
		}

		// Default action
		function actionDefault() {

			// Get the pagesize and current page from the URL
			// We use the @ mark to supress any error messsages
			$page = @ $_GET['page'];
			$size = @ $_GET['size'];

			// Get the list of files in the current directory
			$dir = new YDFSDirectory();
			$files = $dir->getContents( '*.*', null, 'YDFSFile' );

			// Create the YDRecordSet object
			$recordset = new YDRecordSet( $files, $page, $size );

			// Setup the template
			$this->template->assign( 'recordset', $recordset );

			// Display the template
			$this->template->display();

		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>
