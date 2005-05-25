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

            // Get the weblog items and 5 older items
            $items     = $this->weblog->getItems( YDConfig::get( 'weblog_entries_fp', 5 ) );
            $old_items = $this->weblog->getItems( 5, sizeof( $items ) );

            // Assign them to the template
            $this->tpl->assign( 'items',     $items );
            $this->tpl->assign( 'old_items', $old_items );

            // Display the template
            $this->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
