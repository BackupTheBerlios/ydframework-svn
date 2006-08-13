<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Class definition
    class users extends YDWeblogAdminRequest {

        // Class constructor
        function users() {

            // Initialize the parent
            $this->YDWeblogAdminRequest();

        }

        // Default action
        function actionDefault() {

            // Get the list of users
            $users = $this->weblog->getUsers();

            // Add them to the template
            $this->tpl->assign( 'users', $users );

            // Display the template
            $this->display();

        }

        // Add/edit a user
        function actionEdit() {

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // Create a new form
            $form = new YDWeblogForm( 'userForm' );

            // Add the elements
            $form->addElement( 'hidden', 'id', '' );
            if ( $id == '-1' ) {
                $form->addElement( 'text', 'name', t('username'), array( 'class' => 'tfM' ) );
            } else {
                $form->addElement( 'text', 'name', t('username'), array( 'class' => 'tfM', 'disabled' => '' ) );
            }
            $form->addElement( 'text', 'email', t('useremail'), array( 'class' => 'tfM' ) );
            $form->addElement( 'password', 'password', t('password'), array( 'class' => 'tfM' ) );
            $form->addElement( 'submit', 'cmdSubmit', t('save'), array( 'class' => 'button' ) );

            // Apply filters
            $form->addFilter( '__ALL__', 'trim' );
            $form->addFilters( array( 'name', 'email' ), 'strip_html' );

            // Add the rules
            $form->addRule( 'email', 'required', t('req_useremail') );
            $form->addRule( 'email', 'email', t('req_useremail') );
            if ( $id == '-1' ) {
                $form->addRule( 'name', 'required', t('err_username') );
                $form->addRule( 'password', 'required', t('req_loginpass') );
            }
            $form->addFormRule( array( & $this, 'checkUserCredentials' ) );

            // Set the defaults
            if ( $id != '-1' ) {

                // Get the user data
                $user = $this->weblog->getUserByID( $id );
                unset( $user['password'] );

                // Set the form defaults
                $form->setDefaults( $user );

                // Add this to the template
                $this->tpl->assign( 'user_data', $user );

            }

            // Validate the form
            if ( $form->validate() == true ) {

                // Get the form values
                $values = $form->getValues();

                // Save the userdata
                $this->weblog->saveUser( $values );

                // Go to the default view
                $this->redirectToAction();

            }

            // Add the form to the template
            $this->tpl->assignForm( 'form', $form );

            // Display the template
            $this->display();

        }

        // Delete a user
        function actionDelete() {

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // Get the ID of the admin user
            $admin = $this->weblog->getUserByName( 'administrator' );

            // Update the items table and change the users
            $this->weblog->replaceUser( $id, $admin['id'] );

            // Delete the user
            $this->weblog->deleteUser( $id );

            // Redirect to the default action
            $this->redirectToAction();

        }

        // Check the user credentials
        function checkUserCredentials( $params ) {

            // Check if the username already exists
            $user = $this->weblog->getUserByName( $params['name'] );

            // Return an error if the user exists already
            if ( $user ) {
                return array( '__ALL__' => t('user_dup') );
            } else {
                return true;
            }

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
