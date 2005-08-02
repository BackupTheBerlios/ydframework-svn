<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Class definition
    class versioninfo extends YDWeblogAdminRequest {

        // Class constructor
        function versioninfo() {

            // Initialize the parent
            $this->YDWeblogAdminRequest();

        }

        // Default action
        function actionDefault() {

            // Get the XML changelog
            $url = new YDUrl( YD_FW_HOMEPAGE . 'ydf2_changelog_summary.xml' );
            $data = $url->getContents();

            // Parse the XML
            $p = xml_parser_create();
            xml_parse_into_struct( $p, $data, $vals, $index );
            xml_parser_free( $p );

            // Convert to a table array
            $changelog = array();

            // Loop over the items
            foreach ( $vals as $val ) {

                // New logentry
                if ( $val['tag'] == 'LOGENTRY' && $val['type'] == 'open' ) {
                    $data = array();
                    $data['revision'] = $val['attributes']['REVISION'];
                }

                // Author entry
                if ( $val['tag'] == 'AUTHOR' ) {
                    $data['author'] = $val['value'];
                }

                // Author entry
                if ( $val['tag'] == 'DATE' ) {
                    $data['date'] = substr( $val['value'], 0, 10 );
                }

                // Author entry
                if ( $val['tag'] == 'MSG' ) {
                    $data['msg'] = $val['value'];
                }

                // End logentry
                if ( $val['tag'] == 'LOGENTRY' && $val['type'] == 'close' ) {
                    array_push( $changelog, $data );
                }

            }

            // Add it to the template
            $this->tpl->assign( 'changelog', $changelog );

            // Display the template
            $this->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
