<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDRequest.php' );

    // Class definition
    class form_rule extends YDRequest {

        // Class constructor
        function form_rule() {

            // Initialize the parent class
            $this->YDRequest();

        }

        // Default action
        function actionDefault() {

            // Create the form
            $form = new YDForm( 'myForm' );
            $form->setDefaults( array( 'name' => 'Joe User' ) );
            $form->addElement( 'text', 'loginName', 'User name:' );
            $form->addElement( 'password', 'loginPass', 'Password:' );
            $form->addElement( 'submit', 'cmdSubmit', 'Login' );

            // Add the form rule
            $form->addFormRule( array( & $this, 'checkLogin' ) );

            // Process the form
            if ( $form->validate() ) {
                YDDebugUtil::dump( $form->getValues() );
            } else {
                $form->display();
            }

        }

        // Function to check the login. This is the callback for the form rule.
        function checkLogin( $fields ) {
            if ( $fields['loginName'] == 'pieter' && $fields['loginPass'] == 'kermit' ) {
                return true;
            } else {
                return array( 'loginName' => 'Username and/or password incorrect' );
            }
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>