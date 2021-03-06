<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/include/YDWeblog_init.php' );

    // Includes
    YDInclude( 'YDForm.php' );

    // Class definition
    class item extends YDWeblogRequest {

        // Class constructor
        function item() {

            // Initialize the parent
            $this->YDWeblogRequest();

            // Disable caching
            $this->caching = false;

        }

        // Default action
        function actionDefault() {

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // Get the weblog details and go to the default view if none is matched
            $item  = @ $this->weblog->getPublicItemById( $id );
            $this->redirectIfMissing( $item );

            // Get the related items
            $related_items = $this->weblog->getRelatedItemsByItem( YDConfig::get( 'weblog_entries_fp', 5 ), $item );

            // Convert the list of images to a table of 3 columns
            $item['images_as_table'] = YDArrayUtil::convertToTable( $item['images'], 3, true );

            // Get the comments
            $comments = $this->weblog->getComments( $id );

            // Assign the variables to the template
            $this->tpl->assign( 'title', $item['title'] );
            $this->tpl->assign( 'item', $item );
            $this->tpl->assign( 'related_items', $related_items );
            $this->tpl->assign( 'comments', $comments );

            // Create the comments form
            $form = new YDWeblogForm(
                'comments', 'POST', YDTplModLinkItemRespond( $item ), '_self', array( 'id' => 'commentform' )
            );

            // Add the fields
            $form->addElement( 'text', 'username', t( 'name' ) );
            $form->addElement( 'text', 'useremail', t( 'mail_not_published' ) );
            $form->addElement( 'text', 'userwebsite', t( 'website' ) );
            $elem = & $form->addElement( 'captcha', 'security_code', t( 'enter_security_code' ) );
            $form->addElement( 'textarea', 'comment', '' );
            $form->addElement( 'submit', 'cmdSubmit', t( 'submit_comment' ), array( 'class' => 'button' ) );
            $form->addElement( 'hidden', 'item_id' );

            // Change the text position of the captcha element
            $elem->setTextPosition( true );

            // Set the defaults
            $defaults = array();
            $defaults['item_id']     = $id;
            $defaults['username']    = empty( $_COOKIE['YD_USER_NAME'] ) ? '' : $_COOKIE['YD_USER_NAME'];
            $defaults['useremail']   = empty( $_COOKIE['YD_USER_EMAIL'] ) ? '' : $_COOKIE['YD_USER_EMAIL'];
            $defaults['userwebsite'] = empty( $_COOKIE['YD_USER_WEBSITE'] ) ? '' : $_COOKIE['YD_USER_WEBSITE'];
            $form->setDefaults( $defaults );

            // Set the rules
            $form->addRule( 'username',      'required',      t( 'err_name' ) );
            $form->addRule( 'username',      'not_email',     t( 'err_name_email' ) );
            $form->addRule( 'username',      'maxlength',     t( 'err_name_length' ), 35 );
            $form->addRule( 'useremail',     'email',         t( 'err_email' ) );
            $form->addRule( 'useremail',     'required',      t( 'err_email' ) );
            $form->addRule( 'userwebsite',   'httpurl',       t( 'err_website' ) );
            $form->addRule( 'security_code', 'captcha',       t( 'err_security_code_not_valid' ) );
            $form->addRule( 'comment',       'required',      t( 'err_comment' ) );
            $form->addRule( 'comment',       'maxlength',     t( 'err_comment_length' ), YDConfig::get( 'max_comment_length', 1500 ) );
            $form->addRule( 'comment',       'maxhyperlinks', t( 'err_comment_links' ), YDConfig::get( 'max_comment_links', 1 ) );

            // Add the filters
            $form->addFilters( array( 'username', 'useremail', 'userwebsite' ), 'strip_html' );

            // Process the form
            if ( $form->validate() === true ) {

                // Post request, so check comment interval
                if ( $this->weblog->inSpamAttack() ) {
                    die( '<b>ERROR:</b> Comment interval exceeded. Refusing request.' );
                } else {
                    $this->weblog->spamCheckMark();
                }

                // Get the form values
                $values = $form->getValues();

                // Simple spam protection
                if ( ! empty( $values['userwebsite'] ) && strpos( $values['userwebsite'], '.' ) === false ) {
                    $this->redirect( YDTplModLinkItem( $item, '#comment-' . $comment_id ) );
                }

                // Fix any faulty web addresses
                if ( ! empty( $values['userwebsite'] ) && substr( strtolower( $values['userwebsite'] ), 0, 7 ) != 'http://' ) {
                    $values['userwebsite'] = 'http://' . $values['userwebsite'];
                }

                // Save the username, useremail and userwebsite
                setcookie( 'YD_USER_NAME',    $values['username'],    time() + 31536000, '/' );
                setcookie( 'YD_USER_EMAIL',   $values['useremail'],   time() + 31536000, '/' );
                setcookie( 'YD_USER_WEBSITE', $values['userwebsite'], time() + 31536000, '/' );

                // Add the values to the database
                $comment = $this->weblog->addComment( $values );

                // Send an email if configured
                if ( $comment['id'] > 0 && YDConfig::get( 'email_new_comment', true ) ) {

                    // Include the YDEmail library
                    YDInclude( 'YDEmail.php' );

                    // Get the list of subscriptions
                    $subscribers = $this->weblog->getCommentSubscribers( $id );

                    // Get the list of subscriptions
                    $users = $this->weblog->getUsers();

                    // Add the comment to the email template
                    $this->tpl->assign( 'eml_comment', $comment );

                    // Create the email and send it
                    $eml = new YDEmail();
                    if ( ! empty( $item['user_email'] ) ) {
                        $eml->setFrom( $item['user_email'], YDConfig::get( 'weblog_title', 'Untitled Weblog' ) );
                    } else {
                        $eml->setFrom( 'nobody@nowhere.com', YDConfig::get( 'weblog_title', 'Untitled Weblog' ) );
                    }
                    $eml->setReplyTo( 'no@reply.net' );
                    $eml->addBcc( $item['user_email'] );

                    // Spam emails do not go to the subscribers
                    if ( strval( $comment['is_spam'] ) == '0' ) {
                        foreach ( $subscribers as $subscriber ) {
                            $eml->addBcc( $subscriber );
                        }
                    }

                    // Email the item owners
                    foreach ( $users as $user ) {
                        $eml->addBcc( $user['email'], $user['name'] );
                    }

                    // Set the subject and body
                    if ( strval( $comment['is_spam'] ) == '0' ) {
                        $eml->setSubject( t('new_comment') . ': ' . strip_tags( $item['title'] ) );
                        $eml->setHtmlBody( $this->fetch( 'comment_email' ) );
                    } else {
                        $eml->setSubject( '[spam] ' . t('new_comment') . ': ' . strip_tags( $item['title'] ) );
                        $eml->setHtmlBody( $this->fetch( 'comment_email_spam' ) );
                    }

                    // Send the email
                    $eml->send();

                }

                // Clear the cache
                $this->clearCache();

                // Redirect to the item
                $this->redirect( YDTplModLinkItem( $item, '#comment-' . $comment['id'] ) );

            }

            // Add the form to the template
            $this->tpl->assignForm( 'comments_form', $form );

            // Display the template
            $this->display();

        }

        // Reserved action that creates the image 
        function actionShowCaptcha() {

            // include captcha lib
            YDInclude( 'YDCaptcha.php' );

            // Create captcha object 
            $captcha = new YDCaptcha();
            $captcha->_img->SetCharSet( "2-4,6,8-9,2-4,6,8-9" );

            // Return the image
            return $captcha->Create();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>