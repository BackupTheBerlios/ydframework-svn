<?php

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDFileSystem.php' );

    // Class definition
    class fsdirectory extends YDRequest {

        // Class constructor
        function fsdirectory() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Get the directory object for the current directory
            $dir = new YDFSDirectory( dirname( __FILE__ ) );

            // Dump the object
            YDDebugUtil::dump( $dir, dirname( __FILE__ ) );
            
            // Dump the path properties
            YDDebugUtil::dump( $dir->getBasename(), '$dir->getBasename()' );
            YDDebugUtil::dump( $dir->getPath(), '$dir->getPath()' );
            YDDebugUtil::dump( $dir->getAbsolutePath(), '$dir->getAbsolutePath()' );

            // All files in the directory
            YDDebugUtil::dump( $dir->getContents(), '$dir->getContents()' );

            // PHP files in the directory
            YDDebugUtil::dump( $dir->getContents( '*.tpl' ), '$dir->getContents( \'*.tpl\' )' );

            // PHP files in the directory
            YDDebugUtil::dump(
                $dir->getContents( array( '*.jpg', '*.txt' ) ), '$dir->getContents( array( \'*.jpg\', \'*.txt\' ) )'
            );

            // PHP files in the directory
            YDDebugUtil::dump(
                $dir->getContents(array( '*.jpg', '*.txt', '!bbcode.*' ) ),
                '$dir->getContents( array( \'*.jpg\', \'*.txt\', \'!bbcode.*\' ) )'
            );

            // Create subdirectory
            YDDebugUtil::dump( $dir->createDirectory( 'test' ), '$dir->createDirectory( \'test\' ) )' );

            // Delete a directory tree
            YDDebugUtil::dump( $dir->deleteDirectory( 'test' ), '$dir->deleteDirectory( \'test\' )' );
            YDDebugUtil::dump( $dir->deleteDirectory( 'xxx' ), '$dir->deleteDirectory( \'xxx\' )' );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>