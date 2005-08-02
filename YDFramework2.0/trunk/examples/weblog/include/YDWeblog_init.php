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

    // Include the standard modules
    YDInclude( 'YDUtil.php' );
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDBBCode.php' );
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDDatabase.php' );
    YDInclude( 'YDFormElements/YDFormElement_BBTextArea.php' );

    // Include other libraries
    YDInclude( dirname( __FILE__ ) . '/config.php' );
    YDInclude( dirname( __FILE__ ) . '/YDWeblogAPI.php' );
    YDInclude( dirname( __FILE__ ) . '/YDWeblogTemplate.php' );
    YDInclude( dirname( __FILE__ ) . '/YDWeblogTranslate.php' );
    YDInclude( dirname( __FILE__ ) . '/YDWeblogForm.php' );
    YDInclude( dirname( __FILE__ ) . '/YDWeblogRequest.php' );
    YDInclude( dirname( __FILE__ ) . '/YDWeblogAdminRequest.php' );

?>
