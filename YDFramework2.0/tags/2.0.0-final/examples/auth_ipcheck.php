<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );

    // Class definition
    class auth_ipcheck extends YDRequest {

        // Class constructor
        function auth_ipcheck() {
            $this->YDRequest();
            $this->setRequiresAuthentication( true );
            $this->template = new YDTemplate();
        }

        // Default action
        function actionDefault() {
            $this->template->assign( 'allowed', true );
            $this->template->display();
        }

        // Check the authentication
        function isAuthenticated() {
            if ( $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ) {
                return true;
            } else {
                return false;
            }
        }

        // Redirect to the login if the authentication failed
        function authenticationFailed() {
            $this->template->assign( 'allowed', false );
            $this->template->display();
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
