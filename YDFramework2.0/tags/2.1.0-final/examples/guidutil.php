<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );

    // Class definition
    class guidutil extends YDRequest {

        // Class constructor
        function guidutil() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Generate 10 GUIDs
            foreach ( range( 1, 10 ) as $i ) {

                // Show the raw version
                $guid = YDGuidUtil::create();
                YDDebugUtil::dump( $guid, 'GUID ' . $i );

                // Create a formatted one
                $guid_formatted = YDGuidUtil::format( $guid );
                YDDebugUtil::dump( $guid_formatted, 'Formatted GUID ' . $i );

            }

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>