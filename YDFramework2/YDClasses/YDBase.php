<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'ERROR: Yellow Duck Framework is not loaded.' );
    }

    // Include the needed files
    require_once( 'PEAR.php' );

    /**
     *  This is the base class for all other YD classes.
     */
    class YDBase extends PEAR {

        /** 
         *  The class constructor for the YDBase class.
         */
        function YDBase() {

            // Initialize PEAR
            $this->PEAR();

        }

        /**
         *  This function will serialize the current object. The serialized 
         *  output is GZip compressed to save space.
         */
        function serialize() {

            // We first serialize and then compress
            return gzcompress( serialize( $this ) );

        }

    }

?>