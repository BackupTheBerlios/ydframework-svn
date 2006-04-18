<?php

    // Include the framework
    include_once( dirname( __FILE__ ) . '/includes/YDCmfInit.php' );

    // Class definition
    class index extends YDCmfRequest {

        // Class constructor
        function index() {

            // Initialize the parent
            $this->YDCmfRequest();

        }

    }

    // Process request
    YDInclude( 'YDF2_process.php' );

?>