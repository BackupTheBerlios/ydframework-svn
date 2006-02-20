<?php

    // Define the default pagesize
    YDConfig::set( 'YD_DB_DEFAULTPAGESIZE', 40 );

    // The base request object
    class YDWeblogRequest extends YDRequest {

        // Class constructor
        function YDWeblogRequest() {

            // Initialize the parent
            $this->YDRequest();

            // Check if we allow caching
            $this->caching = true;

            // Delete the cache if caching is disabled
            if ( YDConfig::get( 'use_cache', false ) === false ) {
                $this->clearCache();
            }

            // Start with no userdata and check the authentication
            $this->user = null;

            // This requires authentication
            $this->setRequiresAuthentication( true );

            // Setup the weblog object
            $this->weblog = new YDWeblogAPI();

            // Get the skin
            $this->skin = YDConfig::get( 'weblog_skin', 'default' );

            // Get the shortcuts to the directories
            $this->dir_uploads = YDConfig::get( 'dir_uploads', 'uploads' );
            $this->dir_skins   = YDConfig::get( 'dir_skins',   'skins' ) . '/';

            // Initialize the template
            $this->tpl = new YDTemplate();
            $this->tpl->template_dir = dirname( __FILE__ ) . '/../' . $this->dir_skins . $this->skin . '/';

            // Register the modifiers
            $this->tpl->register_function( 't', 'YDTplModT' );
            $this->tpl->register_modifier( 'bbcode', 'YDTplModBBCode' );
            $this->tpl->register_modifier( 'date', 'YDTplModDate' );
            $this->tpl->register_modifier( 'link_item', 'YDTplModLinkItem' );
            $this->tpl->register_modifier( 'link_item_gallery', 'YDTplModLinkItemGallery' );
            $this->tpl->register_modifier( 'link_item_images', 'YDTplModLinkItemImages' );
            $this->tpl->register_modifier( 'link_item_comment', 'YDTplModLinkItemComment' );
            $this->tpl->register_modifier( 'link_item_respond', 'YDTplModLinkItemRespond' );
            $this->tpl->register_modifier( 'link_category', 'YDTplModLinkCategory' );
            $this->tpl->register_modifier( 'link_page', 'YDTplModLinkPage' );
            $this->tpl->register_modifier( 'link_link', 'YDTplModLinkLink' );
            $this->tpl->register_modifier( 'link_thumb', 'YDTplModLinkThumb' );
            $this->tpl->register_modifier( 'link_thumb_small', 'YDTplModLinkThumbSmall' );
            $this->tpl->register_modifier( 'text_num_comments', 'YDTplModTextNumComments' );
            $this->tpl->register_modifier( 'text_num_images', 'YDTplModTextNumImages' );

            // Get the browser information
            $this->browser = new YDBrowserInfo();

        }

        // Clear the cache
        function clearCache() {
            if ( $handle = opendir( YD_DIR_TEMP ) ) {
                while ( false !== ( $file = readdir( $handle ) ) ) {
                    if ( substr( $file, 0, 6 ) == YD_WEBLOG_CACHE_PREFIX && strrchr( $file, '.' ) ) {
                        if ( substr( strrchr( $file, '.' ), 1 ) == YD_WEBLOG_CACHE_SUFFIX ) {
                            @unlink( YD_DIR_TEMP . '/' . $file );
                        }
                    }
                }
                closedir( $handle );
            }
        }

        // Function to show a thumbnail
        function actionThumbnail() {

            // Get the image name
            if ( ! isset( $_GET['id'] ) ) {
                die( 'No image selected.' );
            }

            // Create a new image object
            $img = new YDFSImage( dirname( __FILE__ ) . '/../' . YDConfig::get( 'dir_uploads', 'uploads' ) . '/' . $_GET['id'] );

            // Output the thumbnail
            $img->outputThumbnail( 100, 100 );

        }

        // Function to show a thumbnail
        function actionThumbnailSmall() {

            // Get the image name
            if ( ! isset( $_GET['id'] ) ) {
                die( 'No image selected.' );
            }

            // Create a new image object
            $img = new YDFSImage( YDConfig::get( 'dir_uploads', 'uploads' ) . '/' . $_GET['id'] );

            // Output the thumbnail
            $img->outputThumbnail( 48, 48 );

        }

        // Function to get the ID from the query string
        function getIdFromQS() {
            return @is_numeric( $_GET['id'] ) ? intval( $_GET['id'] ) : -1;
        }

        // Redirect if an item is missing
        function redirectIfMissing( $test ) {
            if ( ! $test ) {
                $this->redirect( 'index.php' );
            }
        }

        // Init the template
        function initTemplate() {

            // Assign the userdata to the template
            $this->tpl->assign( 'user', $this->user );

            // Standard stuff for the sidebar
            $categories = $this->weblog->getCategories();
            $pages      = $this->weblog->getPublicPages();
            $links      = $this->weblog->getLinks();

            // Assign them to the template
            $this->tpl->assign( 'categories', $categories );
            $this->tpl->assign( 'pages',      $pages );
            $this->tpl->assign( 'links',      $links );

            // Assign the weblog details
            $this->tpl->assign( 'weblog_title',                YDConfig::get( 'weblog_title', 'Untitled Weblog' ) );
            $this->tpl->assign( 'weblog_description',          YDConfig::get( 'weblog_description', 'Untitled Weblog Description' ) );
            $this->tpl->assign( 'weblog_dir',                  YDUrl::makeLinkAbsolute( '.' ) );
            $this->tpl->assign( 'weblog_link',                 YDUrl::makeLinkAbsolute( 'index.php' ) );
            $this->tpl->assign( 'weblog_link_rss',             YDUrl::makeLinkAbsolute( 'xml.php?do=rss' ) );
            $this->tpl->assign( 'weblog_link_atom',            YDUrl::makeLinkAbsolute( 'xml.php?do=atom' ) );
            $this->tpl->assign( 'weblog_link_comments_rss',    YDUrl::makeLinkAbsolute( 'xml.php?do=rsscomments' ) );
            $this->tpl->assign( 'weblog_link_comments_atom',   YDUrl::makeLinkAbsolute( 'xml.php?do=atomcomments' ) );
            $this->tpl->assign( 'weblog_link_gallery_rss',     YDUrl::makeLinkAbsolute( 'xml.php?do=rssgallery' ) );
            $this->tpl->assign( 'weblog_link_gallery_atom',    YDUrl::makeLinkAbsolute( 'xml.php?do=atomgallery' ) );
            $this->tpl->assign( 'weblog_link_archive',         YDUrl::makeLinkAbsolute( 'archive.php' ) );
            $this->tpl->assign( 'weblog_link_archive_gallery', YDUrl::makeLinkAbsolute( 'archive_gallery.php' ) );

            // Get the link to the different directories
            $uploads_dir = YDUrl::makeLinkAbsolute( $this->dir_uploads );
            $skin_dir    = YDUrl::makeLinkAbsolute( $this->dir_skins . $this->skin );
            $image_dir   = YDUrl::makeLinkAbsolute( $skin_dir . '/images' );

            // Add the different directories to the template
            $this->tpl->assign( 'uploads_dir', $uploads_dir );
            $this->tpl->assign( 'skin_dir',    $skin_dir );
            $this->tpl->assign( 'image_dir',   $image_dir );

        }

        // Fetch a template
        function fetch( $customTpl=null ) {

            // Initialize the template
            $this->initTemplate();

            // Log a request to the stats
            $this->_logRequest();

            // Parse the template
            $data = $this->tpl->fetch( $customTpl );

            // Check if caching is enabled
            if ( YDConfig::get( 'use_cache', false ) ) {

                // Save the cache if needed
                if ( $this->caching === true ) {

                    // Save the cache
                    $f = fopen( YD_WEBLOG_CACHE_FNAME, 'wb' );
                    fwrite( $f, $data );
                    fclose( $f );

                }

            }

            // Return the parsed templaet
            return $data;

        }

        // Display the named template
        function display( $customTpl=null ) {

            // Display a fetched template
            echo( $this->fetch( $customTpl ) );

        }

        // Get the name of the template
        function getTemplateName( $customTpl=null ) {

            // Get the correct template name
            $name = is_null( $customTpl ) ? strtolower( get_class( $this ) ) : $customTpl;

            // Return the file path
            return  $this->tpl->template_dir . $name . YD_TPL_EXT;

        }

        // Function to disbable request logging
        function _logRequest() {

            // Get the logging data
            $values = array();
            $values['uri'] = str_replace( dirname( YD_SELF_SCRIPT ) . '/', '', $this->getNormalizedUri() );
            if ( substr( $values['uri'], 0, 1 ) == '/' ) {
                $values['uri'] = substr( $values['uri'], 1 );
            }
            $values['date'] = strftime( '%Y-%m-%d' );
            $values['browser'] = $this->browser->browser;
            $values['platform'] = $this->browser->platform;

            // Fix the short URLs so that they all look the same
            if ( YDConfig::get( 'friendly_urls', true ) ) {
                if ( YDStringUtil::startswith( $values['uri'], 'image/', false ) ) {
                    $values['uri'] = 'item_gallery.php?id=' . substr( $values['uri'], 6 );
                } elseif ( YDStringUtil::startswith( $values['uri'], 'archive' ) ) {
                } else {
                    $values['uri'] = substr( $values['uri'], 0, strpos( $values['uri'], '_' ) )
                                   . substr( $values['uri'], strpos( $values['uri'], '.php?id=' ) );
                }
            }

            // Add them to the database
            $this->weblog->logRequestToStats( $values );

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
            return true;
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

    }

    // Class for an admin request
    class YDWeblogAdminRequest extends YDWeblogRequest {

        // Class constructor
        function YDWeblogAdminRequest() {

            // Initialize the parent
            $this->YDWeblogRequest();

            // Check if we allow caching
            $this->caching = false;
            
            // Delete the cache
            if ( sizeof( $_POST ) > 0 ) {
                @ $this->clearCache();
            }

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

        // Function to show a thumbnail
        function actionThumbnailSmall() {

            // Get the image name
            if ( ! isset( $_GET['id'] ) ) {
                die( 'No image selected.' );
            }

            // Create a new image object
            $img = new YDFSImage( YDConfig::get( 'dir_uploads', '../uploads' ) . '/' . $_GET['id'] );

            // Output the thumbnail
            $img->outputThumbnail( 48, 48 );

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

                // Get the path for the cookies
                if ( YDStringUtil::endsWith( dirname( YD_SELF_SCRIPT ), '/manage' ) ) {
                    $cookiePath = dirname( dirname( YD_SELF_SCRIPT ) ) . '/';
                } else {
                    $cookiePath = dirname( YD_SELF_SCRIPT ) . '/';
                }

                // Set the cookies
                setcookie( 'YD_USER_NAME', $values['loginName'], time() + 31536000, $cookiePath );
                setcookie( 'YD_USER_PASS', md5( $values['loginPass'] ), time() + 31536000, $cookiePath );

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

        // Failed authentication, forwards to the login action
        function authenticationFailed() {
            $this->forward( 'login' );
        }

        // Function to disbable request logging
        function _logRequest() {
        }

    }

?>
