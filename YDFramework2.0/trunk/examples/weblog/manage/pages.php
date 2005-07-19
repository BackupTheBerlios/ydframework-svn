<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Class definition
    class pages extends YDWeblogAdminRequest {

        // Class constructor
        function pages() {

            // Initialize the parent
            $this->YDWeblogAdminRequest();

            // Create the image upload form
            $this->form = new YDWeblogForm( 'pageForm', 'POST', YD_SELF_SCRIPT . '?do=edit' );
            $this->form->addElement( 'text', 'title', t('page_title'), array( 'class' => 'tfM' ) );
            $this->form->addElement( 'wladmintextarea', 'body', t('page_body'), array( 'class' => 'taM' ) );
            $this->form->addElement( 'datetimeselect', 'created',     t('created_on'), array( 'class' => 'tfM' ) );
            $this->form->addElement( 'hidden', 'id' );
            $this->form->addElement( 'submit', '_cmdSubmit', t('OK'), array( 'class' => 'button' ) );

            // Add the form rules
            $this->form->addRule( 'title', 'required', t( 'err_page_title' ) );
            $this->form->addRule( 'body', 'required', t( 'err_page_body' ) );

        }

        // Default action
        function actionDefault() {

            // Get the list of pages
            $pages = $this->weblog->getPages();

            // Get the pagesize and current page from the URL
            $page = @ $_GET['page'];

            // Create the YDRecordSet object
            $pages = new YDRecordSet( $pages, $page, YDConfig::get( 'YD_DB_DEFAULTPAGESIZE', 20 ) );

            // Assign it to the template
            $this->tpl->assign( 'pages', $pages );

            // Display the template
            $this->display();

        }

        // Edit a category
        function actionEdit() {

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // If there is something, set the defaults
            if ( $id != -1 ) {

                // Get the page by ID
                $defaults = $this->weblog->getPageById( $id );

                // Set the defaults
                $this->form->setDefaults( $defaults );

            }

            // Process the form
            if ( $this->form->validate() === true ) {

                // Get the form values
                $values = $this->form->getValues();

                // Update the datetimes
                $values['created'] =  $values['created']['timestamp'];
                $values['modified'] = $values['modified']['timestamp'];

                // If there is an ID, we do an edit
                if ( $values['id'] ) {

                    // Update the database
                    $this->weblog->updatePage( $values );

                } else {

                    // Add it to the database
                    $this->weblog->addPage( $values );

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

                // Delete the page
                $this->weblog->deletePage( $id );

            }

            // Redirect to the default acton
            $this->redirectToAction();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
