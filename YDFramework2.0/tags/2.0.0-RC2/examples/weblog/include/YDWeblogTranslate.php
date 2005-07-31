<?php

    // Set the right locale
    $language = YDConfig::get( 'weblog_language', 'en' );
    $language = empty( $language ) ? 'en' : $language;
    YDLocale::set( $language );

    // The t function used for translations
    function t( $t ) {

        // Return empty string when param is missing
        if ( empty( $t ) ) {
            return '';
        }

        // Load the language file
        @include_once( dirname( __FILE__ ) . '/languages/language_' . strtolower( YDLocale::get() ) . '.php' );

        // Initialize the language array
        if ( ! isset( $GLOBALS['t'] ) ) {
            $GLOBALS['t'] = array();
        }

        // Return the right translation
        $t = strtolower( $t );
        return ( array_key_exists( $t, $GLOBALS['t'] ) ? $GLOBALS['t'][$t] : "%$t%" );

    }

?>