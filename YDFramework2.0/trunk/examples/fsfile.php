<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDFileSystem.php' );

    // Class definition
    class fsfile extends YDRequest {

        // Class constructor
        function fsfile() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Get the file object for the current file
            $file = new YDFSFile( __FILE__ );

            // Dump the object
            echo( __FILE__ );
            YDDebugUtil::dump( $file );

            // Dump the object
            echo( '<br>Basename: ' . $file->getBasename() );
            echo( '<br>Extension: ' . $file->getExtension() );
            echo( '<br>Path: ' . $file->getPath() );
            echo( '<br>LastModified: ' . $file->getLastModified() );
            echo( '<br>File size: ' . $file->getSize() );

            // Get the contents
            YDDebugUtil::dump( $file->getContents(), '$file->getContents()' );

            // Get the partial contents
            YDDebugUtil::dump( $file->getContents( 2, 3 ), '$file->getContents( 2, 3 )' );

            // Create a dummy file
            $dir = new YDFSDirectory( '.' );
            $file = $dir->createFile( 'dummy.txt', 'initial contents' );

            // Update the contents
            $file->setContents( 'new contents' );

            // Get the contents
            YDDebugUtil::dump( $file->getContents(), '$file->getContents() after update' );

            // Append the contents
            $file->setContents( YD_CRLF . 'appended contents', true );

            // Get the contents
            YDDebugUtil::dump( $file->getContents(), '$file->getContents() after append' );

            // Delete the file
            $file->delete();

            // Get the file object for the current file
            $file = new YDFSFile( 'nofile.php' );

        }

        // Download action
        function actionDownload() {
            $file = new YDFSFile( __FILE__ );
            $file->download();
        }

        // Download action
        function actionDownload2() {
            $file = new YDFSFile( __FILE__ );
            $file->download( 'download_test.php' );
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
