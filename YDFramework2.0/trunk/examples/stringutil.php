<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );

    // Class definition
    class stringutil extends YDRequest {

        // Class constructor
        function stringutil() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Test the formatting of filesizes
            $filesizes = array(
                '1152921504606846977', '1125899906842625', '1099511627777', '75715455455', '1048577', '6543', '42'
            );

            // Format the filesizes
            foreach ( $filesizes as $filesize ) {
                YDDebugUtil::dump( YDStringUtil::formatFileSize( $filesize ), 'Formatting filesize: ' . $filesize );
            }

            // Test the formatDate function
            YDDebugUtil::dump( YDStringUtil::formatDate( time(), 'date' ), 'Formatting date - date' );
            YDDebugUtil::dump( YDStringUtil::formatDate( time(), 'time' ), 'Formatting date - time' );
            YDDebugUtil::dump( YDStringUtil::formatDate( time(), 'datetime' ), 'Formatting date - datetime' );
            YDDebugUtil::dump( YDStringUtil::formatDate( time(), '%x' ), 'Formatting date - %x' );

            // Test the encode string function
            $string = 'Pieter Claerhout @ creo.com "générales obsolète"';
            YDDebugUtil::dump( YDStringUtil::encodeString( $string ), 'Encoding: ' . $string );
            
            // Test the truncate function
            YDDebugUtil::dump( YDStringUtil::truncate( $string ), 'Truncate (default): ' . $string );
            YDDebugUtil::dump( YDStringUtil::truncate( $string, 20 ), 'Truncate (20): ' . $string );
            YDDebugUtil::dump( YDStringUtil::truncate( $string, 20, ' [more]' ), 'Truncate (20/more): ' . $string );
            YDDebugUtil::dump( YDStringUtil::truncate( $string, 20, ' [more]', true ), 'Truncate (20/more/true): ' . $string );

            // Test the normalizing of newlines
            $string = "line1\nline2\rline3\r\nline4";
            YDDebugUtil::dump( explode( "\n", $string ), 'Original string' );
            YDDebugUtil::dump( explode( YD_CRLF, YDStringUtil::normalizeNewlines( $string ) ), 'normalizeNewlines' );

            // Test the normalizing of newlines
            $string = "  line1  \n  line2  \r  line3  \r\n  line4  ";
            YDDebugUtil::dump( YDStringUtil::removeWhiteSpace( $string ), 'removeWhiteSpace' );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>