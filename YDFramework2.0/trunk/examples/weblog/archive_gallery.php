<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/include/YDWeblog_init.php' );

    // Includes
    YDInclude( 'YDUtil.php' );

    // Class definition
    class archive_gallery extends YDWeblogRequest {

        // Class constructor
        function archive_gallery() {
            $this->YDWeblogRequest();
        }

        // Default action
        function actionDefault() {

            // Get the weblog items
            $items = $this->weblog->getPublicItems();

            // Strip out the items without images
            foreach ( $items as $key=>$item ) {
                if ( sizeof( $item['images'] ) == 0 ) {
                    unset( $items[$key] );
                } else {
                    $items[$key]['images_as_table'] = YDArrayUtil::convertToTable( $item['images'], 8, true );
                }
            }

            // Add them to the template
            $this->tpl->assign( 'items', $items );

            // Display the template
            $this->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>