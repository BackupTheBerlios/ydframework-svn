<?php

    /*
     *  This examples demonstrates the XML/RPC client.
     */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    require_once( 'YDRequest.php' );
    require_once( 'YDXmlRpcClient.php' );
    require_once( 'YDDebugUtil.php' );

    // Class definition
    class xmlrpcclient1Request extends YDRequest {

        // Class constructor
        function xmlrpcclient1Request() {

            // Initialize the parent class
            $this->YDRequest();

        }

        // Default action
        function actionDefault() {

            // Create an XML/RPC client
            $client = new YDXmlRpcClient(
                'http://www.grijzeblubber.be/bba/xmlrpc.php'
            );

            // tide.getTideForDay
            echo( '<p>tide.getTideForDay( 11/05/2004 )</p>' );
            $result = $client->execute(
                'tide.getTideForDay', array( '2004-05-11' )
            );
            YDDebugUtil::dump( $result );

        }

        // Default action
        function actionCurrentTime() {

            // Create an XML/RPC client
            $client = new YDXmlRpcClient( 'http://time.xmlrpc.com/RPC2' );

            // currentTime.getCurrentTime
            echo( '<p>currentTime.getCurrentTime</p>' );
            $result = $client->execute( 'currentTime.getCurrentTime' );
            YDDebugUtil::dump( $result );

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>