<?php

    /*
     *  This examples demonstrates the array utilities.
     */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    require_once( 'YDRequest.php' );
    require_once( 'YDUrl.php' );
    require_once( 'YDDebugUtil.php' );
    require_once( 'YDArrayUtil.php' );
    require_once( 'YDFeedCreator.php' );

    // Class definition
    class pbaseRequest extends YDRequest {

        // Class constructor
        function pbaseRequest() {

            // Initialize the parent
            $this->YDRequest();

            // The list of galleries
            /*
            $this->galleries = array(
                1 => 'http://www.pbase.com/bba/odk2_2004',
                2 => 'http://www.pbase.com/bba/krab04',
            );
            */

            // The fixed url of our gallery
            $this->url = 'http://www.pbase.com/bba/odk2_2004&page=all';
            $this->setVar( 'url', $this->url );
            
            // The regex pattern to match the images
            $this->pattern = '/www\.pbase\.com\/image\/([0-9]+)/ism';

            // Get the list of images
            $objUrl = new YDUrl( $this->url );
            $contents = $objUrl->getContentsWithRegex( $this->pattern );
            $this->images = $contents[1];

        }

        // Default action
        function actionDefault() {

            // Add the template variables
            $this->setVar( 
                'images', YDArrayUtil::convertToTable( $this->images, 4, true )
            );

            // Output the template
            $this->outputTemplate();

        }

        // Show an image gallery
        /*
        function actionShowGallery() {

            // Check if the ID exists
            if ( ! isset( $_GET['id'] ) ) {
                $this->redirectToAction();
            }

            // Check if the ID exists
            if ( ! in_array( $_GET['id'], array_keys( $this->galleries ) ) ) {
                $this->redirectToAction();
            }

            // Output the template
            $this->outputTemplate();

        }
        */

        // Show an image
        function actionShowImage() {

            // Check if the ID exists
            if ( ! isset( $_GET['id'] ) ) {
                $this->redirectToAction();
            }

            // Check if the ID exists
            if ( ! in_array( $_GET['id'], $this->images ) ) {
                $this->redirectToAction();
            }

            // Get current image number
            $imageCurrent = array_search( $_GET['id'], $this->images );

            // Start with no previous and next image
            $imagePrevious = null;
            $imageNext = null;

            // Get the index of the previous image
            if ( $imageCurrent != 0 ) {
                $imagePrevious = $this->images[ $imageCurrent - 1 ];
            }

            // Get the index if the next image
            if ( $imageCurrent < sizeof( $this->images ) ) {
                $imageNext = $this->images[ $imageCurrent + 1 ];
            }

            // Add them to the template
            $this->setVar( 'imagePrevious', $imagePrevious );
            $this->setVar( 'imageCurrent', $_GET['id'] );
            $this->setVar( 'imageNext', $imageNext );

            // Output the template
            $this->outputTemplate();

        }

        // Get the list of images as RSS feed
        function actionRss() {

            // Create a new Feed
            $this->fc = new YDFeedCreator();
            $this->fc->setTitle( $this->url );
            $this->fc->setLink( $this->getCurrentUrl() );

            // Add the images
            foreach ( $this->images as $image ) {

                $this->fc->addItem(
                    $image . '.jpg',
                    'http://www.pbase.com/image/' . $image . '.jpg',
                    '<img src="http://www.pbase.com/image/' . $image . '.jpg">'
                );

            }

            // Output the feed
            $this->fc->outputXml( 'RSS2.0' );

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
