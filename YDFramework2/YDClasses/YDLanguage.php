<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'ERROR: Yellow Duck Framework is not loaded.' );
    }

    // Includes
    require_once( 'YDBase.php' );
    require_once( 'YDError.php' );

    /**
     *  This class will inspect the $_SERVER['HTTP_ACCEPT_LANGUAGE'] variable
     *  and, based on a list of supported languages, return the most appropriate
     *  language.
     */
    class YDLanguage extends YDBase {

        /**
         *  This is the class constructor for the YDLanguage class.
         *
         *  @param $supported (optional) An array with the list of supported
         *                    languages. By default, only english is supported.
         */
        function YDLanguage( $supported=array( 'en' ) ) {

            // Initialize YDBase
            $this->YDBase();

            // Check if the list of languages is an array
            if ( ! is_array( $supported ) ) {
                new YDFatalError(
                    'YDLanguageNegotiator requires an array with the list of '
                    . 'supported languages.'
                );
            }

            // Check if at least one language is specified
            if ( sizeof( $supported ) < 1 ) {
                new YDFatalError(
                    'YDLanguageNegotiator requires you to specify at least one '
                    . 'supported language. None were specified.'
                );
            }

            // The list with supported languages
            $this->_supported = $supported;

        }

        /**
         *  This function returns an array with the languages that are supported
         *  by the browser. This is done by using the HTTP_ACCEPT_LANGUAGE
         *  server variable that gets send with the HTTP headers.
         *
         *  @return Array containing the list of supported languages
         */
        function getBrowserLanguages() {

            // We parse the language headers sent by the browser
            $browserLanguages = explode(
                ',', $_SERVER['HTTP_ACCEPT_LANGUAGE']
            );

            // Normalize the browser language headers
            for ( $i = 0; $i < sizeof( $browserLanguages ); $i++ ) {
                $browserLanguage = explode( ';', $browserLanguages[$i] );
                $browserLanguages[$i] = substr( $browserLanguage[0], 0, 2 );
            }

            // Remove the duplicates
            $browserLanguages = array_unique( $browserLanguages );

            // Return the browser languages
            return array_values( $browserLanguages );

        }

        /**
         *  Function that returns the best language based on the list of server
         *  supported languages and the languages supported by the browser. It
         *  will take in account the order of the languages the server
         *  specified, and it will also take in account the order of the
         *  languages in which the browser specified the supported languages. If
         *  no common language is found, the first server supported language is
         *  used.
         *
         *  @return String containing the language code.
         */
        function getLanguage() {

            // Start with the default language
            $language = $this->_supported[0];

            // Get the list of languages supported by the browser
            $browserLanguages = $this->getBrowserLanguages();

            // Now, we look if the browser specified one
            if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {

                // Loop over the browser languages
                foreach ( $browserLanguages as $browserLanguage ) {

                    // Check if we support the language
                    if ( in_array( $browserLanguage, $this->_supported ) ) {

                        // Supported, stop
                        $language = $browserLanguage;
                        break;

                    }

                }

            }

            // Return the language
            return $language;

        }

    }

?>
