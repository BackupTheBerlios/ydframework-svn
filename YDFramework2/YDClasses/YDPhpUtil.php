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
     *  This class houses all the PHP related utility functions. All the
     *  methods are implemented as static methods and do not require you to
     *  create a class instance in order to use them.
     */
    class YDPhpUtil extends YDBase {

        /**
         *  This function will return true if the current version is lower than
         *  the indicated version. Internally, this uses the phpversion()
         *  function to get the current version number.
         *
         *  @param $vercheck Minimum version required.
         *
         *  @return Boolean indicating if the current version number is higher
         *          or lower than the indicated PHP version.
         */
        function versionCheck( $vercheck ) {
            $minver = explode( '.', $vercheck );
            $curver = explode( '.', phpversion() );
            if (
                ( $curver[0] >= $minver[0] ) && ( $curver[1] >= $minver[1] )
                &&
                ( $curver[1] >= $minver[1] ) && ( $curver[2][0] > $minver[2][0] )
            ) {
                return true;
            } else {
                return false;
            }
        }

    }

?>
