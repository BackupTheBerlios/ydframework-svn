<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Class definition
    class items extends YDWeblogAdminRequest {

        // Class constructor
        function items() {

            // Initialize the parent
            $this->YDWeblogAdminRequest();

        }

        // Default action
        function actionDefault() {

            // Get the list of items
            $items = $this->weblog->getItems();

            // Get the pagesize and current page from the URL
            $page = @ $_GET['page'];

            // Create the YDRecordSet object
            $items = new YDRecordSet( $items, $page, intval( YDConfig::get( 'YD_DB_DEFAULTPAGESIZE', 20 ) / 2 ) );

            // Assign it to the template
            $this->tpl->assign( 'items', $items );

            // Display the template
            $this->display();

        }

        // Edit a comment
        function actionEdit() {

            // Get the list of categories
            $categories = $this->weblog->getCategoriesAsAssoc();

            // Create the edit form
            $form = new YDWeblogForm( 'itemForm', 'POST', YD_SELF_SCRIPT . '?do=edit' );
            $form->addElement( 'text',            'title',          t('item_title'),       array( 'class' => 'tfM' ) );
            $form->addElement( 'wladmintextarea', 'body',           t('item_body'),        array( 'class' => 'tfM' ) );
            $form->addElement( 'wladmintextarea', 'body_more',      t('item_body_more'),   array( 'class' => 'tfM' ) );
            $form->addElement( 'select',          'category_id',    t('category'),         array( 'class' => 'tfM', 'style' => 'width: 100%' ), $categories );
            $form->addElement( 'datetimeselect',  'created',        t('created_on'),       array( 'class' => 'tfM' ) );
            $form->addElement( 'datetimeselect',  'modified',       t('last_modified_on'), array( 'class' => 'tfM' ) );
            $form->addElement( 'checkbox',        'allow_comments', t('allow_comments'),   array( 'style' => 'border: none;' ) );
            $form->addElement( 'checkbox',        'auto_close',     t('auto_close_item'),  array( 'style' => 'border: none;' ) );
            $form->addElement( 'checkbox',        'is_draft',       t('is_draft'),         array( 'style' => 'border: none;' ) );
            $form->addElement( 'hidden',          'id' );
            $form->addElement( 'submit',          '_cmdSubmit',  t('OK'),                  array( 'class' => 'button' ) );

            // Add the form rules
            $form->addRule( 'title',  'required', t( 'err_item_title' ) );
            $form->addRule( 'body',   'required', t( 'err_item_body' ) );

            // Add the filters
            $form->addFilters( array( 'title' ), 'strip_html' );

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // If there is something, set the defaults
            if ( $id != -1 ) {

                // Get the comment by ID
                $defaults = $this->weblog->getItemById( $id );
                $defaults['modified'] = gmmktime();

                // Assign the values to the template
                $this->tpl->assign( 'item', $defaults );

                // Set the defaults
                $form->setDefaults( $defaults );

            }

            // Process the form
            if ( $form->validate() === true ) {

                // Get the form values
                $values = $form->getValues();

                // Update the datetimes
                $values['created'] =  $values['created']['timestamp'];
                $values['modified'] = $values['modified']['timestamp'];

                // Set the user
                $values['user_id'] = $this->user['id'];

                // If there is an ID, we do an edit
                if ( $values['id'] ) {

                    // Update the database
                    $this->weblog->updateItem( $values );

                } else {

                    // Add it to the database
                    $this->weblog->addItem( $values );

                }

                // Redirect to the default acton
                $this->redirectToAction();

            }

            // Add the form to the template
            $this->tpl->assignForm( 'form', $form );

            // Display the template
            $this->display();

        }

        // Delete a category
        function actionDelete() {

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // If there is something, set the defaults
            if ( $id ) {

                // Delete the item
                $this->weblog->deleteItem( $id );

            }

            // Redirect to the default acton
            $this->redirectToAction();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
