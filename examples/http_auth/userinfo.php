<?php

    // Standard include
    require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Include our own request
    require_once( 'MyLoginRequest.php' );

    // Class definition
    class userinfoRequest extends MyLoginRequest {

        // Class constructor
        function userinfoRequest() {
            $this->MyLoginRequest();
        }

        // Default action
        function actionDefault() {
            $this->outputTemplate();
        }

    }

    // Standard include
    require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_process.php' );

?>
