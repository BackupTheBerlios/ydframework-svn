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

            // Get the filter
            $filter = 'active';
            if ( isset( $_GET['filter'] ) && strtolower( $_GET['filter'] ) == 'drafts' ) {
                $filter = 'drafts';
            }

            // Get the list of items
            if ( $filter == 'active' ) {
                $items = $this->weblog->getItems( -1, -1, 'created desc', 'AND is_draft = 0' );
            } else {
                $items = $this->weblog->getItems( -1, -1, 'created desc', 'AND is_draft = 1' );
            }

            // Get the pagesize and current page from the URL
            $page = @ $_GET['page'];

            // Create the YDRecordSet object
            $items = new YDRecordSet( $items, $page, intval( YDConfig::get( 'YD_DB_DEFAULTPAGESIZE', 20 ) / 2 ) );

            // Assign it to the template
            $this->tpl->assign( 'items',  $items );
            $this->tpl->assign( 'filter', $filter );

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
                $defaults['body'] = YDTemplate_modifier_bbcode( $defaults['body'] );
                $defaults['body_more'] = YDTemplate_modifier_bbcode( $defaults['body_more'] );
                $defaults['modified'] = gmmktime();

                // Add delete button with existing items
                $form->addElement(
                    'button', '_cmdDelete', t('delete'),
                    array(
                        'class' => 'button',
                        'onClick' => 'return YDConfirmDeleteAndRedirect( \'' . addslashes( $defaults['title'] ) . '\', \'' . YD_SELF_SCRIPT . '?do=delete&id=' . $defaults['id'] . '\' );'
                    )
                );

                // Assign the values to the template
                $this->tpl->assign( 'item', $defaults );

                // Set the defaults
                $form->setDefaults( $defaults );

            } else {

                // Get the defaults
                $defaults = array();
                $defaults['is_draft'] = YDConfig::get( 'dflt_is_draft', false );

                // Set the form defaults
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

                // Check if we need to publish or not
                $needs_publish_email = false;

                // If there is an ID, we do an edit
                if ( $values['id'] ) {

                    // Get the item by ID
                    $item = $this->weblog->getItemByID( $values['id'] );

                    // Check if the item changes from draft to published
                    if ( $item['is_draft'] && ! $values['is_draft'] ) {

                        // Remember this
                        $needs_publish_email = true;

                        // Update the created timestamp
                        $values['created'] =  time();
                        $values['modified'] = time();

                    }

                    // Update the database
                    $this->weblog->updateItem( $values );

                } else {

                    // Check if the item is draft or not
                    if ( ! $values['is_draft'] ) {

                        // Remember this
                        $needs_publish_email = true;

                        // Update the created timestamp
                        $values['created'] =  time();
                        $values['modified'] = time();

                    }

                    // Add it to the database
                    $this->weblog->addItem( $values );

                }

                // Check if we need to send an email
                if ( $needs_publish_email ) {

                    // Send an email if configured
                    if ( YDConfig::get( 'email_new_item', true ) ) {

                        // Add the item ID
                        $values['id'] = $this->weblog->db->getLastInsertID();

                        // Include the YDEmail library
                        YDInclude( 'YDEmail.php' );

                        // Get the list of subscriptions
                        $subscribers = $this->weblog->getUsers();

                        // Add the comment to the email template
                        $this->tpl->assign( 'item', $values );
                        $this->tpl->assign( 'weblog_link', YDUrl::makeLinkAbsolute( '../index.php' ) );
                        $this->tpl->assign( 'item_link', YDTplModLinkWithID( '../item.php', $values['id'] ) );

                        // Create the email and send it
                        $eml = new YDEmail();
                        if ( ! empty( $item['user_email'] ) ) {
                            $eml->setFrom( $item['user_email'], YDConfig::get( 'weblog_title', 'Untitled Weblog' ) );
                        } else {
                            $eml->setFrom( 'nobody@nowhere.com', YDConfig::get( 'weblog_title', 'Untitled Weblog' ) );
                        }
                        $eml->setReplyTo( 'no@reply.net' );
                        foreach ( $subscribers as $subscriber ) {
                            $eml->addBcc( $subscriber['email'], $subscriber['name'] );
                        }
                        $eml->setSubject( t('new_item') . ': ' . strip_tags( $values['title'] ) );
                        $eml->setHtmlBody( $this->fetch( dirname( __FILE__ ) . '/../' . $this->dir_skins . $this->skin . '/item_email.tpl' ) );
                        $eml->send();

                    }

                }

                // Redirect to the default action
                if ( $values['is_draft'] ) {
                    $this->redirect( YD_SELF_SCRIPT . '?filter=drafts' );
                } else {
                    $this->redirectToAction();
                }

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
