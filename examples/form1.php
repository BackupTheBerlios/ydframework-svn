<?php

    /*

        This examples demonstrates:
        - How you can use forms in YDF
        - How form validation is handled
        - How the default values of a form can be set
        - How a filter can be applied to a form element (trim of the name field)

    */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Class definition
    class form1Request extends YDRequest {

        // Class constructor
        function form1Request() {

            // Initialize the parent class
            $this->YDRequest();

        }

        // Default action
        function actionDefault() {

            // Set the title of the form
            $this->setVar( 'title', 'Sample form' );

            // Mark the form as not valid
            $this->setVar( 'formValid', false );

            // Create the form
            $form = new YDForm( 'firstForm' );

            // Set the defaults
            $form->setDefaults( array( 'name' => 'Joe User' ) );

            // Add the elements
            $form->addElement(
                'text', 'name', 'Enter your name:', array( 'size' => 50 )
            );
            $form->addElement( 'submit', 'cmdSubmit', 'Send' );

            // Apply a filter
            $form->applyFilter( 'name', 'trim' );

            // Add a rule
            $form->addRule( 'name', 'Please enter your name', 'required', null );

            // Process the form
            if ( $form->validate() ) {

                // Mark the form as valid
                $this->setVar( 'formValid', true );

            }

            // Add the form to the template
            $this->addForm( 'form', $form );

            // Output the template
            $this->outputTemplate();
        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
