<?php

    /*

        This examples demonstrates the array utilities.

    */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Class definition
    class arrayutil1Request extends YDRequest {

        // Class constructor
        function arrayutil1Request() {

            // Initialize the parent class
            $this->YDRequest();

        }

        // Default action
        function actionDefault() {

            // The original array
            $array = array( 1, 2, 3, 4, 5, 6, 7 );
            echo( 'Original array' );
            YDDebugUtil::dump( $array );

            // Convert to a three column table
            echo( 'YDArrayUtil::convertToTable( $array, 3 )' );
            YDDebugUtil::dump( YDArrayUtil::convertToTable( $array, 3 ) );

            // Convert to a three column table
            echo( 'YDArrayUtil::convertToTable( $array, 3, true )' );
            YDDebugUtil::dump( YDArrayUtil::convertToTable( $array, 3, true ) );

            // Convert to a three column table
            echo( 'YDArrayUtil::convertToTable( $array, 2 )' );
            YDDebugUtil::dump( YDArrayUtil::convertToTable( $array, 2 ) );

            // Convert to a three column table
            echo( 'YDArrayUtil::convertToTable( $array, 2, true )' );
            YDDebugUtil::dump( YDArrayUtil::convertToTable( $array, 2, true ) );

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>