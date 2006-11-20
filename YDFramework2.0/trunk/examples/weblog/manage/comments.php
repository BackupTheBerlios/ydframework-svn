<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Class definition
    class comments extends YDWeblogAdminRequest {

        // Class constructor
        function comments() {
            $this->YDWeblogAdminRequest();
        }

        // Default action
        function actionDefault() {

            // Get the filter
            $filter = 'no_spam';
            if ( isset( $_GET['filter'] ) && strtolower( $_GET['filter'] ) == 'spam' ) {
                $filter = 'spam';
            }

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // If there is something, set the defaults
            if ( $id != -1 ) {

                // Get the list of comments
                $comments = $this->weblog->getComments( $id, 'CREATED DESC' );

                // Get the item details
                $item = $this->weblog->getItemById( $id );
                $this->tpl->assign( 'item', $item );

            } else {

                // Get the list of comments
                if ( $filter == 'no_spam' ) {
                    $comments = $this->weblog->getComments( null, 'CREATED DESC' );
                } else {
                    $comments = $this->weblog->getComments( null, 'CREATED DESC', -1, -1, false, true );
                }

            }

            // Get the pagesize and current page from the URL
            $page = @ $_GET['page'];

            // Create the YDRecordSet object
            $comments = new YDRecordSet( $comments, $page, YDConfig::get( 'YD_DB_DEFAULTPAGESIZE', 20 ) );

            // Assign it to the template
            $this->tpl->assign( 'comments', $comments );
            $this->tpl->assign( 'filter', $filter );

            // Display the template
            $this->display();

        }

        // Edit a comment
        function actionEdit() {

            // Create the edit form
            $form = new YDWeblogForm( 'commentForm', 'POST', YD_SELF_SCRIPT . '?do=edit' );
            $form->addElement( 'text',            'username',    t('name'),    array( 'class' => 'tfM' ) );
            $form->addElement( 'text',            'useremail',   t('mail'),    array( 'class' => 'tfM' ) );
            $form->addElement( 'text',            'userwebsite', t('website'), array( 'class' => 'tfM' ) );
            $form->addElement( 'textarea',        'comment',     t('comment'), array( 'class' => 'tfM' ) );
            $form->addElement( 'datetimeselect',  'created',     t('created_on'), array( 'class' => 'tfM' ) );
            $form->addElement( 'checkbox',        'is_spam',     t('is_spam'), array( 'style' => 'border: none;' ) );
            $form->addElement( 'hidden',          'id' );
            $form->addElement( 'submit',          '_cmdSubmit',  t('OK'),      array( 'class' => 'button' ) );

            // Add the form rules
            $form->addRule( 'username',    'required',  t( 'err_name' ) );
            $form->addRule( 'username',    'not_email', t( 'err_name_email' ) );
            $form->addRule( 'username',    'maxlength', t( 'err_name_length' ), 35 );
            $form->addRule( 'useremail',   'email',     t( 'err_email' ) );
            $form->addRule( 'useremail',   'required',  t( 'err_email' ) );
            $form->addRule( 'userwebsite', 'httpurl',   t( 'err_website' ) );
            $form->addRule( 'comment',     'required',  t( 'err_comment' ) );

            // Add the filters
            $form->addFilters( array( 'username', 'useremail', 'userwebsite' ), 'strip_html' );

            // Get the ID from the query string
            $id = $this->getIdFromQS();
            if ( $id == -1 ) {
                $id = $form->getValue( 'id' );
                if ( $id == '' ) {
                    $id = -1;
                }
            }

            // If there is something, set the defaults
            if ( $id != -1 ) {

                // Get the comment by ID
                $defaults = $this->weblog->getCommentById( $id );
                $defaults['comment'] = YDTemplate_modifier_bbcode( $defaults['comment'] );

                // Add delete button with existing items
                $form->addElement(
                    'button', '_cmdDelete', t('delete'),
                    array(
                        'class' => 'button',
                        'onClick' => 'return YDConfirmDeleteAndRedirect( \'' . addslashes( smarty_modifier_truncate( trim( strip_tags( $defaults['comment'] ) ) ) ) . '\', \'' . YD_SELF_SCRIPT . '?do=delete&id=' . $defaults['id'] . '\' );'
                    )
                );

                // Set the defaults
                $form->setDefaults( $defaults );

                // Add the comment to the template
                $this->tpl->assign( 'comment', $defaults );

            }

            // Check if the comment exists
            if ( $form->getValue( 'id' ) == '' ) {
                $this->redirectToAction();
            }

            // Process the form
            if ( $form->validate() === true  ) {

                // Get the form values
                $values = $form->getValues();

                // Update the datetimes
                $values['created'] =  $values['created']['timestamp'];

                // Update the database
                $this->weblog->updateComment( $values );

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
            $id = $this->getIdFromQS();
            if ( $id ) {
                $this->weblog->deleteComment( $id );
            }
            $this->redirectToAction();
        }

        // Action to mark a comment as spam
        function actionMark_as_spam() {
            $id = $this->getIdFromQS();
            if ( $id ) {
                $this->weblog->updateCommentAsSpam( $id );
            }
            $this->redirect( YD_SELF_SCRIPT . '?filter=spam' );
        }

        // Action to unmark a comment as spam
        function actionMark_as_not_spam() {
            $id = $this->getIdFromQS();
            if ( $id ) {
                $this->weblog->updateCommentAsNotSpam( $id );
            }
            $this->redirectToAction();
        }

        // Empty the spam
        function actionEmptySpam() {
            $this->weblog->emptySpam();
            $this->redirect( YD_SELF_SCRIPT . '?filter=spam' );
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
