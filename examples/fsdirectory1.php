<?php

    /*

        This examples demonstrates the file utilities.

    */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Class definition
    class fsdirectory1Request extends YDRequest {

        // Class constructor
        function fsdirectory1Request() {

            // Initialize the parent class
            $this->YDRequest();

        }

        // Default action
        function actionDefault() {

            // Get the directory object for the current directory
            $dir = new YDFSDirectory( dirname( __FILE__ ) );

            // Dump the object
            echo( dirname( __FILE__ ) );
            YDDebugUtil::dump( $dir );

            // All files in the directory
            echo( 'Full file list' );
            YDDebugUtil::dump( $dir->getContents() );

            // PHP files in the directory
            echo( 'List of PHP files' );
            YDDebugUtil::dump( $dir->getContents( '*.php' ) );

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>