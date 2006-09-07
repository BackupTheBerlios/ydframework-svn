<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Class definition
    class categories extends YDWeblogAdminRequest {

        // Class constructor
        function categories() {

            // Initialize the parent
            $this->YDWeblogAdminRequest();

            // Create the image upload form
            $this->form = new YDWeblogForm( 'categoryForm', 'POST', YD_SELF_SCRIPT . '?do=edit' );
            $this->form->addElement( 'text',   'title', t('category'), array( 'class' => 'tfM' ) );
            $this->form->addElement( 'hidden', 'id' );
            $this->form->addElement( 'submit', '_cmdSubmit', t('OK'), array( 'class' => 'button' ) );

            // Add the form rules
            $this->form->addRule( 'title', 'required', t( 'err_category_title' ) );

            // Add the filters
            $this->form->addFilters( array( 'title' ), 'strip_html' );

        }

        // Default action
        function actionDefault() {

            // Get the list of categories
            $categories = $this->weblog->getCategories();

            // Get the pagesize and current page from the URL
            $page = @ $_GET['page'];

            // Create the YDRecordSet object
            $categories = new YDRecordSet( $categories, $page, YDConfig::get( 'YD_DB_DEFAULTPAGESIZE', 20 ) );

            // Assign it to the template
            $this->tpl->assign( 'categories', $categories );

            // Assign the quick adds form to the template
            $this->tpl->assignForm( 'form', $this->form );

            // Display the template
            $this->display();

        }

        // Edit a category
        function actionEdit() {

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // If there is something, set the defaults
            if ( $id != -1 ) {

                // Get the category by ID
                $defaults = $this->weblog->getCategoryById( $id );

                // Add delete button with existing items
                $this->form->addElement(
                    'button', '_cmdDelete', t('delete'),
                    array(
                        'class' => 'button',
                        'onClick' => 'return YDConfirmDeleteAndRedirect( \'' . addslashes( $defaults['title'] ) . '\', \'' . YD_SELF_SCRIPT . '?do=delete&id=' . $defaults['id'] . '\' );'
                    )
                );

                // Set the defaults
                $this->form->setDefaults( $defaults );

            }

            // Add a form rule for new links
            if ( $this->form->getValue( 'id' ) == '' ) {
                $this->form->addFormRule( array( & $this, 'checkUniqueCategory' ) );
            }

            // Process the form
            if ( $this->form->validate() === true ) {
                $values = $this->form->getValues();
                if ( $values['id'] ) {
                    $this->weblog->updateCategory( $values );
                } else {
                    $this->weblog->addCategory( $values );
                }
                $this->redirectToAction();
            }

            // Add the form to the template
            $this->tpl->assignForm( 'form', $this->form );

            // Display the template
            $this->display();

        }

        // Delete a category
        function actionDelete() {
            $id = $this->getIdFromQS();
            if ( $id ) {
                $this->weblog->deleteCategory( $id );
            }
            $this->redirectToAction();
        }

        // Check for a unique category
        function checkUniqueCategory( $values ) {
            $category = $this->weblog->getCategoryByName( $values['title'] );
            if ( $category ) {
                return array( '__ALL__' => t( 'err_unique_category' ) );
            } else {
                return true;
            }
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
