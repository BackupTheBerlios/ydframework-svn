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
     *  This class defines an XML/RPC server. This XML/RPC server supports
     *  introspection and is also able to handle HTTP GET requests. In case of a
     *  HTTP GET request, it will display a page describing the service. This
     *  page will list methods with their parameters and help message. It will
     *  also list the capabilities of the XML/RPC server.
     *
     *  This class supports the same actions as a normal YDRequest, so you can
     *  use all the normal functions to restrict access etc. Make sure you do
     *  not override the process function, or no XML/RPC requests will be served
     *  anymore (unless you know what you are doing).
     *
     *  @remark
     *      When raising errors in this class, make sure you raise the right
     *      kind or error. If you have an XML/RPC request that needs to return
     *      an error, you need to return an IXR_Error. If you are not serving an
     *      XML/RPC request, you need to return a YDError or YDFatalError.
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
         *  This function will return true if the request served by the URL was
         *  a genuine XML/RPC call (by checking if there was POST data or if it
         *  was a normal HTTP GET function.
         *
         *  @returns This function returns true if HTTP POST data was found.
         */
        function isXmlRpcRequest() {

            // Check for the HTTP RAW POST DATA
            if ( $GLOBALS['HTTP_RAW_POST_DATA'] ) {
                return true;
            } else {
                return false;
            }

        }

        /**
         *  We override the process function because this is where we need to
         *  check if we need to process the post data or not.
         */
        function process() {

            // Check for post data
            if ( ! $this->isXmlRpcRequest() ) {
                $this->requestNotXmlRpc();
                return;
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
         */
        function requestNotXmlRpc() {

            // Create a new list of the supported methods
            $methods = array();

            // Get the methods
            $methodNames = $this->listMethods( null );

            // Loop over the methods
            foreach( $methodNames as $method ) {

                // Get the input and output parameters
                if ( $this->signatures[ $method ] ) {
                    if ( sizeof( $this->signatures[ $method ] ) == 1 ) {
                        $paramsIn = null;
                        $paramsOut = $this->signatures[ $method ];
                    } else {
                        $paramsIn = $this->signatures[ $method ];
                        $paramsOut = array_shift( $paramsIn );
                    }
                } else {
                        $paramsIn = null;
                        $paramsOut = null;
                }

                // Create a new array with the capabilities
                $methodInfo = array();
                $methodInfo['signature'] = $this->signatures[ $method ];
                $methodInfo['paramsIn'] = $paramsIn;
                $methodInfo['paramsOut'] = $paramsOut;
                $methodInfo['help'] = $this->help[ $method ];

                // Add it to the list
                $methods[ $method ] = $methodInfo;

            }

            // Create a new template
            require_once( 'YDTemplate.php' );

            // Instantiate the template
            $template = new YDTemplate();
            $template->setVar( 'methods', $methods );
            $template->setVar( 'xmlRpcUrl', $this->getCurrentUrl() );
            $template->setVar( 'capabilities', $this->getCapabilities( null ) );
            $template->setVar( 'rowcolor', '#EEEEEE' );
            echo( $template->getOutput(
                dirname( __FILE__ ) . '/YDXmlRpcServer'
            ) );

        }

        /**
         *  This function gets called by the process function if the specified
         *  action was not implemented in the class. By default, it raises a
         *  fatal error indicating this.
         *
         *  Another option might be to redirect to the default function.
         *
         *  @param $action The name of the action that is missing.
         */
        function errorMissingAction( $action ) {

            // Construct the error message
            $err = 'Class ' . get_class( $this ) . ' does not contain an '
                 . ' action called "' . strtolower( $action ) . '" '
                 . '(function name).';

            // Raise the right error
            if ( ! $this->isXmlRpcRequest() ) {
                return new IXR_Error( -100001, $err );
            } else {
                new YDFatalError( $err );
            }

        }

        /**
         *  If the authentication was unsuccesful, this function is execute just
         *  before the actual processing of the request. You will need to
         *  override this function in the classes that implement the YDRequest
         *  class.
         */
        function authenticationFailed() {

            // Construct the error message
            $err = 'Authentication failed.';

            // Raise the right error
            if ( ! $this->isXmlRpcRequest() ) {
                return new IXR_Error( -100002, $err );
            } else {
                new YDFatalError( $err );
            }

        }

        /**
         *  If the current request is not allowed to execute the specified
         *  action, the code in this function gets executed. You will need to
         *  override this function in the classes that implement the YDRequest
         *  class.
         */
        function actionNotAllowed() {

            // Construct the error message
            $err = 'You are not allow to access the action "'
                 . $this->getActionName() . '"';

            // Raise the right error
            if ( ! $this->isXmlRpcRequest() ) {
                return new IXR_Error( -100002, $err );
            } else {
                new YDFatalError( $err );
            }

        }

    }

?>
