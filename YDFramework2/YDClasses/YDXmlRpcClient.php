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
     *  This is the actual implementation of the YDXmlRpcClient class. It
     *  extends the IXR_Client class and adds support for GZip compressed 
     *  communications based on the HttpClient class.
     *
     *  @todo
     *      The HTTP Client class should correctly set the content type. If not,
     *      it's not possible to use this class to do the requests.
     */
    class YDXmlRpcClientCore extends IXR_Client {

        function YDXmlRpcClientCore( $url ) {
            $this->IXR_Client( $url );
        }

        /**
         *  This is our implementation of the query function from the IXR_Client
         *  class so that we can use the HttpClient class to do the HTTP stuff.
         */
        function query() {

            // Include the HTTP client
            require_once( YD_DIR_3RDP . '/HttpClient.class.php' );

            // Get the function arguments
            $args = func_get_args();

            // Get the method name
            $method = array_shift($args);

            // Create a new request
            $request = new IXR_Request( $method, $args );

            // Create a new HTTP client
            $client = new HttpClient( $this->server, $this->port );
            $client->useGzip( true );
            $client->setDebug( YD_DEBUG );
            $client->path = $this->path;
            $client->method = 'POST';
            $client->postdata = str_replace( "\n", '', $request->getXml() );

            // Show in debugging mode
            if ( $this->debug ) {
                echo( '<pre>' . htmlspecialchars( $client->postdata ) . "\n</pre>\n\n" );
            }

            // Now send the request
            $result = $client->doRequest();

            // Die with an error if any
            if ( ! $result ) {
                //die( 'An error occurred: '.$client->getError() );
                $this->error = new IXR_Error( -32300, $client->getError() );
                return false;
            }

            // Get the contents
            $contents = $client->getContent();

            // Show in debugging mode
            if ( $this->debug ) {
                echo( '<pre>' . htmlspecialchars( $contents ) . "\n</pre>\n\n" );
            }

            // Now parse what we've got back
            $this->message = new IXR_Message( $contents );
            if ( ! $this->message->parse() ) {
                // XML error
                $this->error = new IXR_Error( -32700, 'parse error. not well formed' );
                return false;
            }

            // Is the message a fault?
            if ( $this->message->messageType == 'fault' ) {
                $this->error = new IXR_Error( $this->message->faultCode, $this->message->faultString );
                return false;
            }

            // Message must be OK
            return true;

        }

    }

    /**
     *  This class defines an XML/RPC client.
     */
    class YDXmlRpcClient extends YDBase {

        /**
         *  This is the class constructor of the YDXmlRpcClient class.
         *
         *  @param $url The URL of the XML/RPC server.
         *
         *  @todo
         *      Move to our GZip enabled XML/RPC client as soon as we have the
         *      HttpClient class fixed.
         */
        function YDXmlRpcClient( $url ) {

            // Check for the url
            if ( empty( $url ) ) {
                new YDFatalError(
                    'You did not specify an URL for the YDXmlRpcClient.'
                );
            }

            // Instantiate the client
            //$this->_client = new YDXmlRpcClientCore( $url );
            $this->_client = new IXR_Client( $url );

            // Set the debugging mode
            if ( YD_DEBUG == 1 ) {
                $this->_client->debug = true;
            } else {
                $this->_client->debug = false;
            }

        }

        /**
         *  This function will execute the specified XML/RPC call on the server.
         *
         *  @params $method Name of the XML/RPC method.
         *  @params $params The parameters for this method.
         *
         *  @returns Returns the result of the query. If something went wrong, a
         *           YDFatalError is raised.
         */
        function execute() {

            // Get the function arguments
            $args = func_get_args();

            // Execute the function
            $result = call_user_func_array( 
                array( & $this->_client, 'query' ), $args 
            );

            // Check the result
            if ( $result == false ) {
                new YDFatalError( $this->_client->getErrorMessage() );
            }

            // Return the result
            return $this->_client->getResponse();

        }

    }

?>
