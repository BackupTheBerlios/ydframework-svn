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
     *  This class is an object interfacing a Windows style INI file.
     *
     *  @todo
     *      Check if there are any PEAR classes that are able to handle this as
     *      well and maybe already in an object oriented way.
     */
    class YDIniFile {

        /**
         */
        function YDIniFile( $file ) {
        }
    
        /**
         *  Parse an ini file
         */
        function parseIniFile( $filename, $process_sections=false ) {

            // Start with an empty array
            $ini_array = array();

            // Start with no section name
            $sec_name = '';

            // Read in the file
            $lines = file( $filename );

            // Loop over the lines
            foreach ( $lines as $line ) {

                // Trim the spaces from the line
                $line = trim( $line );

                // Empty line, skip it
                if ( $line == '' ) {
                    continue;
                }

                // We have a section
                if( $line[0] == '[' && $line[strlen( $line ) - 1] == ']' ) {

                    // Get the section name
                    $sec_name = substr( $line, 1, strlen( $line ) - 2 );

                // We have a normal line
                } else {

                    // Find out the position of the =
                    $pos = strpos( $line, '=' );

                    // Get the property name and value
                    $property = substr( $line, 0, $pos );
                    $value = substr( $line, $pos + 1 );

                    // Strip the double quotes
                    if ( $value[0] == '"' && $value[strlen( $value )-1] == '"' ) {
                        $value = substr($value, 1, strlen( $value ) - 2);
                    }
                    
                    // Check if we need to process sections
                    if ( substr( $property, 0, 1 ) != ';' ) {
                        if( $process_sections ) {
                            $ini_array[$sec_name][$property] = $value;
                        } else {
                            $ini_array[$property] = $value;
                        }
                    }

                }

            }

            // Return the contents as an array
            return $ini_array;

        }

    }

?>
