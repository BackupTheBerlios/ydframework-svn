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

    // Class definition
    class email1Request extends YDRequest {

        // Class constructor
        function email1Request() {

            // Initialize the parent class
            $this->YDRequest();

        }

        // Default action
        function actionDefault() {

            // Mark the form as not valid
            $this->setVar( 'formValid', false );

            // Create the form
            $form = new YDForm( 'emailForm' );

            // Add the elements
            $form->addElement( 'text', 'email', 'Enter your email address:' );
            $form->addElement( 'submit', 'cmdSubmit', 'Send' );

            // Apply a filter
            $form->applyFilter( 'email', 'trim' );

            // Add a rule
            $form->addRule(
                'email', 'Please enter a valid email address', 'email'
            );

            // Process the form
            if ( $form->validate() ) {

                // Mark the form as valid
                $this->setVar( 'formValid', true );

                // Send the email
                $eml = new YDEmail();
                $eml->setFrom( 'pieter@yellowduck.be', YD_FW_NAME );
                $eml->addTo( $form->exportValue( 'email' ) );
                $eml->setSubject( 'Hello from Pieter & Fiona!' );
                $eml->setTxtBody( 'Hello from Pieter & Fiona!' );
                $eml->setHtmlBody(
                    '<h1>Hello from Pieter & Fiona!</h1>'
                    . '<img src="fsimage1.jpg" border="1">'
                );
                $eml->addAttachment( 'email1.tpl' );
                $eml->addHtmlImage( 'fsimage1.jpg', 'image/jpeg' );
                $eml->send();

            }

            // Add the form to the template
            $this->addForm( 'form', $form );

            // Output the template
            $this->outputTemplate();

            /*
            $eml = new YDEmail();
            $eml->setFrom( 'pieter.claerhout@creo.com', 'Pieter - CREO' );
            $eml->addTo( 'pieter.claerhout@creo.com', 'Pieter - CREO' );
            $eml->addCC( 'pieter.claerhout@pandora.be' );
            $eml->setSubject( 'Hello from Pieter & Fiona!' );
            $eml->setTxtBody( 'Hello from Pieter & Fiona!' );
            $eml->setHtmlBody( '<h1>Hello from Pieter & Fiona!</h1><img src="fsimage1.jpg">' );
            $eml->addAttachment( 'config.php' );
            $eml->addHtmlImage( 'fsimage1.jpg', 'image/jpeg' );
            $eml->send();

            echo( 'Email was send successfully!' );
            */

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>