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

    }

?>