<?php

    /*

        This examples demonstrates the file utilities.

    */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Class definition
    class fileutil1Request extends YDRequest {

        // Class constructor
        function fileutil1Request() {

            // Initialize the parent class
            $this->YDRequest();

        }

        // Default action
        function actionDefault() {

            echo( 'YDFileUtil::match( \'*.php\', __FILE__ )' );
            YDDebugUtil::dump(
                YDFileUtil::match( '*.php', __FILE__ )
            );

            echo( 'YDFileUtil::match( \'*.tpl\', __FILE__ )' );
            YDDebugUtil::dump(
                YDFileUtil::match( '*.tpl', __FILE__ )
            );

            echo( 'YDFileUtil::match( \'fileutil?.php\', basename( __FILE__ ) )' );
            YDDebugUtil::dump(
                YDFileUtil::match( 'fileutil?.php', basename( __FILE__ ) )
            );

            echo( 'YDFileUtil::match( \'fileutil?.php\', __FILE__ )' );
            YDDebugUtil::dump(
                YDFileUtil::match( 'fileutil?.php', __FILE__ )
            );

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>