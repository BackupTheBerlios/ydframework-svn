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
     *  This is a general timer class that starts counting when it's instantiated,
     *  and which returns the elapsed time as soon as the finish method is called.
     *
     *  @todo
     *      Needs to be replaced with the PEAR::Bench package. More info on:
     *      http://www.pearfr.org/index.php/en/article/bench
     */
    class YDTimer extends YDBase {

        /**
         *  This is the class constructor of the YDTimer class.
         */
        function YDTimer() {

            // Initialize YDBase
            $this->YDBase();

            // Start the timer
            $this->_startTime = $this->getTime();

        }

        /**
         *  Stops the timer and returns the elapsed time.
         *
         *  @return The total elapsed time.
         */
        function finish() {
            $this->_endTime = $this->getTime();
            return round( ( $this->_endTime - $this->_startTime ), 4 );

        }

        /**
         *  This function returns the current microtime as a double.
         *
         *  @return Double containing the current time.
         */
        function getTime() {
            $time = explode ( ' ', microtime() );
            return ( doubleval( $time[0] ) + $time[1] );
        }

    }

?>