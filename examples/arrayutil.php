<?php

    /*
     *  This examples demonstrates the array utilities.
     */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    require_once( 'YDRequest.php' );
    require_once( 'YDArrayUtil.php' );
    require_once( 'YDDebugUtil.php' );

    // Class definition
    class arrayutilRequest extends YDRequest {

        // Class constructor
        function arrayutilRequest() {
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

            // Test for errors
            echo( 'YDArrayUtil::convertToTable( $array, "a", true )' );
            YDDebugUtil::dump( YDArrayUtil::convertToTable( $array, 'a', true ) );

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>