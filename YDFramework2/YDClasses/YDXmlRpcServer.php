<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'ERROR: Yellow Duck Framework is not loaded.' );
    }

    // Includes
    require_once( 'YDError.php' );
    require_once( 'YDRequest.php' );
    require_once( 'IXR_Library.inc.php' );

    /**
     *  This class defines an XML/RPC server.
     *
     *  @todo
     *      Make sure that all the relevant functions from the YDRequest server
     *      return XML/RPC errors instead of YDFatalErrors.
     */
    class YDXmlRpcServer extends YDRequest {

        /**
         *  This is the class constructor of the YDXmlRpcServer class.
         */
        function YDXmlRpcServer() {

            // Initialize YDRequest
            $this->YDRequest();

            // Aggregate the functions from the IXR_IntrospectionServer class
            aggregate( $this, 'IXR_IntrospectionServer' );
            IXR_IntrospectionServer::IXR_IntrospectionServer();

        }

        /**
         *  We override the process function because this is where we need to
         *  check if we need to process the post data or not.
         */
        function process() {

            // Check for post data
            if ( ! $GLOBALS['HTTP_RAW_POST_DATA'] ) {
                $this->postdataMissing();
            }

            // Just server the server
            $this->serve();

        }

        /**
         *  Use this function to register a new XML/RPC method for this server.
         *
         *  @param $method    Name of the XML/RPC method.
         *  @param $function  Function that needs to be executed for this
         *                    XML/RPC request.
         *  @param $signature The signature for this function.
         *  @param $help      (optional) The help text describing this method.
         */
        function registerMethod( $method, $function, $signature, $help='' ) {
            $this->addCallback( $method, $function, $signature, $help );
        }

        /**
         *  This function will be executed if there is no post data found. This
         *  is happening when there is no XML/RPC request found.
         *
         *  @todo
         *      If no post data, give an overview page describing the XML/RPC
         *      services and it's exposed functions.
         */
        function postdataMissing() {
            
            // Raise a fatal error.
            new YDFatalError( 'XML-RPC server accepts POST requests only.' );

        }

    }

?>
