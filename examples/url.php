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

    // Class definition
    class urlRequest extends YDRequest {

        // Class constructor
        function urlRequest() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Create the URL object
            $url = new YDUrl( 'http://www.yellowduck.be/index.xml' );

            // The different parts
            echo( '<br>URL: ' . $url->getUrl() );
            echo( '<br>Scheme: ' . $url->getScheme() );
            echo( '<br>Host: ' . $url->getHost() );
            echo( '<br>Port: ' . $url->getPort() );
            echo( '<br>User: ' . $url->getUser() );
            echo( '<br>Password: ' . $url->getPassword() );
            echo( '<br>Path: ' . $url->getPath() );
            echo( '<br>Query: ' . $url->getQuery() );
            echo( '<br>Fragment: ' . $url->getFragment() );

            // Get the contents
            YDDebugUtil::dump( $url->getContents() );

        }

        // Action to retrieve an image
        function actionImage1() {

            // Create the URL object
            $url = new YDUrl( 'http://www.yellowduck.be/images/uploads/kuifjeseend.jpg' );

            // Get the contents
            header( 'Content-type: image/jpeg' );
            echo( $url->getContents() );

        }
  
        // Action to retrieve an image
        function actionImage2() {

            // Create the URL object
            $url = new YDUrl( 'http://www.pbase.com/image/23.jpg' );

            // Get the contents
            header( 'Content-type: image/jpeg' );
            echo( $url->getContents() );

        }
  
        // Action to retrieve an image
        function actionImage3() {

            // Create the URL object
            $url = new YDUrl( 'http://mikoga.image.pbase.com/u44/beachshop/small/28557543.testdag_mei_2004IMG_9569.jpg' );

            // Get the contents
            header( 'Content-type: image/jpeg' );
            echo( $url->getContents() );

        }

        // Function to get the header for a URL
        function actionHeaders() {

            // Create the URL object
            $url = new YDUrl( 'http://www.yellowduck.be/index.xml' );

            // Dump the headers
            YDDebugUtil::dump( $url->getHeaders() );

        }

        // Function to get the status for a URL
        function actionStatus() {

            // Create the URL object
            $url = new YDUrl( 'http://www.yellowduck.be/index.xml' );

            // Dump the headers
            YDDebugUtil::dump( $url->getStatus() );

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>