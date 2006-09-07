<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/include/YDWeblog_init.php' );

    // Class definition
    class index extends YDWeblogRequest {

        // Class constructor
        function index() {
            $this->YDWeblogRequest();
        }

        // Default action
        function actionDefault() {

            // Get the ID from the query string
            $id = $this->getQueryStringParameterAsInt( 'id', -1 );
            if ( $id != -1 ) {
                $this->redirect( 'item.php?id=' . $id );
            }

            // Add support for itemid query strings
            $id = $this->getQueryStringParameterAsInt( 'itemid', -1 );
            if ( $id != -1 ) {
                $this->redirect( 'item.php?id=' . $id );
            }

            // Get the weblog items and 5 older items
            $items     = $this->weblog->getPublicItems( YDConfig::get( 'weblog_entries_fp', 5 ) );
            $old_items = $this->weblog->getPublicItems( YDConfig::get( 'weblog_entries_fp', 5 ), sizeof( $items ) );

            // Assign the variables to the template
            $this->tpl->assign( 'items',     $items );
            $this->tpl->assign( 'old_items', $old_items );

            // Display the template
            $this->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
