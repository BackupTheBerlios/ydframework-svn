<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die(  'Yellow Duck Framework is not loaded.' );
    }

    /**
     *  This function defines a fatal error.
     *
     *  @param $error Error message.
     */
    function YDFatalError( $error ) {
        trigger_error( $error, E_USER_ERROR );
    }

    /**
     *  This function defines a warning.
     *
     *  @param $error Error message.
     */
    function YDWarning( $error ) {
        trigger_error( $error, E_USER_WARNING );
    }

    /**
     *  This function defines a notice.
     *
     *  @param $error Error message.
     */
    function YDNotice( $error ) {
        trigger_error( $error, E_USER_NOTICE );
    }

    /**
     *  This is the global error handler for the Yellow Duck Framework. This
     *  function will take care of all error handling.
     *
     *  @param $errno   Type of the error (fatal/warning/notice).
     *  @param $errstr  Error string.
     *  @param $errfile Name of the file in which the error occured.
     *  @param $errline Line number in the file where the error occured.
     */
    function YDErrorHandler( $errno, $errstr, $errfile, $errline ) {

        // Text values for the error types
        $errortypes = array(
            E_ERROR           => 'ERROR',
            E_WARNING         => 'WARNING',
            E_PARSE           => 'PARSING ERROR',
            E_NOTICE          => 'NOTICE',
            E_CORE_ERROR      => 'CORE ERROR',
            E_CORE_WARNING    => 'CORE WARNING',
            E_COMPILE_ERROR   => 'COMPILE ERROR',
            E_COMPILE_WARNING => 'COMPILE WARNING',
            E_USER_ERROR      => 'USER ERROR',
            E_USER_WARNING    => 'USER WARNING',
            E_USER_NOTICE     => 'USER NOTICE',
        );

        // Honour the defined level of error reporting set either in php.ini or 
        // with error_reporting()
        if ( ! ( $errno & error_reporting() ) ) {
            return;
        }

        // Output the error text
        echo( '<p style="background-color: lightyellow;">' );
        echo( '<font color="red" size="-1">' );
        echo( '<b>' . $errortypes[ $errno ] . '</b>' );
        echo( '<br>' );
        echo( '<i>' . $errfile . ' (line ' . $errline . ')</i>' );
        echo( '<br>' );
        echo( $errstr );
        echo( '</font></p>' );

        // Check the type of the error
        switch ( $errno ) {

            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                die();
                break;

        }

    }

?>
