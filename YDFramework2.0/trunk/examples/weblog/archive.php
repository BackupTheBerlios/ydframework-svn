<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/include/YDWeblog_init.php' );

    // Includes
    YDInclude( 'YDUtil.php' );

    // Class definition
    class archive extends YDWeblogRequest {

        // Class constructor
        function archive() {
            $this->YDWeblogRequest();
        }

        // Default action
        function actionDefault() {

            // Get the weblog items
            $items = $this->weblog->getPublicItems();

            // Convert to a nested array
            $items = YDArrayUtil::convertToNested( $items, 'yearmonth' );

            // Add them to the template
            $this->tpl->assign( 'items', $items );

            // Display the template
            $this->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>