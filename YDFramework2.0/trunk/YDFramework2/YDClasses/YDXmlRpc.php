<?php

	/*
	
		Yellow Duck Framework version 2.0
		Copyright (C) (c) copyright 2004 Pieter Claerhout
	
		This library is free software; you can redistribute it and/or
		modify it under the terms of the GNU Lesser General Public
		License as published by the Free Software Foundation; either
		version 2.1 of the License, or (at your option) any later version.
	
		This library is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
		Lesser General Public License for more details.
	
		You should have received a copy of the GNU Lesser General Public
		License along with this library; if not, write to the Free Software
		Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
	
	*/

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDHttpClient.php' );
	YDInclude( 'IXR_Library.inc.php' );

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
			$client->handle_redirects = false;

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
				//trigger_error( $this->_client->getErrorMessage(), YD_ERROR );
				return false;
			}
			return $this->_client->getResponse();
		}

		/**
		 *	This function returns the last error that occured.
		 *
		 *	@returns	Array with the error code and error message.
		 */
		function getErrorMsg() {
			//YDDebugUtil::dump( $this->_client->error );
			/*
			return array(
				'code' => $this->_client->error->code,
				'message' => $this->_client->error->message,
			);
			*/
			return $this->_client->error->code . '  ' . $this->_client->error->message;
		}

	}

	/**
	 *	This class implements the actual XML/RPC server.
	 *
	 *	@internal
	 */
	class YDXmlRpcServerImpl extends IXR_IntrospectionServer {

		/**
		 *	This is the class constructor of the YDXmlRpcServerImpl class.
		 */
		function YDXmlRpcServerImpl() {

			// Initialize parent
			$this->IXR_IntrospectionServer();

		}

		/**
		 *	This function will execute the actual method.
		 *
		 *	@param $methodname	Name of the method to execute.
		 *	@param $args		The arguments for executing the method.
		 *
		 *	@returns	The results of the method.
		 *
		 *	@internal
		 */
		function call( $methodname, $args ) {
			if ( ! $this->hasMethod( $methodname ) ) {
				return new IXR_Error( -32601, 'server error. requested method ' . $methodname . ' does not exist.' );
			}
			$method = $this->callbacks[ $methodname ];
			if ( count( $args ) == 1 ) {
				$args = $args[0];
			}
			if ( is_array( $method ) ) {
				if ( ! method_exists( $method[0], $method[1] ) ) {
					return new IXR_Error( -32601, 'server error. requested class method "'.$method.'" does not exist.' );
				}
				$result = call_user_func( $method, $args );
			} elseif ( substr( $method, 0, 5 ) == 'this:' ) {
				$method = substr( $method, 5 );
				if ( ! method_exists( $this, $method ) ) {
					return new IXR_Error( -32601, 'server error. requested class method "' . $method . '" does not exist.' );
				}
				$result = $this->$method( $args );
			} else {
				if ( ! function_exists( $method ) ) {
					return new IXR_Error( -32601, 'server error. requested function "'.$method.'" does not exist.' );
				}
				$result = $method( $args );
			}
			return $result;
		}

	}

	/**
	 *	This class defines an XML/RPC server. This XML/RPC server supports introspection and is also able to handle 
	 *	HTTP GET requests. In case of a HTTP GET request, it will display a page describing the service. This page will
	 *	list methods with their parameters and help message. It will also list the capabilities of the XML/RPC server.
	 *
	 *	This class supports the same actions as a normal YDRequest, so you can use all the normal functions to restrict 
	 *	access etc. Make sure you do not override the process function, or no XML/RPC requests will be served anymore 
	 *	(unless you know what you are doing).
	 *
	 *	@remark
	 *		When raising errors in this class, make sure you raise the right kind or error. If you have an XML/RPC 
	 *		request that needs to return an error, you need to return an IXR_Error. If you are not serving an XML/RPC
	 *		request, you need to return a YDError or YDFatalError.
	 */
	class YDXmlRpcServer extends YDRequest {

		/**
		 *	This is the class constructor of the YDXmlRpcServer class.
		 */
		function YDXmlRpcServer() {

			// Initialize YDRequest
			$this->YDRequest();

			// Instantiate the server object
			$this->_server = new YDXmlRpcServerImpl();

		}

		/**
		 *	This function will return true if the request served by the URL was a genuine XML/RPC call (by checking if
		 *	there was POST data or if it was a normal HTTP GET function.
		 *
		 *	@returns	This function returns true if HTTP POST data was found.
		 */
		function isXmlRpcRequest() {
			if ( @ $GLOBALS['HTTP_RAW_POST_DATA'] ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 *	We override the process function because this is where we need to check if we need to process the post data
		 *	or not.
		 */
		function process() {
			if ( ! $this->isXmlRpcRequest() ) {
				$this->requestNotXmlRpc();
				return;
			}
			$this->_server->serve();
		}

		/**
		 *	Use this function to register a new XML/RPC method for this server.
		 *
		 *	@param $method		Name of the XML/RPC method.
		 *	@param $function	Function that needs to be executed for this XML/RPC request.
		 *	@param $signature	The signature for this function.
		 *	@param $help		(optional) The help text describing this method.
		 */
		function registerMethod( $method, $function, $signature, $help='' ) {
			$this->_server->addCallback( $method, $function, $signature, $help );
		}

		/**
		 *	This function will be executed if there is no post data found. This is happening when there is no XML/RPC
		 *	request found.
		 */
		function requestNotXmlRpc() {

			// Get the list of methods
			$methods = array();
			$methodNames = $this->_server->listMethods( null );

			// Loop over the methods
			foreach( $methodNames as $method ) {
				if ( isset( $this->_server->signatures[ $method ] ) ) {
					if ( sizeof( $this->_server->signatures[ $method ] ) == 1 ) {
						$paramsIn = null;
						$paramsOut = $this->_server->signatures[ $method ];
					} else {
						$paramsIn = $this->_server->signatures[ $method ];
						$paramsOut = array_shift( $paramsIn );
					}
				} else {
					$paramsIn = null;
					$paramsOut = null;
				}
				$methodInfo = array();
				$methodInfo['signature'] = @ $this->_server->signatures[ $method ];
				$methodInfo['paramsIn'] = $paramsIn;
				$methodInfo['paramsOut'] = $paramsOut;
				$methodInfo['help'] = @ $this->_server->help[ $method ];
				$methods[ $method ] = $methodInfo;
			}

			// Create a new template
			YDInclude( 'YDTemplate.php' );
			$template = new YDTemplate();
			$template->template_dir = dirname( __FILE__ );
			$template->assign( 'methods', $methods );
			$template->assign( 'xmlRpcUrl', $this->getCurrentUrl() );
			$template->assign( 'capabilities', $this->_server->getCapabilities( null ) );
			$template->assign( 'rowcolor', '#EEEEEE' );
			$template->display( 'YDXmlRpcServer.tpl' );

		}

		/**
		 *	This function gets called by the process function if the specified action was not implemented in the class.
		 *	By default, it raises a fatal error indicating this. Another option might be to redirect to the default
		 *	action.
		 *
		 *	@param	$action The name of the action that is missing.
		 */
		function errorMissingAction( $action ) {
			$err = 'Class ' . get_class( $this ) . ' does not contain an action called "' . strtolower( $action ) . '" '
				 . '(function name).';
			if ( ! $this->isXmlRpcRequest() ) {
				return new IXR_Error( -100001, $err );
			} else {
				trigger_error( $err, YD_ERROR );
			}
		}

		/**
		 *	If the authentication was unsuccesful, this function is execute just before the actual processing of the 
		 *	request. You will need to override this function in the classes that implement the YDRequest class.
		 */
		function authenticationFailed() {
			$err = 'Authentication failed.';
			if ( ! $this->isXmlRpcRequest() ) {
				return new IXR_Error( -100002, $err );
			} else {
				trigger_error( $err, YD_ERROR );
			}
		}

		/**
		 *	If the current request is not allowed to execute the specified action, the code in this function gets
		 *	executed. You will need to override this function in the classes that implement the YDRequest class.
		 */
		function errorActionNotAllowed() {
			$err = 'You are not allow to access the action "' . $this->getActionName() . '"';
			if ( ! $this->isXmlRpcRequest() ) {
				return new IXR_Error( -100002, $err );
			} else {
				trigger_error( $err, YD_ERROR );
			}
		}

	}

?>
