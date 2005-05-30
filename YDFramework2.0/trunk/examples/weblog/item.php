<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/include/YDWeblog_init.php' );

    // Includes
    YDInclude( 'YDForm.php' );

    // Class definition
    class item extends YDWeblogRequest {

        // Class constructor
        function item() {
            $this->YDWeblogRequest();
        }

        // Default action
        function actionDefault() {

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // Get the weblog details and go to the default view if none is matched
            $item  = @ $this->weblog->getItemById( $id );
            $this->redirectIfMissing( $item );

            // Convert the list of images to a table of 3 columns
            $item['images_as_table'] = YDArrayUtil::convertToTable( $item['images'], 3, true );

            // Get the comments
            $comments = $this->weblog->getComments( $id );

            // Add them to the template
            $this->tpl->assign( 'item', $item );
            $this->tpl->assign( 'comments', $comments );

            // Create the comments form
            $form = new YDWeblogForm(
                'comments', 'POST', YDTplModLinkItemRespond( $item ), '_self', array( 'id' => 'commentform' )
            );

            // Add the fields
            $form->addElement( 'text', 'username', t( 'name' ) );
            $form->addElement( 'text', 'useremail', t( 'mail_not_published' ) );
            $form->addElement( 'text', 'userwebsite', t( 'website' ) );
            $form->addElement( 'wlbbtextarea', 'comment', '', array( 'style' => 'width: 450px' ) );
            $form->addElement( 'submit', 'cmdSubmit', t( 'submit_comment' ), array( 'id' => 'submit' ) );
            $form->addElement( 'hidden', 'item_id' );

            // Add the form filters
            $form->addFilter( 'username',    'utf8_decode' );
            $form->addFilter( 'useremail',   'utf8_decode' );
            $form->addFilter( 'userwebsite', 'utf8_decode' );
            $form->addFilter( 'comment',     'utf8_decode' );

            // Set the defaults
            $defaults = array();
            $defaults['item_id']     = $id;
            $defaults['username']    = empty( $_COOKIE['YD_USER_NAME'] ) ? '' : $_COOKIE['YD_USER_NAME'];
            $defaults['useremail']   = empty( $_COOKIE['YD_USER_EMAIL'] ) ? '' : $_COOKIE['YD_USER_EMAIL'];
            $defaults['userwebsite'] = empty( $_COOKIE['YD_USER_WEBSITE'] ) ? '' : $_COOKIE['YD_USER_WEBSITE'];
            $form->setDefaults( $defaults );

            // Set the rules
            $form->addRule( 'username',  'required', t( 'err_name' ) );
            $form->addRule( 'useremail', 'email',    t( 'err_email' ) );
            $form->addRule( 'comment',   'required', t( 'err_comment' ) );

            // Process the form
            if ( $form->validate() === true ) {

                // Get the form values
                $values = $form->getValues();

                // Save the username, useremail and userwebsite
                setcookie( 'YD_USER_NAME',    $values['username'],    time() + 31536000, '/' );
                setcookie( 'YD_USER_EMAIL',   $values['useremail'],   time() + 31536000, '/' );
                setcookie( 'YD_USER_WEBSITE', $values['userwebsite'], time() + 31536000, '/' );

                // Add the values to the database
                $comment_id = $this->weblog->addComment( $values );

                // Send an email if configured
                if ( YDConfig::get( 'email_new_comment', true ) === true ) {

                    // Include the YDEmail library
                    YDInclude( 'YDEmail.php' );

                    // Get the list of subscriptions
                    $subscribers = $this->weblog->getCommentSubscribers( $id );

                    // Add the comment to the email template
                    $this->tpl->assign( 'eml_comment', $values );

                    // Create the email and send it
                    $eml = new YDEmail();
                    $eml->setFrom( $item['user_email'], YDConfig::get( 'weblog_title', 'Untitled Weblog' ) );
                    $eml->setReplyTo( 'no@reply.net' );
                    $eml->addBcc( $item['user_email'] );
                    foreach ( $subscribers as $subscriber ) {
                        $eml->addBcc( $subscriber );
                    }
                    $eml->setSubject( 'New comment: ' . strip_tags( $item['title'] ) );
                    $eml->setHtmlBody( $this->fetch( 'comment_email' ) );
                    $eml->send();

                }

                // Redirect to the item
                $this->redirect( YDTplModLinkItem( $item, '#comment-' . $comment_id ) );

            }

            // Add the form to the template
            $this->tpl->assignForm( 'comments_form', $form );

            // Display the template
            $this->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>