<?php

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    require_once( 'YDRequest.php' );

    // Class definition
    class phpinforequest extends YDRequest {

        // Class constructor
        function phpinforequest() {

            // Initialize the parent class
            $this->YDRequest();

        }

        // Default action
        function actionDefault() {

            // Show the PHP information
            phpinfo();

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
