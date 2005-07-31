<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );

    // Class definition
    class ajax extends YDRequest {


        // Class constructor
        function ajax() {

            // Initialize the parent
            $this->YDRequest();

            // Create a template object
            $this->tpl = new YDTemplate();

        }

        // Default action
        function actionDefault() {

            // Display the template
            $this->tpl->display();

        }

        // Server action
        function actionServer() {

            // Output the result
            echo( YD_FW_NAMEVERS );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>