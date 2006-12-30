<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Class definition
    class bad_behavior extends YDWeblogAdminRequest {

        // Class constructor
        function bad_behavior() {
            $this->YDWeblogAdminRequest();
        }

        // Default action
        function actionDefault() {

            // Get the count and number of requests
            $requests_count = $this->weblog->getBadBehaviorRequestsCount();
            $requests = $this->weblog->getBadBehaviorRequests();

            // Get the pagesize and current page from the URL
            $page = @ $_GET['page'];

            // Create the YDRecordSet object
            $requests = new YDRecordSet( $requests, $page, YDConfig::get( 'YD_DB_DEFAULTPAGESIZE', 20 ) );

            // Assign it to the template
            $this->tpl->assign( 'requests_count', $requests_count );
            $this->tpl->assign( 'requests', $requests );

            // Display the template
            $this->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
