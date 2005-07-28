<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Includes
    YDInclude( 'phpThumb/phpthumb.class.php' );
    YDInclude( 'phpmailer/class.phpmailer.php' );

    // Class definition
    class serverinfo extends YDWeblogAdminRequest {

        // Class constructor
        function serverinfo() {

            // Initialize the parent
            $this->YDWeblogAdminRequest();

        }

        // Default action
        function actionDefault() {

            // Get the general PHP info
            ob_start();
            phpinfo( INFO_GENERAL );
            $phpInfo .= ob_get_contents();
            ob_end_clean();

            // Get the right part
            $phpInfo = substr( $phpInfo, strpos( $phpInfo, '<tr>' ) );
            $phpInfo = substr( $phpInfo, 0, strpos( $phpInfo, '</table>' ) );

            // Strip unneeded things
            $phpInfo = str_replace( '<tr><td class="e">', '', $phpInfo );
            $phpInfo = str_replace( ' </td></tr>', '', $phpInfo );
            $phpInfo = trim( $phpInfo );

            // Get the settings
            $settings = array();
            foreach ( explode( "\n", $phpInfo ) as $line ) {
                $line = explode( ' </td><td class="v">', $line );
                if ( isset( $line[1] ) ) {
                    $this->tpl->assign( strtolower( str_replace( ' ', '_', $line[0] ) ), $line[1] );
                } else {
                    $this->tpl->assign( strtolower( str_replace( ' ', '_', $line[0] ) ), '' );
                }
            }

            // Get the version of phpThumb
            $phpThumb = new phpthumb();

            // Get the version of phpMailer
            $PHPMailer = new PHPMailer();

            // Get the other variables
            $this->tpl->assign( 'php_version',       phpversion() );
            $this->tpl->assign( 'mysql_version',     mysql_get_server_info() );
            $this->tpl->assign( 'php_modules',       implode( get_loaded_extensions(), ', ' ) );
            $this->tpl->assign( 'includePath',       $GLOBALS['YD_INCLUDE_PATH'] );
            $this->tpl->assign( 'phpthumb_version',  $phpThumb->phpthumb_version );
            $this->tpl->assign( 'phpmailer_version', $PHPMailer->Version );
            $this->tpl->assign( 'PHP_OS', PHP_OS );

            // Add the settings to the template
            $this->tpl->assign( 'settings', $settings );

            // Display the template
            $this->display();

        }

        // The phpinfo action
        function actionPhpInfo() {
            phpinfo();
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
