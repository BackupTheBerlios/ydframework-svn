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


            // Default to default skin
            if ( ! is_dir( dirname( __FILE__ ) . '/../' . $this->dir_skins . $this->skin . '/' ) ) {
                YDConfig::set( 'weblog_skin', 'default' );
                $this->skin = YDConfig::get( 'weblog_skin', 'default' );
            }

            // Initialize the template
            $this->tpl = new YDTemplate();
            $this->tpl->template_dir = dirname( __FILE__ ) . '/../' . $this->dir_skins . $this->skin . '/';

            // Register the modifiers
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
                    if ( substr( $file, -strlen( '.tpl.php' ) ) == '.tpl.php' ) {
                        @unlink( YD_DIR_TEMP . '/' . $file );
                    }
                }
                closedir( $handle );
            }
        }

        // Function to show a thumbnail
        function actionThumbnail() {
            if ( ! isset( $_GET['id'] ) ) {
                die( 'No image selected.' );
            }
            $img = new YDFSImage( dirname( __FILE__ ) . '/../' . YDConfig::get( 'dir_uploads', 'uploads' ) . '/' . $_GET['id'] );
            $img->outputThumbnail( 100, 100 );
        }

        // Function to show a thumbnail
        function actionThumbnailSmall() {
            if ( ! isset( $_GET['id'] ) ) {
                die( 'No image selected.' );
            }
            $img = new YDFSImage( YDConfig::get( 'dir_uploads', 'uploads' ) . '/' . $_GET['id'] );
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
            $title = $this->tpl->get_template_vars( 'title' );
            $wltitle = YDConfig::get( 'weblog_title', 'Untitled Weblog' );
            if ( $title != null ) {
                $title = $wltitle . ' &raquo; ' . $title;
            } else {
                $title = $wltitle;
            }
            $this->tpl->assign( 'title',                       $title );
            $this->tpl->assign( 'weblog_title',                $wltitle );
            $this->tpl->assign( 'weblog_description',          YDConfig::get( 'weblog_description', 'Untitled Weblog Description' ) );
            $this->tpl->assign( 'weblog_language',             YDConfig::get( 'weblog_language', 'en' ) );
            $this->tpl->assign( 'weblog_dir',                  YDUrl::makeLinkAbsolute( '.' ) );
            $this->tpl->assign( 'weblog_current_url',          YDUrl::makeLinkAbsolute( YD_SELF_URI ) );
            $this->tpl->assign( 'weblog_link',                 YDUrl::makeLinkAbsolute( 'index.php' ) );
            $this->tpl->assign( 'weblog_link_rss',             YDUrl::makeLinkAbsolute( 'xml.php?do=rss' ) );
            $this->tpl->assign( 'weblog_link_atom',            YDUrl::makeLinkAbsolute( 'xml.php?do=atom' ) );
            $this->tpl->assign( 'weblog_link_comments_rss',    YDUrl::makeLinkAbsolute( 'xml.php?do=rsscomments' ) );
            $this->tpl->assign( 'weblog_link_comments_atom',   YDUrl::makeLinkAbsolute( 'xml.php?do=atomcomments' ) );
            $this->tpl->assign( 'weblog_link_gallery_rss',     YDUrl::makeLinkAbsolute( 'xml.php?do=rssgallery' ) );
            $this->tpl->assign( 'weblog_link_gallery_atom',    YDUrl::makeLinkAbsolute( 'xml.php?do=atomgallery' ) );
            $this->tpl->assign( 'weblog_link_archive',         YDUrl::makeLinkAbsolute( 'archive.php' ) );
            $this->tpl->assign( 'weblog_link_archive_gallery', YDUrl::makeLinkAbsolute( 'archive_gallery.php' ) );

            // Add the Google Analytics tag
            if ( YDConfig::get( 'google_analytics', '' ) != '' ) {
                $out = '<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>' . YD_CRLF;
                $out .= '<script type="text/javascript">' . YD_CRLF;
                $out .= '_uacct = "' . YDConfig::get( 'google_analytics', '' ) . '"; urchinTracker();' . YD_CRLF;
                $out .= '</script>' . YD_CRLF;
                $this->tpl->assign( 'weblog_google_analytics', $out );
            } else {
                $this->tpl->assign( 'weblog_google_analytics', '' );
            }

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
            $this->initTemplate();
            $this->_logRequest();
            $data = $this->tpl->fetch( $customTpl );
            if ( YDConfig::get( 'use_cache', false ) ) {
                if ( $this->caching === true ) {
                    $f = fopen( YD_WEBLOG_CACHE_FNAME, 'wb' );
                    fwrite( $f, $data );
                    fclose( $f );
                }
            }
            return $data;
        }

        // Display the named template
        function display( $customTpl=null ) {
            echo( $this->fetch( $customTpl ) );
        }

        // Get the name of the template
        function getTemplateName( $customTpl=null ) {
            $name = is_null( $customTpl ) ? strtolower( get_class( $this ) ) : $customTpl;
            return  $this->tpl->template_dir . $name . YD_TPL_EXT;
        }

        // Function to disbable request logging
        function _logRequest() {

            // Check if we need to log statistics or not
            if ( YDConfig::get( 'keep_stats', true ) ) {

                // Get the logging data
                $values = array();
                $values['uri'] = str_replace( dirname( YD_SELF_SCRIPT ) . '/', '', $this->getNormalizedUri() );
                if ( substr( $values['uri'], 0, 1 ) == '/' ) {
                    $values['uri'] = substr( $values['uri'], 1 );
                }
                if ( substr( $values['uri'], 0, 4 ) == '.php' ) {
                    $values['uri'] = basename( YD_SELF_SCRIPT ) . substr( $values['uri'], 4 );
                }
                $values['date'] = strftime( '%Y-%m-%d' );
                $values['browser'] = $this->browser->browser;
                $values['platform'] = $this->browser->platform;

                // Fix the short URLs so that they all look the same
                if ( YDConfig::get( 'friendly_urls', true ) && strpos( $values['uri'], '_' ) ) {
                    if ( YDStringUtil::startswith( $values['uri'], 'image/', false ) ) {
                        $values['uri'] = 'item_gallery.php?id=' . substr( $values['uri'], 6 );
                    } elseif ( YDStringUtil::startswith( $values['uri'], 'archive' ) ) {
                    } else {
                        $values['uri'] = substr( $values['uri'], 0, strpos( $values['uri'], '_' ) )
                                       . substr( $values['uri'], strpos( $values['uri'], '.php?id=' ) );
                    }
                }

                // Add index.php
                if ( empty( $values['uri'] ) && basename( YD_SELF_SCRIPT ) == 'index.php' ) {
                    $values['uri'] = 'index.php';
                }

                // Add them to the database
                $this->weblog->logRequestToStats( $values );

            }

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

            // Get a link to the database metadata
            $dbmeta = new YDDatabaseMetaData( $this->weblog->db );

            // Optimize the tables
            foreach ( $dbmeta->getTables() as $table ) {
                $this->weblog->db->executeSql( 'optimize table ' . $table );
            }

        }

        // Init the template
        function initTemplate() {

            // Assign the userdata to the template
            $this->tpl->assign( 'user', $this->user );

            // Assign the weblog details
            $this->tpl->assign( 'weblog_title',       YDConfig::get( 'weblog_title', 'Untitled Weblog' ) );
            $this->tpl->assign( 'weblog_description', YDConfig::get( 'weblog_description', 'Untitled Weblog Description' ) );
            $this->tpl->assign( 'weblog_language',    YDConfig::get( 'weblog_language', 'en' ) );
            $this->tpl->assign( 'google_analytics',   YDConfig::get( 'google_analytics', '' ) != '' );
            $this->tpl->assign( 'keep_stats',         YDConfig::get( 'keep_stats', true ) );

            // Get the link to the different directories
            $uploads_dir = YDUrl::makeLinkAbsolute( '../' . $this->dir_uploads );
            $skin_dir    = YDUrl::makeLinkAbsolute( '../' . $this->dir_skins . $this->skin );
            $image_dir   = YDUrl::makeLinkAbsolute( $skin_dir . '/images' );

            // Add the different directories to the template
            $this->tpl->assign( 'uploads_dir', $uploads_dir );
            $this->tpl->assign( 'skin_dir',    $skin_dir );
            $this->tpl->assign( 'image_dir',   $image_dir );

        }

        // Redirect if an item is missing
        function redirectIfMissing( $test ) {
            if ( ! $test ) {
                $this->redirectToAction();
            }
        }

        // Function to show a thumbnail
        function actionThumbnailSmall() {
            if ( ! isset( $_GET['id'] ) ) {
                die( 'No image selected.' );
            }
            $img = new YDFSImage( YDConfig::get( 'dir_uploads', '../uploads' ) . '/' . $_GET['id'] );
            $img->outputThumbnail( 48, 48 );
        }

        // Function to get the cookie path
        function _getCookiePath() {
            if ( YDStringUtil::endsWith( dirname( YD_SELF_SCRIPT ), '/manage' ) ) {
                $cookiePath = dirname( dirname( YD_SELF_SCRIPT ) );
            } else {
                $cookiePath =  dirname( YD_SELF_SCRIPT );
            }
            $cookiePath = rtrim( str_replace( '\\', '/', $cookiePath ), '/' );
            return $cookiePath . '/';
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
                $cookiePath = $this->_getCookiePath();

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
            $cookiePath = $this->_getCookiePath();
            setcookie( 'YD_USER_PASS', null, time() - 31536000, $cookiePath );
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
