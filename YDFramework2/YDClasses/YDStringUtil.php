<?php

    /*

       Yellow Duck Framework version 2.0
       (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

    */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'ERROR: Yellow Duck Framework is not loaded.' );
    }

    /**
     *  This class houses all the string related utility functions. All the methods
     *  are implemented as static methods and do not require you to create a
     *  class instance in order to use them.
     */
    class YDStringUtil extends YDBase {

        /**
         *  Function to format a file size to a meaningful value.
         *
         *  @remarks
         *      This function recognizes EB, PB, TB, GB, MB and KB.
         *  
         *  @param $bytes    The file size to format.
         *  @param $decimals (optional) The number of decimals that should be 
         *                   returned.
         *
         *  @returns String containing the formatted file size.
         */
        function formatFilesize( $bytes, $decimals=1 ) {

            // The different units
            $units = array(
              '1152921504606846976' => 'EB',
              '1125899906842624'    => 'PB',
              '1099511627776'       => 'TB',
              '1073741824'          => 'GB',
              '1048576'             => 'MB',
              '1024'                => 'KB'
            );

            // Smaller than 1 KB, so return bytes
            if ( $bytes <= 1024 ) {
                return $bytes . " Bytes";
            }
            
            // Loop over the remaining possibilities
            foreach( $units as $base => $title ) {
                if( floor( $bytes / $base ) != 0 ) {
                    return number_format(
                        $bytes / $base, $decimals, '.', ''
                    ) . ' ' . $title;
                }
            }

        }

        /**
         *  This function will encode an email address using HTML entities.
         *
         *  @param $email Email address to encode.
         *
         *  @returns Encoded email address.
         */
        function encodeEmail( $email ) {
            if ( $email != '' ) {
                $ent = '';
                $userName = '';
                $domainName = '';
                for ( $i = 0; $i < strlen( $email ); $i++ ) {
                    $c = substr( $email, $i, 1 );
                    if ( $c == '@' ) {
                        $userName = $ent;
                        $ent = "";
                    } else {
                        $ent .= '&#' . ord($c) . ';';
                    }
                }
                $domainName = $ent;
                return $userName . '&#64;' . $domainName;
            } else {
                return '';
            }
        }

        /**
         *  This function will decode an email address from HTML entities.
         *
         *  @param $email Email address to decode.
         *
         *  @returns Decoded email address.
         */
        function decodeEmail( $email ) {
            if (
                ( substr( $email, 0, 2 ) == '&#' )
                &&
                ( substr( $email, -1 ) == ';' ) )
            {
                $decodedEmail = '';
                $charList = explode( ';&#', substr( $email, 2, -1 ) );
                foreach( $charList as $char ) {
                    $decodedEmail = $decodedEmail . chr( $char );
                }
                return $decodedEmail;
            } else {
                return $email;
            }
        }

        /**
         *  Function to format a date. This function follows the syntax of the
         *  strftime function from PHP. More info on this function can be found
         *  on the following URL:
         *  http://us2.php.net/manual/en/function.strftime.php
         *
         *  If the variable $text is a string, it will be first converted to a
         *  real time object using the strtotime function.
         *
         *  If an empty string is passed to this function, it will be returned
         *  without any modification.
         *
         *  @param $text       Date that needs be formatted.
         *  @param $dateFormat Date format to use.
         *
         *  @return Formatted date.
         */
        function formatDate( $text, $dateFormat ) {
            if ( is_integer( $text ) ) {
                return strftime( $dateFormat, $text );
            } else {
                if ( $text != '' ) {
                    return strftime( $dateFormat, strtotime( $text ) );
                } else {
                    return $text;
                }
            }
        }

        /**
         *  This function will encode all characters which have an ordinal
         *  bigger than 128 to numeric HTML entities, which can be safely
         *  included in e.g. XML output.
         *
         *  @param $string The original string to encode.
         *
         *  @returns String with all the characters with an ordinal bigger than
         *           128 converted to numeric HTML entities.
         */
        function encodeString( $string ) {

            $encoded = '';

            for ( $i=0; $i < strlen( $string ); $i++ )  {
                if ( ord( substr( $string, $i, 1 ) ) > 128 ) {
                    $encoded .= '&#' . ord( substr( $string, $i, 1 ) ) . ';';
                } elseif ( ord( substr( $string, $i, 1 ) ) == 0 ) {
                    $encoded .= ' ';
                } else {
                    $encoded .= substr( $string, $i, 1 );    
                }
            }

            return $encoded;

        }


    }

?>
