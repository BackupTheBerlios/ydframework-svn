<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );

    // Class definition
    class phpinfo extends YDRequest {

        // Class constructor
        function phpinfo() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {
            phpinfo();
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
