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

    // Class definition
    class pbaseRequest extends YDRequest {

        // Class constructor
        function pbaseRequest() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Define the pattern
            $url = 'http://www.pbase.com/bba/odk2_2004&page=all';
            $pattern = '/www\.pbase\.com\/image\/([0-9]+)/ism';

            // Get the contents
            $objUrl = new YDUrl( 'http://www.pbase.com/bba/odk2_2004&page=all' );
            $contents = $objUrl->getContentsWithRegex( $pattern );

            // Add the template variables
            $this->setVar( 'images', YDArrayUtil::convertToTable( $contents[1], 4, true ) );
            $this->setVar( 'url', $url );

            // Output the template
            $this->outputTemplate();

        }

        // Show an image
        function actionShow() {
            $id = $_GET['id'];
            $url = 'http://www.pbase.com/image/' . $id . '.jpg';
            $this->redirect( $url );
        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>