<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die(  'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    require_once( 'YDBase.php' );

    /**
     *  This class houses all the debug related utility functions. All the
     *  methods are implemented as static methods and do not require you to
     *  create a class instance in order to use them.
     */
    class YDDebugUtil extends YDBase {

        /**
         *  Function to output a debug message. These message are only shown if
         *  the constant YD_DEBUG is set to 1. You can turn on debugging by
         *  specifying the YD_DEBUG parameter in the url and assigning it the
         *  value 1.
         *
         *  Example url with debugging turned on:
         *  http://localhost/index.php?YD_DEBUG=1
         *
         *  This function accepts a variable amount of arguments which are all
         *  concatenated using a space in between. All debug messages will be
         *  shown as HTML comments with the prefix "[ YD_DEBUG ]".
         */
        function debug() {

            // Get the function arguments
            $args = func_get_args();

            // Check if debugging is on
            if ( YD_DEBUG == 1 ) {

                // Output the debugging info
                echo( "\n" . '<!--' . "\n" . '[ YD_DEBUG ]' . "\n\n" );
                echo( implode( ' ', $args ) . "\n" );
                echo( '-->' . "\n" );

            }

        }

        /**
         *  Function to dump the contents of pretty much anything. This is the
         *  sames as the var_dump function in PHP, but has a much nicer and more
         *  readable output.
         *
         *  @param $obj Object you want to dump.
         */
        function dump( $obj ) {

            // Display the information
            echo( YDDebugUtil::r_dump( $obj, true ) );

        }

        /**
         *  Function to return the contents of pretty much anything. This is the
         *  same as the var_export function in PHP.
         *
         *  @param $obj  Object you want to dump.
         *  @param $html (optional) If you want to have everything returned as
         *               HTML or text. The default is false, returning text.
         *
         *  @returns Text representation of the object.
         */
        function r_dump( $obj, $html=false ) {

            // Return the information
            $data = var_export( $obj, true );

            // Convert to HTML if needed
            if ( $html == true ) {
                $data = stripslashes( htmlentities( $data ) );
                $data = '<pre>' . $data . '</pre>';
            }

            // Return the data
            return $data;

        }

    }

?>
