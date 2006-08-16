<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Class definition
    class comments extends YDWeblogAdminRequest {

        // Class constructor
        function comments() {

            // Initialize the parent
            $this->YDWeblogAdminRequest();

        }

        // Default action
        function actionDefault() {

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
                $comments = $this->weblog->getComments( null, 'CREATED DESC' );

            }

            // Get the pagesize and current page from the URL
            $page = @ $_GET['page'];

            // Create the YDRecordSet object
            $comments = new YDRecordSet( $comments, $page, YDConfig::get( 'YD_DB_DEFAULTPAGESIZE', 20 ) );

            // Assign it to the template
            $this->tpl->assign( 'comments', $comments );

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
            $form->addElement( 'wladmintextarea', 'comment',     t('comment'), array( 'class' => 'tfM' ) );
            $form->addElement( 'datetimeselect',  'created',     t('created_on'), array( 'class' => 'tfM' ) );
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

            // If there is something, set the defaults
            if ( $id != -1 ) {

                // Get the comment by ID
                $defaults = $this->weblog->getCommentById( $id );
                $defaults['comment'] = YDTemplate_modifier_bbcode( $defaults['comment'] );

                // Set the defaults
                $form->setDefaults( $defaults );

                // Add the comment to the template
                $this->tpl->assign( 'comment', $defaults );

            }

            // Check if the comment exists
            if ( $form->getValue( 'id' ) == '' ) {

                // Return to the default action
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

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // If there is something, set the defaults
            if ( $id ) {

                // Delete the comment
                $this->weblog->deleteComment( $id );

            }

            // Redirect to the default acton
            $this->redirectToAction();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
