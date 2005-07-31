<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDFileSystem.php' );

    // Class definition
    class fsimage extends YDRequest {

        // Class constructor
        function fsimage() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // We want to test the different image types
            foreach ( array( 'jpg', 'gif', 'png' ) as $imgType ) {

                // Get the file object for the current file
                $img = new YDFSImage( dirname( __FILE__ ) . '/fsimage.' . $imgType );

                // Dump the object
                YDDebugUtil::dump( $img, __FILE__ );

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
                YDDebugUtil::dump( $img->getImageSize(), 'Imagesize' );

            }

            // Get the file object for the current file
            $file = new YDFSFile( 'nofile.php' );

        }

        // Action to create and show thumbnail
        function actionThumbnail1() {
            $img = new YDFSImage( 'fsimage.jpg' );
            $img->outputThumbnail( 150, 110 );
        }

        // Action to create and show thumbnail
        function actionThumbnail2() {
            $img = new YDFSImage( 'fsimage.jpg' );
            $img->outputThumbnail( 1000, 1000 );
        }

        // Action to create and show thumbnail
        function actionThumbnail3() {
            $img = new YDFSImage( 'fsimage.jpg' );
            $img->outputThumbnail( 150, 110, false );
        }

        // Action to create and show thumbnail
        function actionThumbnail4() {
            $img = new YDFSImage( 'fsimage.png' );
            $img->outputThumbnail( 150, 110 );
        }

        // Action to create and show thumbnail
        function actionThumbnail5() {
            $img = new YDFSImage( 'fsimage.gif' );
            $img->outputThumbnail( 150, 110 );
        }

        // Action to create and show thumbnail
        function actionThumbnailSave() {
            @unlink( 'fsimage.thumb.jpg' );
            $img = new YDFSImage( 'fsimage.jpg' );
            $img->saveThumbnail( 150, 110, 'fsimage.thumb.jpg' );
            header( 'Location: fsimage.thumb.jpg' );
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>