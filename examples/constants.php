<?php

    /*
     *  This examples demonstrates the array utilities.
     */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    require_once( 'YDRequest.php' );
    require_once( 'YDForm.php' );
    require_once( 'YDEmail.php' );
    require_once( 'YDTemplate.php' );

    // Class definition
    class constantsRequest extends YDRequest {

        // Class constructor
        function constantsRequest() {

            // Initialize the parent class
            $this->YDRequest();

        }

        // Default action
        function actionDefault() {

            // Output the template
            $this->outputTemplate();

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>