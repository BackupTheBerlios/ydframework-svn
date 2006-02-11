<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/include/YDWeblog_init.php' );

    // Class definition
    class link extends YDWeblogRequest {

        // Class constructor
        function link() {
            $this->YDWeblogRequest();
        }

        // Default action
        function actionDefault() {

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // Get the weblog details and go to the default view if none is matched
            $link = $this->weblog->getLinkById( $id );
            $this->redirectIfMissing( $link );

            // Increase the num_visits fields
            $this->weblog->updateLinkNumVisits( $id );

            // Redirect to the link
            $this->redirect( $link['url'] );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>