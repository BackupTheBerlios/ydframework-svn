<?php

    // Define the default pagesize
    YDConfig::set( 'YD_DB_DEFAULTPAGESIZE', 20 );

    // Class for an admin request
    class YDWeblogAdminRequest extends YDWeblogRequest {

        // Class constructor
        function YDWeblogAdminRequest() {

            // Initialize the parent
            $this->YDWeblogRequest();

            // This requires authentication
            $this->setRequiresAuthentication( true );

            // Start with no userdata and check the authentication
            $this->user = null;

            // Change the template directory
            $this->tpl->template_dir = YD_SELF_DIR;

        }

        // Init the template
        function initTemplate() {

            // Assign the userdata to the template
            $this->tpl->assign( 'user', $this->user );

            // Assign the weblog details
            $this->tpl->assign( 'weblog_title',       YDConfig::get( 'weblog_title', 'Untitled Weblog' ) );
            $this->tpl->assign( 'weblog_description', YDConfig::get( 'weblog_description', 'Untitled Weblog Description' ) );

            // Add the different directories to the template
            $this->tpl->assign( 'uploads_dir', $this->dir_uploads );

        }

        // Redirect if an item is missing
        function redirectIfMissing( $test ) {
            if ( ! $test ) {
                $this->redirectToAction();
            }
        }

        // The login action
        function actionLogin() {

            // Redirect to default action if already logged in
            if ( $this->isAuthenticated() == true || ! is_null( $this->user ) ) {
                $this->forward( 'default' );
                return;
            }

            // Create the login form
            $form = new YDWeblogForm( 'loginForm' );

            // Check if the login name exists
            if ( ! empty( $_COOKIE[ 'YD_USER_NAME' ] ) ) {
                $form->setDefaults( array( 'loginName' => $_COOKIE['YD_USER_NAME'] ) );
            }

            // Add the elements
            $form->addElement( 'text', 'loginName', t( 'username' ), array( 'class'=>'tfS' ) );
            $form->addElement( 'password', 'loginPass', t( 'password' ), array( 'class'=>'tfS' ) );
            $form->addElement( 'submit', 'cmdSubmit', t( 'login' ), array( 'class'=>'button' ) );

            // Add the element rules
            $form->addRule( 'loginName', 'required', t( 'err_username' ) );
            $form->addRule( 'loginPass', 'required', t( 'err_password' ) );

            // Add the rules
            $form->addFormRule( array( & $this, 'checkLogin' ) );

            // Process the form
            if ( $form->validate() == true ) {

                // Get the form values
                $values = $form->getValues();

                // Set the cookies
                setcookie( 'YD_USER_NAME', $values['loginName'], time() + 31536000, '/' );
                setcookie( 'YD_USER_PASS', md5( $values['loginPass'] ), time() + 31536000, '/' );

                // Set the username
                $this->username = $values['loginName'];

                // Forward to the main manage page
                $this->redirect( 'index.php' );

            }

            // Add the form to the template
            $this->tpl->assignForm( 'form', $form );

            // Output the template
            $this->display( 'login' );

        }


        // Logout action
        function actionLogout() {
            setcookie( 'YD_USER_PASS', null, time() - 31536000, '/' );
            $this->redirectToAction( 'login' );
        }

        // Check for authentication
        function isAuthenticated() {
            if ( ! empty( $_COOKIE[ 'YD_USER_NAME' ] ) && ! empty( $_COOKIE[ 'YD_USER_PASS' ] ) ) {
                $fields = array( 'loginName' => $_COOKIE[ 'YD_USER_NAME' ], 'loginPass' => $_COOKIE[ 'YD_USER_PASS' ] );
                if ( $this->checkLogin( $fields, true ) === true ) {
                    $this->username = $_COOKIE['YD_USER_NAME'];
                    $this->authenticationSucceeded();
                    return true;
                }
            }
            return false;
        }

        // Function to check the login
        function checkLogin( $fields, $md5=false ) {
            if ( ! isset( $this->username ) ) {
                if ( $md5 === false ) {
                    $fields['loginPass'] = md5( $fields['loginPass'] );
                }
                $result = $this->weblog->checkLogin( $fields['loginName'], $fields['loginPass'] );
                if ( $result === false ) {
                    return array( '__ALL__' => t( 'err_login_all' ) );
                } else {
                    $this->user = $result;
                    return true;
                }
            }
        }

        // Failed authentication, forwards to the login action
        function authenticationFailed() {
            $this->forward( 'login' );
        }

        // Function to disbable request logging
        function _logRequest() {
        }

    }

?>
