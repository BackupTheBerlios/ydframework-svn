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
     *  This class houses all the debug related utility functions. All the 
     *  methods are implemented as static methods and do not require you to
     *  create a class instance in order to use them.
     */
    class YDDebugUtil extends PEAR {

        /**
         *  Function to output a debug message. These message are only shown if the
         *  constant YD_DEBUG is set to 1. You can turn on debugging by specifying
         *  the YD_DEBUG parameter in the url and assigning it the value 1.
         *
         *  Example url with debugging turned on:
         *  http://localhost/index.php?YD_DEBUG=1
         *
         *  This function accepts a variable amount of arguments which are all
         *  concatenated using a space in between. All debug messages will be shown
         *  in a gray box and are prepended with the text "[ debug ]".
         */
        function debug() {
            $args = func_get_args();
            if ( YD_DEBUG == 1 ) {
                echo( '<p style="background-color: #CCCCCC;"><font size="-1">' );
                echo( '<b>[ debug ]</b> '. implode( ' ', $args ) );
                echo( '</font></p>' );
            }
        }

        /**
         *  Function to dump the contents of pretty much anything. This is the 
         *  sames as the var_dump function in PHP, but has a much nicer and more 
         *  readable output.
         *
         *  The following happens depending on the object type:
         *  - If given a simple variable (string, integer, double, ressource), 
         *    the value itself is printed.
         *  - If given an array, it is explored recursively and values are 
         *    presented in a format that shows keys and elements.
         *  - If given an object, informations about the object and the class
         *    are printed.
         *
         *  @param $obj Object you want to dump.
         */
        function dump( $obj ) {

            // Display the information
            echo( YDDebugUtil::r_dump( $obj ) );

        }

        /**
         *  Function to return the contents of pretty much anything. This is the 
         *  same as the var_export function in PHP, but has a much nicer and 
         *  more readable output.
         *
         *  The following happens depending on the object type:
         *  - If given a simple variable (string, integer, double, ressource), 
         *    the value itself is returned.
         *  - If given an array, it is explored recursively and values are 
         *    presented in a format that shows keys and elements.
         *  - If given an object, informations about the object and the class
         *    are returned.
         *
         *  @param $obj Object you want to dump.
         *
         *  @returns HTML representation of the object.
         */
        function r_dump( $obj ) {

            // Include the Var_Dump package
            require_once( YD_DIR_3RDP_PEAR . '/Var_Dump.php' );

            // We want to have HTML4 text returned
            Var_Dump::displayInit( array( 'display_mode' => 'HTML4_Text' ) );

            // Return the information
            return Var_Dump::display( $obj, true );

        }

    }

?>
