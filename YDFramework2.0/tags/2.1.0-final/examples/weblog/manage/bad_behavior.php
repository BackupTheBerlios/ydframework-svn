<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Class definition
    class bad_behavior extends YDWeblogAdminRequest {

        // Class constructor
        function bad_behavior() {

            // Initialize parent
            $this->YDWeblogAdminRequest( true );

        }

        // Default action
        function actionDefault() {

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // If there is nothing, show the list
            if ( $id == -1 ) {

                // Get the count and number of requests
                $requests_count = $this->weblog->getBadBehaviorRequestsCount();
                $requests = $this->weblog->getBadBehaviorPostRequests();

                // Get the pagesize and current page from the URL
                $page = @ $_GET['page'];

                // Create the YDRecordSet object
                $requests = new YDRecordSet( $requests, $page, YDConfig::get( 'YD_DB_DEFAULTPAGESIZE', 20 ) );

                // Assign it to the template
                $this->tpl->assign( 'requests_count', $requests_count );
                $this->tpl->assign( 'requests', $requests );

            } else {

                // Get the request data
                $request = $this->weblog->getBadBehaviorRequestById( $id );

                // Redirect if nothing found
                if ( ! $request ) {
                    $this->redirectToAction();
                }

                // Add it to the template
                $this->tpl->assign( 'request', $request );

            }

            // Display the template
            $this->display();

        }

        // Empty the request information
        function actionEmptyBadBehaviour() {
            $this->weblog->emptyBadBehaviour();
            $this->redirect( YD_SELF_SCRIPT );
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
