<?php

    // Class
    class YDCmfHome extends YDCmfContentNode {

        // Constructor
        function YDCmfHome() {
            $this->YDCmfContentNode();
        }

        // Custom action
        function actionCustom() {

            // Get the results from a database
            $version = $this->db->getValue( 'select version()' );
            $this->tpl->assign( 'mysql_version', $version );

            // Display the template
            $this->display();

        }

        // Form action
        function actionForm() {

            // Create the form
            $form = new YDForm( 'firstForm' );

            // Set the defaults
            $form->setDefaults( array( 'name' => 'Joe User' ) );

            // Add the elements
            $form->addElement( 'text', 'name', 'Enter your name:', array( 'size' => 50 ) );
            $form->addElement( 'textarea', 'desc1', 'Enter the description:' );
            $form->addElement( 'textarea', 'desc2', 'Enter the description (no toolbar):' );
            $form->addElement( 'submit', 'cmdSubmit', 'Send' );

            // Add a rule
            $form->addRule( 'name',  'required', 'Please enter your name' );
            $form->addRule( 'desc1', 'required', 'Please enter the description' );

            // Add filters
            $form->addFilter( '__ALL__', 'trim' );
            $form->addFilter( 'desc1', 'safe_html' );

            // Process the form
            if ( $form->validate() ) {

                // Show the form values
                YDDebugUtil::dump( $form->getValues() );

            }

            // Add the form to the template
            $this->tpl->assign( 'form', $form->toHtml() );

            // Display the template
            $this->display();

        }

    }

?>