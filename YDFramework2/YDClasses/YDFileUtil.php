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
     *  This class houses all the file related utility functions. All the 
     *  methods are implemented as static methods and do not require you to
     *  create a class instance in order to use them.
     */
    class YDFileUtil {

        /**
         *  Function to emulate the fnmatch function from UNIX which is not
         *  available on all servers.
         *
         *  @remark
         *      This function is a reformatted version of the function found on:
         *      http://www.php.net/manual/en/function.fnmatch.php#31353
         *
         *  @param $pattern Pattern to match the file against.
         *  @param $file    File name that needs to be checked.
         *
         *  @return Boolean indicating if the file matched the pattern or not.
         */
        function match( $pattern, $file ) {

            // Loop over the characters of the pattern
            for ( $i=0; $i < strlen( $pattern ); $i++ ) {

                // Character is a *
                if ( $pattern[$i] == '*' ) {
                    for (
                        $c = $i;
                        $c < max( strlen( $pattern ), strlen( $file ) );
                        $c++
                    ) {
                        if ( YDFileUtil::match(
                            substr( $pattern, $i+1 ), substr( $file, $c ) ) 
                        ) {
                            return true;
                        }
                    }
                    return false;
                }

                // Pattern is a [
                if ( $pattern[$i] == '[' ) {
                    $letter_set = array();
                    for( $c=$i+1; $c < strlen( $pattern ); $c++ ) {
                        if ( $pattern[$c] != ']' ) {
                            array_push( $letter_set, $pattern[$c] );
                        } else {
                            break;
                        }
                    }
                    foreach ( $letter_set as $letter ) {
                        if ( YDFileUtil::match(
                            $letter . substr( $pattern, $c+1 ),
                            substr( $file, $i ) ) 
                        ) {
                            return true;
                        }
                    }
                    return false; 
                }

                // Pattern is a ?
                if( $pattern[$i] == '?' ) {
                    continue;
                }

                // Pattern not the same as the file character
                if ( $pattern[$i] != $file[$i] ) {
                    return false;
                }

            }

            // All the rest returns positive
            return true;

        }

    }

?>
