<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/include/YDWeblog_init.php' );

    // Class definition
    class page extends YDWeblogRequest {

        // Class constructor
        function page() {
            $this->YDWeblogRequest();
        }

        // Default action
        function actionDefault() {

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // Get the page details
            $page  = $this->weblog->getPublicPageById( $id );
            $this->redirectIfMissing( $page );

            // Add them to the template
            $this->tpl->assign( 'page', $page );

            // Display the template
            $this->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>