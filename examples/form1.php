<?php

    /*
     *  This examples demonstrates:
     *  - How you can use forms in YDF
     *  - How form validation is handled
     *  - How the default values of a form can be set
     *  - How a filter can be applied to a form element (trim of the name field)
     */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    require_once( 'YDForm.php' );
    require_once( 'YDRequest.php' );
    require_once( 'YDFSDirectory.php' );

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
            $form->addElement(
                'bbtextarea', 'desc', 'Enter the description:'
            );
            $form->addElement(
                'bbtextarea', 'desc2', 'Enter the description (no toolbar):'
            );
            $form->addElement(
                'bbtextarea', 'desc3', 'Enter the description (no toolbar):'
            );
            $form->addElement(
                'date', 'date', 'Enter the date:',
                array( 'language'=>'en', 'format'=>'dMYHi' )
            );
            $form->addElement( 'submit', 'cmdSubmit', 'Send' );

            // Update the no toolbar element
            $element = & $form->getElement( 'desc2' );
            $element->clearModifiers();
            $element->clearSimplePopups();

            // Add a popup window to the third description
            $element = & $form->getElement( 'desc3' );
            $element->addPopupWindow(
                'form1.php?do=selector&field=desc3&tag=img', 'select image'
            );
            $element->addPopupWindow(
                'form1.php?do=selector&field=desc3&tag=url', 'select url'
            );

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

        function actionSelector() {

            // Redirect to the main if no field in the url
            if ( empty( $_GET['field'] ) ) {
                $this->forward( 'default' );
            }

            // Get the list of images in the current directory
            $dir = new YDFSDirectory();

            // Add the list of images to the template
            if ( $_GET['tag'] == 'img' ) {
                $this->setVar( 'items', $dir->getContents( '*.jpg' ) );
            }
            if ( $_GET['tag'] == 'url' ) {
                $this->setVar( 'items', $dir->getContents( '*.php' ) );
            }

            // Output the template
            $this->outputTemplate( 'form1_selector' );

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
