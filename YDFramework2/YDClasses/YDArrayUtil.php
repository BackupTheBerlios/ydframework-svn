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
     *  This class houses all the array related utility functions. All the 
     *  methods are implemented as static methods and do not require you to
     *  create a class instance in order to use them.
     */
    class YDArrayUtil extends PEAR {

        /**
         *  This function will convert a single dimension array to a multi
         *  dimension array with the indicated number of colums. If the number
         *  of columns is 1, it will return the original array.
         *  
         *  If you enable the $fillLastRow option, it will fill the last row
         *  with null values to match the number of columns.
         *
         *  @param $array       The single dimension array you want to convert.
         *  @param $columns     The number of columns the table should have.
         *  @param $fillLastRow (optional) If true, the last row will be filled
         *                      with null values so that it matches the number 
         *                      of columns.
         *
         *  @returns A multi-dimension array with the contents of the original
         *           array converted to a table with the indicated number of
         *           colums.
         */
        function convertToTable( $array, $columns, $fillLastRow=false ) {

            // Check if the number of columns is an integer or not
            if ( ! is_int( $columns ) ) {
                new YDFatalError(
                    'Number of columns parameter of the '
                    . 'YDArrayUtil::convertToTable function should be an '
                    . 'integer.'
                );
            }

            // If the number of columns is 1, return the original array
            if ( $columns == 1 ) {
                return $array;
            }

            // Use the array_chunk function if possible
            if ( function_exists( 'array_chunk' ) ) {

                // Convert to a table
                $newArray = array_chunk( $array, $columns );

            } else {

                // Create an empty table
                $newArray = array();

                // Convert the array to a table
                $rowArray = array();
                foreach( $array as $item ) {
                    
                    // Check the length of the current row
                    if ( sizeof( $rowArray ) == $columns ) {
                        array_push( $newArray, $rowArray );
                        $rowArray = array();
                    }

                    // Add the element to the row
                    array_push( $rowArray, $item );

                }

                // Add the last row
                array_push( $newArray, $rowArray );

            }

            // Pad the last row
            if ( $fillLastRow ) {
                
                // Get the last row
                $lastRow = $newArray[sizeof( $newArray )-1];

                // Get the number of missing elements
                $numMissing = $columns - sizeof( $lastRow );

                // Add the item
                for ( $i = 0; $i < $numMissing; $i++ ) {
                    array_push( $newArray[sizeof( $newArray )-1], null );
                }

            }

            // Return the array
            return $newArray;

        }
    
    }

?>
