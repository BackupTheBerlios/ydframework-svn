<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDFileSystem.php' );

    // Class definition
    class fsdirectory2 extends YDRequest {

        // Class constructor
        function fsdirectory2() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Get the directory object for the current directory
            $dir = new YDFSDirectory( dirname( __FILE__ ) );

            // Directorties in the directory
            YDDebugUtil::dump(
                $dir->getContents( '', null, array( 'YDFSDirectory' ) ),
                '$dir->getContents( \'\', null, array( \'YDFSDirectory\' ) )'
            );
            YDDebugUtil::dump(
                $dir->getContents( '', '', array( 'YDFSDirectory' ) ),
                '$dir->getContents( \'\', \'\', array( \'YDFSDirectory\' ) )'
            );

            // Files in the directory
            YDDebugUtil::dump(
                $dir->getContents( '', null, array( 'YDFSFile' ) ),
                '$dir->getContents( \'\', null, array( \'YDFSFile\' ) )'
            );
            YDDebugUtil::dump(
                $dir->getContents( '', '', array( 'YDFSFile' ) ),
                '$dir->getContents( \'\', \'\', array( \'YDFSFile\' ) )'
            );

            // Images in the directory
            YDDebugUtil::dump(
                $dir->getContents( '', null, array( 'YDFSImage' ) ),
                '$dir->getContents( \'\', null, array( \'YDFSImage\' ) )'
            );
            YDDebugUtil::dump(
                $dir->getContents( '', '', array( 'YDFSImage' ) ),
                '$dir->getContents( \'\', \'\', array( \'YDFSImage\' ) )'
            );

            // Images in the directory
            YDDebugUtil::dump(
                $dir->getContents( '', null, 'YDFSImage' ),
                '$dir->getContents( \'\', null, \'YDFSImage\' )'
            );
            YDDebugUtil::dump(
                $dir->getContents( '', '', 'YDFSImage' ),
                '$dir->getContents( \'\', \'\', \'YDFSImage\' )'
            );

            // Images and directories in the directory
            YDDebugUtil::dump(
                $dir->getContents( '', null, array( 'YDFSImage', 'YDFSDirectory' ) ),
                '$dir->getContents( \'\', null, array( \'YDFSImage\', \'YDFSDirectory\' ) )'
            );
            YDDebugUtil::dump(
                $dir->getContents( '', '', array( 'YDFSImage', 'YDFSDirectory' ) ),
                '$dir->getContents( \'\', \'\', array( \'YDFSImage\', \'YDFSDirectory\' ) )'
            );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>