<?php

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDFileSystem.php' );

    // Class definition
    class form extends YDRequest {

        // Class constructor
        function form() {
            $this->YDRequest();
            $this->template = new YDTemplate();
        }

        // Default action
        function actionDefault() {

            // Set the title of the form
            $this->template->assign( 'title', 'Sample form' );

            // Mark the form as not valid
            $this->template->assign( 'formValid', false );

            // Create the form
            $form = new YDForm( 'firstForm' );

            // Set the defaults
            $form->setDefaults( array( 'name' => 'Joe User' ) );

            // Add the elements
            $form->addElement( 'text', 'name', 'Enter your name:', array( 'size' => 50 ) );
            $form->addElement( 'bbtextarea', 'desc1', 'Enter the description:' );
            $form->addElement( 'bbtextarea', 'desc2', 'Enter the description (no toolbar):' );
            $form->addElement( 'bbtextarea', 'desc3', 'Enter the description:' );
            $form->addElement( 'submit', 'cmdSubmit', 'Send' );

            // Update the no toolbar element
            $element = & $form->getElement( 'desc2' );
            $element->clearButtons();

            // Add a popup window to the third description
            $element = & $form->getElement( 'desc3' );
            $element->addPopupWindow( 'form.php?do=selector&field=firstForm_desc3&tag=img', 'select image' );
            $element->addPopupWindow( 'form.php?do=selector&field=firstForm_desc3&tag=url', 'select url' );

            // Apply a filter
            $form->addFilter( 'name', 'trim' );

            // Add a rule
            $form->addRule( 'name', 'required', 'Please enter your name' );

            // Process the form
            if ( $form->validate() ) {

                // Mark the form as valid
                $this->template->assign( 'formValid', true );

            }

            // Add the form to the template
            $this->template->assignForm( 'form', $form );

            // Output the template
            $this->template->display();

        }

        function actionSelector() {

            // Redirect to the main if no field in the url
            if ( empty( $_GET['field'] ) ) {
                $this->forward( 'default' );
            }

            // Get the list of images in the current directory
            $dir = new YDFSDirectory();

            // Start with no items
            $items = array();

            // Add the list of images to the template
            if ( $_GET['tag'] == 'img' ) {
                $pattern = '*.jpg';
            }
            if ( $_GET['tag'] == 'url' ) {
                $pattern = '*.php';
            }

            // Get the item list
            foreach( $dir->getContents( $pattern ) as $item ) {
                array_push( $items, $item->getBaseName() );
            }

            // Add the items to the template
            $this->template->assign( 'items', $items );

            // Output the template
            $this->template->display( 'form_selector' );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
