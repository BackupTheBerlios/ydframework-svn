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
     *      We need to add a function getOutput which should return the results
     *      as a nice text table.
     */
    class YDTimer extends YDBase {

        /**
         *  This is the class constructor of the YDTimer class.
         */
        function YDTimer() {

            // Initialize YDBase
            $this->YDBase();

            // Create a new timer object
            $this->_timer = new Benchmark_Timer();

        }

        /**
         *  This function will start the timer.
         */
        function start() {
            $this->_timer->start();
        }

        /**
         *  This function will set a mark in the timer.
         *
         *  @param $marker Name of the marker
         */
        function setMarker( $marker ) {
            $this->_timer->setMarker( $marker );
        }

        /**
         *  This function will stop the timer.
         */
        function stop() {
            $this->_timer->stop();
        }

        /**
         *  This function will return the info of the timer.
         *
         *  @returns Array with the timer information.
         */
        function getResult() {
            return $this->_timer->getProfiling();
        }

        /**
         *  This function will return the info of the timer.
         *
         *  @returns Array with the timer information.
         */
        function getOutput() {
            return strip_tags( $this->_timer->getOutput() );
        }
    
    }

?>