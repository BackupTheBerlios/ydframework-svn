<?php

    // Includes
    include_once( 'YDForm.php' );
    include_once( 'YDRequest.php' );
    include_once( 'YDTemplate.php' );

    // Class definition
    class MyLoginRequest extends YDRequest {

        // Class constructor
        function MyLoginRequest() {

            // Initialize the parent class
            $this->YDRequest();

            // Indicate we require login
            $this->setRequiresAuthentication( true );

            // Instantiate the template object
            $this->template = new YDTemplate();

        }

        // Default action
        function actionDefault() {
            $this->outputTemplate();
        }

        // Login function
        function actionLogin() {

            // Redirect to default action if already logged in
            if ( $this->isAuthenticated() === true ) {
                $this->forward( 'default' );
                return;
            }

            // Create the login form
            $form = new YDForm( 'loginForm' );
            $form->setDefaults( array( 'name' => 'Joe User' ) );
            $form->addElement( 'text', 'loginName', 'User name:' );
            $form->addElement( 'password', 'loginPass', 'Password:' );
            $form->addElement( 'submit', 'cmdSubmit', 'Login' );

            // Add the rules
            $form->addFormRule( array( & $this, 'checkLogin' ) );

            // Process the form
            if ( $form->validate() ) {

                // Get username and password
                $usrName = $form->getValue( 'loginName' );
                $usrPass = $form->getValue( 'loginPass' );

                // Mark the session that we are logged in
                $_SESSION['usrName'] = 'pieter';
                $_SESSION['isLoggedIn'] = true;

                // Mark the form as valid
                $this->authenticationSucceeded();
                $this->forward( 'default' );
                return;

            }

            // Add the form to the template
            $this->template->assignForm( 'form', $form );

            // Output the template
            $this->template->display( 'login' );

        }

        // Check the authentication
        function isAuthenticated() {

            // Check the session variables
            if ( isset( $_SESSION['usrName'] ) ) {

                // Check if we are marked as being logged in
                if ( isset( $_SESSION['isLoggedIn'] ) && $_SESSION['isLoggedIn'] == true ) {
                    return true;
                }

            }

            // Fails otherwise
            return false;

        }

        // Redirect to the login if the authentication failed
        function authenticationFailed() {
            $this->forward( 'login' );
            return;
        }

        // Function to check the login
        function checkLogin( $fields ) {
            if ( $fields['loginName'] == 'pieter' && $fields['loginPass'] == 'kermit' ) {
                return true;
            } else {
                return array( 'loginName' => 'Username and/or password incorrect' );
            }
        }

    }

?>
