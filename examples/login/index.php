<?php

    // Standard include
    require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Include our own request
    require_once( 'MyLoginRequest.php' );

    // Class definition
    class indexRequest extends MyLoginRequest {

        // Class constructor
        function indexRequest() {
            $this->MyLoginRequest();
        }

        // Default action
        function actionDefault() {
            $this->outputTemplate();
        }

        // Function to logout
        function actionLogout() {
            unset( $_SESSION['usrName'] );
            unset( $_SESSION['isLoggedIn'] );
            $this->forward( 'login' );
            return;
        }

    }

    // Standard include
    require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_process.php' );

?>
