<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Class definition
    class links extends YDWeblogAdminRequest {

        // Class constructor
        function links() {

            // Initialize the parent
            $this->YDWeblogAdminRequest();

            // Create the image upload form
            $this->form = new YDWeblogForm( 'linkForm', 'POST', YD_SELF_SCRIPT . '?do=edit' );
            $this->form->addElement( 'text',   'title', t('link_title'), array( 'class' => 'tfM' ) );
            $this->form->addElement( 'text',   'url', t('link_url'), array( 'class' => 'tfM' ) );
            $this->form->addElement( 'hidden', 'id' );
            $this->form->addElement( 'submit', '_cmdSubmit', t('OK'), array( 'class' => 'button' ) );

            // Add the form rules
            $this->form->addRule( 'title', 'required', t( 'err_link_title' ) );
            $this->form->addRule( 'url', 'required', t( 'err_link_url' ) );

            // Add the filters
            $this->form->addFilters( array( 'title', 'url' ), 'strip_html' );

        }

        // Default action
        function actionDefault() {

            // Get the list of links
            $links = $this->weblog->getLinks();

            // Get the pagesize and current page from the URL
            $page = @ $_GET['page'];

            // Create the YDRecordSet object
            $links = new YDRecordSet( $links, $page, YDConfig::get( 'YD_DB_DEFAULTPAGESIZE', 20 ) );

            // Assign it to the template
            $this->tpl->assign( 'links', $links );

            // Display the template
            $this->display();

        }

        // Edit a category
        function actionEdit() {

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // If there is something, set the defaults
            if ( $id != -1 ) {

                // Get the link by ID
                $defaults = $this->weblog->getLinkById( $id );

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

                // Add a rule to make sure the link is unique
                $this->form->addFormRule( array( & $this, 'checkUniqueUrl' ) );

            }

            // Process the form
            if ( $this->form->validate() === true ) {

                // Get the form values
                $values = $this->form->getValues();

                // If there is an ID, we do an edit
                if ( $values['id'] ) {

                    // Update the database
                    $this->weblog->updateLink( $values );

                } else {

                    // Add it to the database
                    $this->weblog->addLink( $values );

                }

                // Redirect to the default acton
                $this->redirectToAction();

            }

            // Add the form to the template
            $this->tpl->assignForm( 'form', $this->form );

            // Display the template
            $this->display();

        }

        // Delete a category
        function actionDelete() {

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // If there is something, set the defaults
            if ( $id ) {

                // Delete the link
                $this->weblog->deleteLink( $id );

            }

            // Redirect to the default acton
            $this->redirectToAction();

        }

        // Check for a unique category
        function checkUniqueUrl( $values ) {

            // Get the category by name
            $link = $this->weblog->getLinkByUrl( $values['url'] );

            // Return an error if it exists
            if ( $link ) {
                return array( '__ALL__' => t( 'err_unique_link' ) );
            } else {
                return true;
            }

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
