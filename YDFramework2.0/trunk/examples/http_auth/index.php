<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Include our own request
    YDInclude( 'MyLoginRequest.php' );

    // Class definition
    class index extends MyLoginRequest {

        // Class constructor
        function index() {
            $this->MyLoginRequest();
        }

        // Default action
        function actionDefault() {
            $this->template->display();
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
