<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/include/YDWeblog_init.php' );

    // Includes
    YDInclude( 'YDForm.php' );
    YDInclude( dirname( __FILE__ ) . '/include/exifer/exif.php' );

    // Class definition
    class item_gallery extends YDWeblogRequest {

        // Class constructor
        function item_gallery() {
            $this->YDWeblogRequest();
        }

        // Default action
        function actionDefault() {

            // Get the ID from the URL
            $id = $_GET['id'];

            // Redirect if empty
            if ( empty( $id ) ) {
                die( 'No image selected.' );
            }

            // Get the post ID
            $item_id = substr( $id, 5, strpos( $id, '/' )-5 );

            // Get the weblog item
            $item  = @ $this->weblog->getPublicItemById( $item_id );
            if ( ! $item ) {
                die( 'Invalid image specified' );
            }

            // Get the image object
            $image = null;
            foreach ( $item['images'] as $key=>$i ) {
                if ( $i->relative_path == $id ) {
                    $image = $i;
                    $image->previous = ( $key <= 0 ) ? null : $item['images'][$key - 1];
                    $image->next = ( $key >= sizeof( $item['images'] )-1 ) ? null : $item['images'][$key + 1];
                    $image->num = $key + 1;
                    $image->total_images = sizeof( $item['images'] );
                }
            }

            // Get the EXIF info for the image
            $result = read_exif_data_raw( $image->getAbsolutePath(), false );
            $image->exif = array();
            if ( isset( $result['IFD0'] ) ) {
                $image->exif = array_merge( $image->exif, $result['IFD0'] );
            }
            if ( isset( $result['SubIFD'] ) ) {
                $image->exif = array_merge( $image->exif, $result['SubIFD'] );
            }

            // Add them to the template
            $this->tpl->assign( 'item',  $item );
            $this->tpl->assign( 'image', $image );

            // Display the template
            $this->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>