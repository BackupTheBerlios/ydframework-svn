<?php

    /*
     *  This examples demonstrates the XML/RPC client.
     */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    //require_once( 'YDRequest.php' );
    require_once( 'YDXmlRpcServer.php' );
    require_once( 'YDDebugUtil.php' );

    // Class definition
    class xmlrpcserver1Request extends YDXmlRpcServer {

        // Class constructor
        function xmlrpcserver1Request() {

            // Initialize the parent class
            $this->YDXmlRpcServer();
            
            // Register the methods
            $this->registerMethod(
                'echo', 'this:xmlrpcEcho', array( 'string', 'string' )
            );

        }

        // The method which just echoes a string
        function xmlrpcEcho( $var ) {
            return $var;
        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>