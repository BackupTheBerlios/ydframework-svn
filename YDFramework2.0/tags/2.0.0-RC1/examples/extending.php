<?php

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );

    // Class definition (custom request handler)
    class customBaseRequest extends YDRequest {

        // Class constructor
        function customBaseRequest() {
            $this->YDRequest();
        }

    }

    // Class definition (handles the actual request)
    class extending extends customBaseRequest {

        // Class constructor
        function extending() {
            $this->customBaseRequest();
        }

        // Default action
        function actionDefault() {
            YDDebugUtil::dump( YDObjectUtil::getAncestors( $this ), 'ancestors of this request:' );
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
