<?php

    /*
     *  This examples demonstrates the image utilities.
     */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    require_once( 'YDRequest.php' );
    require_once( 'YDFSImage.php' );
    require_once( 'YDFSFile.php' );
    require_once( 'YDDebugUtil.php' );

    // Class definition
    class fsimage1Request extends YDRequest {

        // Class constructor
        function fsimage1Request() {

            // Initialize the parent class
            $this->YDRequest();

        }

        // Default action
        function actionDefault() {

            // Get the file object for the current file
            $img = new YDFSImage( 
                dirname( __FILE__ ) . '/fsimage1.jpg'
            );

            // Dump the object
            echo( __FILE__ );
            YDDebugUtil::dump( $img );

            // Dump the object
            echo( '<br>Basename: ' . $img->getBasename() );
            echo( '<br>Extension: ' . $img->getExtension() );
            echo( '<br>Path: ' . $img->getPath() );
            echo( '<br>LastModified: ' . $img->getLastModified() );
            echo( '<br>File size: ' . $img->getSize() );
            echo( '<br>Width: ' . $img->getWidth() );
            echo( '<br>Height: ' . $img->getHeight() );
            echo( '<br>Image type: ' . $img->getImageType() );
            echo( '<br>MIME type: ' . $img->getMimeType() );

            // Image size
            echo( '<br>Imagesize:' );
            YDDebugUtil::dump( $img->getImageSize() );

            // Get the file object for the current file
            $file = new YDFSFile( 'nofile.php' );

        }

        // Action to create and show thumbnail
        function actionThumbnail1() {
            $img = new YDFSImage( 'fsimage1.jpg' );
            $img->outputThumbnail( 150, 110 );
        }

        // Action to create and show thumbnail
        function actionThumbnail2() {
            $img = new YDFSImage( 
                dirname( __FILE__ ) . '/../YDFramework2/doc/userguide/RequestProcessing.gif'
            );
            $img->outputThumbnail( 100, 100 );
        }

        // Action to create and show thumbnail
        function actionThumbnail3() {
            $img = new YDFSImage( 'fsimage1.jpg' );
            $img->outputThumbnail( 1000, 1000 );
        }

        // Action to create and show thumbnail
        function actionThumbnail4() {
            $img = new YDFSImage( 'fsimage1.jpg' );
            $img->outputThumbnail( 150, 110, false );
        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>