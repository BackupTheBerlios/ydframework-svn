<?php

    /*

       Yellow Duck Framework version 2.0
       (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

    */

    /**
     *  This class defines an error using an error message. Each error object
     *  has just one error message. The class is also the base class for al
     *  other error classes.
     *
     *  @todo
     *      Implement new error mechanism.
     */
    class YDError extends YDBase {

        /**
         *  The class constructor for the YDError class. When you instantiate
         *  this class, you need to specify the error message as a string.
         *
         *  @param $errorMessage The string indicating the error message.
         */
        function YDError( $errorMessage ) {

            // Initialize YDBase
            $this->YDBase();

            // Standard variables
            $this->_errorMessage = $errorMessage;

        }

        /**
         *  This function returns the string of the error message. If no error
         *  message is specified, the string "Unknow error" is returned.
         *
         *  @returns String indicating the error message.
         */
        function getError() {
            if ( empty( $this->_errorMessage ) ) {
                return 'Unknown error';
            } else {
                return $this->_errorMessage;
            }
        }

        /**
         *  This function will check if the given object is an error object or not.
         *  All objects that have the YDError class as one of it's ancestors is
         *  considered as being an error class.
         *
         *  @remark
         *      This is a static method. You do not need to instantiate the
         *      the class in order to use this function.
         *
         *  @return Boolean indicating if the object is an error object or not.
         */
        function isError( $obj ) {
            return YDObjectUtil::isSubClass( $obj, 'YDError' );
        }

    }

    /**
     *  This class defines a fatal error. Since this class is based on the
     *  YDError class, you also need to specify an error message. The difference
     *  is that this class will output the error message, and will stop the
     *  execution of the script after this error occured.
     *
     *  By default, error messages are shown in a light yellow box with red text
     *  displaying the error message.
     *
     *  @todo
     *      Implement new error mechanism.
     */
    class YDFatalError extends YDError {

        /**
         *  The class constructor for the YDFatalError class. When you instantiate
         *  this class, you need to specify the error message as a string.
         *
         *  @param $errorMessage The string indicating the error message.
         */
        function YDFatalError( $errorMessage ) {

            // Initialize the parent
            $this->YDError( $errorMessage );

            // Echo the error message
            echo( '<p style="background-color: lightyellow;">' );
            echo( '<font color="red" size="-1">' );
            echo( '<b>' . YD_FW_NAME . ' Fatal Error</b>' );
            echo( '<br>' );
            echo( $this->getError() );
            echo( '</font></p>' );

            // Die the execution
            die();

        }

    }

?>
