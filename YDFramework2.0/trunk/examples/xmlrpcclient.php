<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDXmlRpc.php' );

    // Class definition
    class xmlrpcclient extends YDRequest {

        // Class constructor
        function xmlrpcclient() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Create an XML/RPC client
            $client = new YDXmlRpcClient( 'http://www.grijzeblubber.be/bba/xmlrpc.php' );

            // tide.getTideForDay
            $result = $client->execute( 'tide.getTideForDay', array( '2004-05-11' ) );
            YDDebugUtil::dump( $result, 'tide.getTideForDay( 11/05/2004 )' );

        }

        // Default action
        function actionCurrentTime() {

            // Create an XML/RPC client
            $client = new YDXmlRpcClient( 'http://time.xmlrpc.com/RPC2' );

            // currentTime.getCurrentTime
            $result = $client->execute( 'currentTime.getCurrentTime' );
            YDDebugUtil::dump( $result, 'currentTime.getCurrentTime' );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>