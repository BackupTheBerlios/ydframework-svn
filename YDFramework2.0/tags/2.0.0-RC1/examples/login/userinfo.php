<?php

    // Standard include
    require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Include our own request
    YDInclude( 'MyLoginRequest.php' );

    // Class definition
    class userinfo extends MyLoginRequest {

        // Class constructor
        function userinfo() {
            $this->MyLoginRequest();
        }

        // Default action
        function actionDefault() {
            $this->template->display();
        }

    }

    // Standard include
    YDInclude( 'YDF2_process.php' );

?>
