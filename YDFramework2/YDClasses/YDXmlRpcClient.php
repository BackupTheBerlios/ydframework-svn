<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );
	require_once( 'YDHttpClient.php' );
	require_once( 'IXR_Library.inc.php' );

	/**
	 *	This is the actual implementation of the YDXmlRpcClient class. It extends the IXR_Client class and adds support 
	 *	for GZip compressed communications based on the YDHttpClient class.
	 */
	class YDXmlRpcClientCore extends IXR_Client {

		/**
		 *	This is the class constructor for the YDXmlRpcClientCore class.
		 *
		 *	@param $url	The URL of the XML/RPC server.
		 */
		function YDXmlRpcClientCore( $url ) {
			$this->IXR_Client( $url );
		}

		/**
		 *	This is our implementation of the query function from the IXR_Client class so that we can use the 
		 *	YDHttpClient class to do the HTTP stuff.
		 */
		function query() {

			// Get the function arguments
			$args = func_get_args();
			$method = array_shift($args);

			// Create a new request
			$request = new IXR_Request( $method, $args );

			// Create a new HTTP client
			$client = new YDHttpClient( $this->server, $this->port );
			$client->useGzip( true );
			$client->setDebug( YD_DEBUG );
			$client->path = $this->path;
			$client->method = 'POST';
			$client->contenttype = 'text/xml';
			$client->postdata = str_replace( "\n", '', $request->getXml() );

			// Show in debugging mode
			if ( $this->debug ) {
				$tmp = htmlspecialchars( $client->postdata );
				echo( '<pre>' . $tmp . "\n</pre>\n\n" );
			}

			// Now send the request
			$result = $client->doRequest();

			// Die with an error if any
			if ( ! $result ) {
				$this->error = new IXR_Error( -32300, $client->getError() );
				return false;
			}

			// Get the contents
			$contents = $client->getContent();

			// Show in debugging mode
			if ( $this->debug ) {
				$tmp = htmlspecialchars( $contents );
				echo( '<pre>' . $tmp . "\n</pre>\n\n" );
			}

			// Now parse what we've got back
			$this->message = new IXR_Message( $contents );
			if ( ! $this->message->parse() ) {
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
	 *	This class defines an XML/RPC client.
	 */
	class YDXmlRpcClient extends YDBase {

		/**
		 *	This is the class constructor of the YDXmlRpcClient class.
		 *
		 *	@param $url	The URL of the XML/RPC server.
		 */
		function YDXmlRpcClient( $url ) {
			$this->_client = new YDXmlRpcClientCore( $url );
			if ( YD_DEBUG == 1 ) {
				$this->_client->debug = true;
			} else {
				$this->_client->debug = false;
			}
		}

		/**
		 *	This function will execute the specified XML/RPC call on the server.
		 *
		 *	@param $method	Name of the XML/RPC method.
		 *	@param $args	(optional) An array specifying the parameters for this method.
		 *
		 *	@returns	Returns the result of the query. If something went wrong, a YDFatalError is raised.
		 */
		function execute( $method, $args=array() ) {
			array_unshift( $args, $method );
			$result = call_user_func_array( array( & $this->_client, 'query' ), $args );
			if ( $result == false ) {
				YDFatalError( $this->_client->getErrorMessage() );
			}
			return $this->_client->getResponse();
		}

	}

?>
