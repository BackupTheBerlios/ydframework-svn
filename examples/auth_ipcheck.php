<?php

    /*

        This examples demonstrates how you can use the authentication functions
        to allow access to localhost only.

    */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Class definition
    class auth_ipcheckRequest extends YDRequest {

        // Class constructor
        function auth_ipcheckRequest() {
            $this->YDRequest();
            $this->setRequiresAuthentication( true );
        }

        // Default action
        function actionDefault() {
            $this->setVar( 'allowed', true );
            $this->outputTemplate();
        }

        // Default action
        function actionTest() {
            $this->outputTemplate();
        }

        // Check the authentication
        function isAuthenticated() {

            // We only allow localhost access
            if ( $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ) {
                return true;
            } else {
                return false;
            }

        }

        // Redirect to the login if the authentication failed
        function authenticationFailed() {
            $this->setVar( 'allowed', false );
            echo( 'Only localhost access is allowed!' );
            //$this->outputTemplate();
        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
