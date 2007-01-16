<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDDatabase.php' );
    YDInclude( 'YDTemplate.php' );

    // Define the default pagesize
    YDConfig::set( 'YD_DB_DEFAULTPAGESIZE', 15 );

    // Class definition
    class array_paging_with_sorting extends YDRequest {

        // Class constructor
        function array_paging_with_sorting() {
            $this->YDRequest();
            $this->template = new YDTemplate();
        }

        // Default action
        function actionDefault() {

            // Get the pagesize and current page from the query string
            $page = @ $_GET['page'];
            $size = @ $_GET['size'];

            // Get the database connection
            $db = YDDatabase::getInstance( 'mysql', 'test', 'root', '', 'localhost' );

            // Get the records
            $recordset = $db->getRecords( 'show status' );

            // Close the database connection
            $db->close();

            // Create the YDRecordSet object
            $recordset = new YDRecordSet( $recordset, $page, $size );

            // Setup the template
            $this->template->assign( 'recordset', $recordset );

            // Display the template
            $this->template->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
