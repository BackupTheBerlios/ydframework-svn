<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	/**
	 *	This class implements a request. The request is the base class you can use to base your scripts on. The request
	 *	class is the one taking care of the action dispatching when a script is executed. Also the authentication
	 *	framework is implemented in this class.
	 */
	class YDRequest extends YDBase {

		/**
		 *	This is the class constructor for the YDRequest class. This sets the default action to 'actionDefault' (but
		 *	can be overridden later on).
		 */
		function YDRequest() {

			// Initialize YDBase
			$this->YDBase();

			// Set up the request class
			$this->__isInitialized = true;
			$this->__requiresAuthentication = false;

		}

		/**
		 *	This function returns the name of the action that was specified by the do parameter specified in the URL. 
		 *	This does NOT include the action prefix.
		 *
		 *	@returns	The name of the current action.
		 */
		function getActionName() {
			if ( empty( $_GET[ YD_ACTION_PARAM ] ) ) {
				$action = YD_ACTION_DEFAULT;
			} else {
				$action = $_GET[ YD_ACTION_PARAM ];
			}
			$action = strtolower( $action );
			if ( strpos( $action, 'action' ) === 0 ) {
				$action = substr( $action, strlen( 'action' ) );
			}
			return strtolower( $action );
		}

		/**
		 *	This function will return the full URL to the current request, including hostname, port and request URI.
		 *
		 *	@returns	The full url of the current request.
		 */
		function getCurrentUrl() {
			$url = 'http://' . strtolower( $_SERVER['SERVER_NAME'] );
			if ( $_SERVER['SERVER_PORT'] != '80' ) {
				$url = $url . ':' . $_SERVER['SERVER_PORT'];
			}
			$url = $url . $_SERVER['REQUEST_URI'];
			return $url;
		}

		/**
		 *	This function will return a normalized URI. This is the URI without the debug information and will all keys
		 *	sorted alphabetically.
		 *
		 *	@returns	Normalized request URI.
		 */
		function getNormalizedUri() {
			$url = parse_url( $this->getCurrentUrl() );
			$params = $_GET;
			ksort( $params );
			if ( isset( $params['YD_DEBUG'] ) ) {
				unset( $params['YD_DEBUG'] );
			}
			$query = array();
			foreach( $params as $key=>$val ) {
				array_push( $query, urlencode( $key ) . '=' . urlencode( $val ) );
			}
			$url['query'] = implode( '&', $query );
			if ( ! empty( $url['query'] ) ) {
				$url['query'] = '?' . $url['query'];
			}
			return $url['path'] . $url['query'];
		}

		/**
		 *	This function will forward the current request to a different action. By using this function, you make sure
		 *	the action names and so on are also update to reflect this.
		 *
		 *	@param $action	Name of the action to forward to.
		 */
		function forward( $action ) {
			if ( strpos( $action, 'action' ) === 0 ) {
				$action = substr( $action, strlen( 'action' ) );
			}
			$_GET[ YD_ACTION_PARAM ] = $action;
			call_user_func( array( $this, 'action' . $action ) );
		}

		/**
		 *	This function will to a HTTP redirect to the specified action.
		 *
		 *	@param $action	(optional) Name of the action to redirect to. If no action is specified, it will redirect to
		 *					the default action.
		 */
		function redirectToAction( $action='default' ) {
			if ( strpos( $action, 'action' ) === 0 ) {
				$action = substr( $action, strlen( 'action' ) );
			}
			$url = YD_SELF_SCRIPT . '?' . YD_ACTION_PARAM . '=' . $action;
			$this->redirect( $url );
		}
		
		/**
		 *	This function will redirect the current request to a new URL. This does an HTTP redirect. If you want to 
		 *	forward to a different action in the same request, please use the forward function instead.
		 *
		 *	@param $url	The URL to redirect to.
		 */
		function redirect( $url ) {
			header( 'Location: '. $url );
			die();
		}

		/**
		 *	This function will check if the request was initialized properly.
		 *
		 *	@returns	Returns a boolean indicating if the request was initialized properly or not.
		 */
		function isInitialized() {
			if ( $this->__isInitialized == true ) {
				return true;
			} else {
				return false;
			};
		}

		/**
		 *	This function is the intelligent part of the YDRequest class. Based on the action specified in the URL, it
		 *	will execute the right function of this class. It performs quite a lot of error checking to see whether the
		 *	action function is specified or not.
		 */
		function process() {
			if ( empty( $_GET[ YD_ACTION_PARAM ] ) ) {
				$action = YD_ACTION_DEFAULT;
			} else {
				$action = 'action' . $_GET[ YD_ACTION_PARAM ];
			}
			if ( ! method_exists( $this, $action ) ) {
				$this->errorMissingAction( $action );
			}
			call_user_func( array( $this, $action ) );
		}

		/**
		 *	This function gets called by the process function if the specified action was not implemented in the class.
		 *	By default, it raises a fatal error indicating this.
		 *
		 *	@param $action	The name of the action that is missing.
		 */
		function errorMissingAction( $action ) {
			trigger_error(
				'Class ' . get_class( $this ) . ' does not contain an action called "' . strtolower( $action ) . '" '
				. '(function name).', YD_ERROR
			);
		}

		/**
		 *	This is the placeholder for the default function. You need to override this function in the classes you
		 *	derive from the YDRequest class. This function takes no arguments.
		 */
		function actionDefault() {
			echo( 'action: actionDefault' );
			echo( "\n" );
			echo( 'You need to override this function to have the default action do something.' );
		}

		/**
		 *	This function indicates that this request needs authentication. If you change this to true, it will cause
		 *	the Yellow Duck Framework to check the isAuthenticated function to see if you are authenticated or not.
		 *
		 *	@param $bool	Boolean indicating if this request requires authentication or not.
		 */
		function setRequiresAuthentication( $bool ) {
			if ( $bool == true ) {
				$this->__requiresAuthentication = true;
			} else {
				$this->__requiresAuthentication = false;
			}
		}

		/**
		 *	This function will return true or false to indicate whether this class needs authentication or not.
		 *
		 *	@returns	This function returns true or false to indicate if this requires authentication or not.
		 */
		function getRequiresAuthentication() {
			if ( $this->__requiresAuthentication == false ) {
				return false;
			} else {
				return true;
			}
		}

		/**
		 *	This function performs the actual authentication and returns either true or false. By default, this function
		 *	is not implemented and simply returns true. You will need to override this function in your own class to 
		 *	define how you want the authentication to happen.
		 *
		 *	@returns	Boolean indicating is the client is authenticated or not.
		 */
		function isAuthenticated() {
			return true;
		}

		/**
		 *	If the authentication was succesful, this function is execute just before the actual processing of the 
		 *	request. You will need to override this function in the classes that implement the YDRequest class.
		 */
		function authenticationSucceeded() {
		}

		/**
		 *	If the authentication was unsuccesful, this function is execute just before the actual processing of the 
		 *	request. You will need to override this function in the classes that implement the YDRequest class.
		 */
		function authenticationFailed() {
			trigger_error( 'Authentication failed.', YD_ERROR );
		}

		/**
		 *	You can use this function to check wether the current request is allowed to access the specified action or
		 *	not. This check is performed just before the actual processing of the request and just after the
		 *	authentication if any authentication was needed. If this check fails, the code in the actionNotAllowed 
		 *	method is performed. You will need to override this function in the classes that implement the YDRequest
		 *	class.
		 *
		 *	@returns	Boolean indicating if the current request is allowed to perform the specified action or not.
		 */
		function isActionAllowed() {
			return true;
		}

		/**
		 *	If the current request is not allowed to execute the specified action, the code in this function gets 
		 *	executed. You will need to override this function in the classes that implement the YDRequest class.
		 */
		function actionNotAllowed() {
			trigger_error( 'You are not allow to access the action "' . $this->getActionName() . '"', YD_ERROR );
		}

	}

?>
