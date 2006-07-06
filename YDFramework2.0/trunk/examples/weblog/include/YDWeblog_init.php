<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../../../YDFramework2/YDF2_init.php' );

    // Redirect to the installer if the config file doesn't exist
    if ( ! is_file( dirname( __FILE__ ) . '/config.php' ) ) {
        $root_path =    str_replace( '\\', '/', dirname( dirname( __FILE__ ) ) ) . '/';
        $current_path = str_replace( '\\', '/', dirname( YD_SELF_FILE ) ) . '/';
        $install_path = str_replace( $root_path, '', $current_path );
        $install_path = explode( '/', $install_path );
        $install_path_rel = '';
        for ( $i=1; $i < sizeof( $install_path ); $i++ ) {
            if ( is_file( $install_path_rel . 'install.php' ) ) {
                break;
            }
            $install_path_rel .= '../';
        }
        $install_path_rel .= 'install.php';
        header( 'Location: ' . $install_path_rel );
    }

    // Define some constants
    @define( 'YD_WEBLOG_CACHE_PREFIX', 'YDF_L_' );
    @define( 'YD_WEBLOG_CACHE_SUFFIX', 'cache' );

    // First stab of includes
    YDInclude( 'YDRequest.php' );
    YDInclude( dirname( __FILE__ ) . '/config.php' );

    // Set the YD_DEBUG flag
    if ( YDConfig::get( 'include_debug_info', 1 ) == 1 ) {
        YDConfig::set( 'YD_DEBUG', 1 );
    } else {
        YDConfig::set( 'YD_DEBUG', 0 );
    }

    // Update the database prefix
    YDConfig::set( 'YD_DB_TABLEPREFIX', YDConfig::get( 'db_prefix', '' ) );

    // Get the cache filename
    $cache_file = YD_DIR_TEMP . '/' . YD_WEBLOG_CACHE_PREFIX . md5( YDRequest::getNormalizedCurrentUrl() ) . '.' . YD_WEBLOG_CACHE_SUFFIX;
    @define( 'YD_WEBLOG_CACHE_FNAME', $cache_file );

    // Include the standard modules
    YDInclude( 'YDDatabase.php' );
    YDInclude( 'YDDatabaseMetaData.php' );
    YDInclude( 'YDTemplate.php' );

    // Include other libraries
    YDInclude( dirname( __FILE__ ) . '/YDWeblogAPI.php' );
    YDInclude( dirname( __FILE__ ) . '/YDWeblogRequest.php' );

    // Check if the user wanted to use caching
    if ( YDConfig::get( 'use_cache', false ) ) {

        // Check if we allow caching
        if ( sizeof( $_POST ) == 0 ) {

            // Check if there is a cache item for this request
            if ( is_file( YD_WEBLOG_CACHE_FNAME ) ) {

                // Output the cached item
                $data = file_get_contents( YD_WEBLOG_CACHE_FNAME );

                // Include a cache filter if any
                @include( dirname( __FILE__ ) . '/cache_filter.php' );

                // Add the elapsed time
                $elapsed = $GLOBALS['timer']->getElapsed();

                // Log the request to the statistics
                $req = new YDWeblogRequest();
                $req->_logRequest();

                // Add the statistics
                die( $data . YD_CRLF . '<!-- #cached: ' . $elapsed . ' ms -->' );

            }

        }

    }

    // Include the standard modules
    YDInclude( 'YDUrl.php' );
    YDInclude( 'YDUtil.php' );
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDFileSystem.php' );
    YDInclude( 'YDFormElements/YDFormElement_TextArea.php' );

    // Check if the weblog application is locked or not
    $db = YDDatabase::getInstance(
        'mysql',
        YDConfig::get( 'db_name', 'YDWeblog' ), YDConfig::get( 'db_user', 'root' ),
        YDConfig::get( 'db_pass', '' ), YDConfig::get( 'db_host', 'localhost' )
    );
    $tables = $db->getRecords( 'show tables' );
    $table_field_name = 'tables_in_' . strtolower( YDConfig::get( 'db_name', 'YDWeblog' ) );
    foreach ( $tables as $table ) {
        if ( strtolower( $table[$table_field_name] ) == YDConfig::get( 'db_prefix', '' ) . 'locked' ) {
            die( 'Weblog application is being updated. Please come again later.' );
        }
    }

    // Set the right locale
    $language = YDConfig::get( 'weblog_language', 'en' );
    $language = empty( $language ) ? 'en' : $language;
    YDLocale::set( $language );

    // Load the language file
    @include_once( dirname( __FILE__ ) . '/languages/language_' . strtolower( YDLocale::get() ) . '.php' );

    // Template Date modifier
    function YDTplModDate( $text, $format=null ) {
        if ( is_null( $format ) ) {
            $format = '%A, %d %b %Y';
        }
        return YDStringUtil::encodeString(
            ucwords( strtolower( YDTemplate_modifier_date_format( $text, $format ) ) )
        );
    }

    // Create a link using the ID and given base
    function YDTplModLinkWithID( $base, $item, $suffix=null ) {
        $ori_base = $base;
        $base .= ( strpos( $base, '?' ) === false ) ? '?id=' : '&id=';
        if ( is_numeric( $item ) ) {
            $base .= $item;
        } else {
            $base .= ( is_array( $item ) && isset( $item['id'] ) ) ? $item['id'] : $item;
        }
        if ( YDConfig::get( 'friendly_urls', false ) ) {
            if ( $ori_base == 'item_gallery.php' ) {
                $base = str_replace( 'item_gallery.php?id=', 'image/', $base );
            } else {
                $base = str_replace( '.php?id=', '_', $base ) . '.php';
            }
        }
        if ( ! is_null( $suffix ) ) {
            $base = $base . $suffix;
        }
        return YDUrl::makeLinkAbsolute( $base, YDRequest::getCurrentUrl( true ) );
    }

    // Provide a link to a weblog item
    function YDTplModLinkItem( $id, $suffix='' ) {
        return YDTplModLinkWithID( 'item.php', $id, $suffix );
    }

    // Provide a link to a weblog image
    function YDTplModLinkItemImages( $id ) {
        return YDTplModLinkWithID( 'item.php', $id, '#images' );
    }

    // Provide a link to a weblog image
    function YDTplModLinkItemGallery( $id ) {
        return YDTplModLinkWithID( 'item_gallery.php', $id );
    }

    // Provide a link to a weblog comment
    function YDTplModLinkItemComment( $id ) {
        return YDTplModLinkWithID( 'item.php', $id, '#comment' );
    }

    // Provide a link to a weblog item comment form
    function YDTplModLinkItemRespond( $id ) {
        return YDTplModLinkWithID( 'item.php', $id, '#respond' );
    }

    // Provide a link to a category
    function YDTplModLinkCategory( $id ) {
        return YDTplModLinkWithID( 'category.php', $id );
    }

    // Provide a link to a page
    function YDTplModLinkPage( $id ) {
        return YDTplModLinkWithID( 'page.php', $id );
    }

    // Provide a link to a link
    function YDTplModLinkLink( $id ) {
        return YDTplModLinkWithID( 'link.php', $id );
    }

    // Provide a link to a thumbnail
    function YDTplModLinkThumb( $img ) {
        return YDUrl::makeLinkAbsolute(
            YDConfig::get( 'dir_uploads', 'uploads' ) . '/' . dirname( $img->relative_path ) . '/m_' . basename( $img->relative_path )
        );
    }

    // Provide a link to a small thumbnail
    function YDTplModLinkThumbSmall( $img ) {
        return YDUrl::makeLinkAbsolute(
            YDConfig::get( 'dir_uploads', 'uploads' ) . '/' . dirname( $img->relative_path ) . '/s_' . basename( $img->relative_path )
        );
    }

    // Get a text containing the number of comments
    function YDTplModTextNumComments( $item, $showIfNone=false ) {
        if ( $item['num_comments'] == '0' ) {
            return $showIfNone ? strtolower( t( 'no_comments_yet' ) ) : '';
        } elseif ( $item['num_comments'] == '1' ) {
            return $item['num_comments'] . ' ' . strtolower( t( 'comment' ) );
        } else {
            return $item['num_comments'] . ' ' . strtolower( t( 'comments' ) );
        }
    }

    // Get a text containing the number of images
    function YDTplModTextNumImages( $item, $showIfNone=false ) {
        if ( $item['num_images'] == '0' ) {
            return $showIfNone ? strtolower( t( 'no_images_yet' ) ) : '';
        } elseif ( $item['num_images'] == '1' ) {
            return $item['num_images'] . ' ' . strtolower( t( 'image' ) );
        } else {
            return $item['num_images'] . ' ' . strtolower( t( 'images' ) );
        }
    }

    // The form class to use for the weblog
    class YDWeblogForm extends YDForm {

        // Class constructor
        function YDWeblogForm( $name, $method='post', $action='', $target='_self', $attributes=array() ) {

            // Initialize the parent
            $this->YDForm( $name, $method, $action, $target, $attributes );

            // Set the required
            $this->setHtmlRequired( '', ' <span class="required">(' . t( 'required' ) . ')</span>' );

            // Register the form element
            $this->registerElement( 'wlbbtextarea', 'YDWeblogFormElement_BBTextArea' );
            $this->registerElement( 'wladmintextarea', 'YDFormElement_AdminTextArea' );

        }

    }

    // A BB Text Area form element
    class YDWeblogFormElement_BBTextArea extends YDFormElement_TextArea {

        // Class constructor
        function YDWeblogFormElement_BBTextArea( $form, $name, $label='', $attributes=array(), $options=array() ) {
            $this->YDFormElement_TextArea( $form, $name, $label, $attributes, $options );
        }

    }

    // Custom bbtext area
    class YDFormElement_AdminTextArea extends YDWeblogFormElement_BBTextArea {

        // Class constructor
        function YDFormElement_AdminTextArea( $form, $name, $label='', $attributes=array(), $options=array() ) {
            $this->YDWeblogFormElement_BBTextArea( $form, $name, $label, $attributes, $options );
        }

    }

?>
