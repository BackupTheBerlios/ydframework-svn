<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDXmlRpc.php' );

	// Class definition
	class xmlrpcserver extends YDXmlRpcServer {

		// Class constructor
		function xmlrpcserver() {

			// Initialize the parent class
			$this->YDXmlRpcServer();
			
			// Register the methods
			$this->registerMethod(
				'echo', array( & $this, 'xmlrpcEcho' ), array( 'string', 'string' ), 'Echoes a string'
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
