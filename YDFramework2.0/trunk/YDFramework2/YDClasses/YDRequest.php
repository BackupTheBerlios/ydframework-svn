<?php

    /*

        Yellow Duck Framework version 2.0
        (c) Copyright 2002-2005 Pieter Claerhout

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

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    /**
     *	This class implements a request. The request is the base class you can use to base your scripts on. The request
     *	class is the one taking care of the action dispatching when a script is executed. Also the authentication
     *	framework is implemented in this class.
     */
    class YDRequest extends YDBase {

        var $_callbacks = null;

        /**
         *	This is the class constructor for the YDRequest class. This sets the default action to 'actionDefault' (but
         *	can be overridden later on).
         *
         *  @param  $cookiePath     (optional) The default cookie path. Defaults to '/'.
         *  @param  $cookieLifeTime (optional) The default cookie life time. Defaults to 31536000 seconds (1 year).
         */
        function YDRequest( $cookiePath='/', $cookieLifeTime=null ) {

            // Initialize YDBase
            $this->YDBase();

            // Set up the request class
            $this->__isInitialized = true;
            $this->__requiresAuthentication = false;

            // setup the callbacks object
            $this->_callbacks = new YDBase();

            // this is for callbacks applicable to all actions
            $this->_callbacks->set( 'action',   array( 'before' => array(), 'after' => array() ) );

            // The default cookie path
            $this->cookiePath = $cookiePath;
            if ( $cookieLifeTime == null ) {
                $cookieLifeTime = time() + 31536000;
            }
            $this->cookieLifeTime = $cookieLifeTime;

        }

        /**
         *	This function returns the name of the action that was specified by the do parameter specified in the URL.
         *	This does NOT include the action prefix.
         *
         *	@static
         *
         *	@returns	The name of the current action.
         */
        function getActionName() {
            $action = strtolower( empty( $_GET[ 'do' ] ) ? 'actionDefault' : 'action' . $_GET[ 'do' ] );
            if ( strpos( $action, 'action' ) === 0 ) {
                $action = substr( $action, strlen( 'action' ) );
            }
            return strtolower( $action );
        }

        /**
         *  Get the value of a query string variable.
         *
         *  @param  $name       The name of the query string variable.
         *  @param  $default    (optional) The default value if the query string variable is not existing.
         *  @param  $values     (optional) The allowed values as an array.
         *  @param  $use_keys   (optional) Use the keys from the values array instead of the values. Defaults to false.
         *
         *  @returns    The value of the query string variable as a string.
         */
        function getQueryStringParameter( $name, $default='', $values=null, $use_keys=false ) {
            if ( isset( $_GET[$name] ) ) {
                if ( ! is_null( $values ) ) {
                    $values = $use_keys ? array_keys( $values ) : array_values( $values );;
                    if ( in_array( $_GET[$name], $values ) ) {
                        return $_GET[$name];
                    } else {
                        return $default;
                    }
                } else {
                    return $_GET[$name];
                }
            } else {
                return $default;
            }
        }

        /**
         *  Get the value of a query string variable as an integer.
         *
         *  @param  $name    The name of the query string variable.
         *  @param  $default (optional) The default value if the query string variable is not existing.
         *  @param  $values  (optional) The allowed values as an array.
         *  @param  $use_keys   (optional) Use the keys from the values array instead of the values. Defaults to false.
         *
         *  @returns    The value of the query string variable as an integer.
         */
        function getQueryStringParameterAsInt( $name, $default=null, $values=null, $use_keys=false ) {
            $value = $this->getQueryStringParameter( $name, $default, $values, $use_keys );
            return is_numeric( $value ) ? intval( $value ) : $default;
        }

        /**
         *  Get the value of a query string variable. When the required parameter is not set, it will redirect to the
         *  indicated action.
         *
         *  @param  $name               The name of the query string variable.
         *  @param  $default            (optional) The default value if the query string variable is not existing.
         *  @param  $values             (optional) The allowed values as an array.
         *  @param  $action_if_fails    (optional) The action to redirect of if the parameter is invalid.
         *  @param  $use_keys   (optional) Use the keys from the values array instead of the values. Defaults to false.
         *
         *  @returns    The value of the query string variable as a string.
         */
        function getRequiredQueryStringParameter( $name, $default, $values=null, $action_if_fails='default', $use_keys=false ) {
            $result = $this->getQueryStringParameter( $name, $default, $values, $use_keys );
            if ( empty( $result ) ) {
                $this->redirectToAction( $action_if_fails );
            } else {
                return $result;
            }
        }

        /**
         *	This function will return the full URL to the current request, including hostname, port and request URI.
         *
         *  @param  $no_qs  (optional) If set to false, the query string will be omitted. Default is false.
         *  @param  $use_ip (optional) If set to true, the IP address of the server will be used instead of the
         *                  hostname. Default is false.
         *
         *	@returns	The full url of the current request.
         */
        function getCurrentUrl( $no_qs=false, $use_ip=false ) {
            if ( $use_ip ) {
                $url = 'http://' . strtolower( $_SERVER['SERVER_ADDR'] );
            } else {
                $url = 'http://' . strtolower( $_SERVER['SERVER_NAME'] );
            }
            if ( $_SERVER['SERVER_PORT'] != '80' ) {
                $url = $url . ':' . $_SERVER['SERVER_PORT'];
            }
            if ( $no_qs ) {
                $url = $url . YD_SELF_SCRIPT;
            } else {
                $url = $url . $_SERVER['REQUEST_URI'];
            }
            return $url;
        }

        /**
         *  This function returns the normalized full URL
         *
         *  @returns    The normalized full URL of the current request.
         */
        function getNormalizedCurrentUrl() {
            $url = 'http://' . strtolower( $_SERVER['SERVER_NAME'] );
            if ( $_SERVER['SERVER_PORT'] != '80' ) {
                $url = $url . ':' . $_SERVER['SERVER_PORT'];
            }
            return $url . YDRequest::getNormalizedUri();
        }

        /**
         *	This function will return a normalized URI. This is the URI without the debug information and will all keys
         *	sorted alphabetically.
         *
         *	@returns	Normalized request URI.
         */
        function getNormalizedUri() {
            $url = parse_url( YDRequest::getCurrentUrl() );
            $params = $_GET;
            ksort( $params );
            if ( isset( $params['YD_DEBUG'] ) ) {
                unset( $params['YD_DEBUG'] );
            }
            $query = array();
            foreach( $params as $key=>$val ) {
                if ( $key != 'PHPSESSID' ) {
                    array_push( $query, urlencode( $key ) . '=' . urlencode( $val ) );
                }
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
         *
         *  @returns 	Returns an array containing results returned from action and callback processing.
         *				Elements of this array are keyed by strings:
         *					action:before
         *					&lt;actionname&gt;:before
         *					&lt;actionname&gt;
         *					&lt;actionname&gt;:after
         *					action:after
         */
        function forward( $action ) {
            if ( strpos( $action, 'action' ) === 0 ) {
                $action = substr( $action, strlen( 'action' ) );
            }
            $_GET[ 'do' ] = $action;
            if ( ! $this->isActionAllowed() ) {
                $this->errorActionNotAllowed();
            } else {
                // process the requested action (second parameter disables callback processing)
                return $this->_processAction( $action, false );
            }
        }

        /**
         *	This function will do a HTTP redirect to the specified action.
         *
         *	@param $action	(optional) Name of the action to redirect to. If no action is specified, it will redirect to
         *					the default action.
         *	@param $params	(optional) The $_GET parameters as an associative array that need to be added to the URL
         *					before redirecting. They will automatically get URL encoded.
         */
        function redirectToAction( $action='default', $params=array() ) {
            if ( is_null( $action ) ) {
                $action = 'default';
            }
            if ( strpos( $action, 'action' ) === 0 ) {
                $action = substr( $action, strlen( 'action' ) );
            }
            $url = YD_SELF_SCRIPT . '?do=' . $action;
            foreach ( $params as $key=>$val ) {
                $url .= '&' . urlencode( $key ) . '=' . urlencode( $val );
            }
            $this->redirect( $url );
        }

        /**
         *	This function will redirect the current request to a new URL. This does an HTTP redirect. If you want to
         *	forward to a different action in the same request, please use the forward function instead.
         *
         *	@param $url	The URL to redirect to.
         */
        function redirect( $url ) {
            if ( strpos( strtoupper( $_SERVER['SERVER_SOFTWARE'] ), 'IIS' ) ) {
               header( 'Refresh: 0; URL=' . $url );
            } else {
               header( 'Location: '. $url );
            }
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
         *  This function processes the specified action.
         *
         *  @param  $action  			The action name.
         *  @param  $enable_callbacks	Default is true. Use this to disable callback processing.
         *
         *  @returns 	Returns an array containing results returned from action and callback processing.
         *				Elements of this array are keyed by strings:
         *					action:before
         *					&lt;actionname&gt;:before
         *					&lt;actionname&gt;
         *					&lt;actionname&gt;:after
         *					action:after
         *
         *  @internal
         */
        function _processAction( $action, $enable_callbacks = true ) {
            if ( ! YDStringUtil::startsWith( $action, 'action' ) ) {
                $action = 'action' . $action;
            }
            $results = array();
            if ( $enable_callbacks == true ) {
                $results['action:before'] = $this->_executeCallbacks( 'action' , true );
                $results[$action . ':before'] = $this->_executeCallbacks( $action, true );
            }
            $results[$action] = call_user_func( array( $this, $action ) );
            if ( $enable_callbacks == true ) {
                $results[$action . ':after'] = $this->_executeCallbacks( $action, false );
                $results['action:after'] = $this->_executeCallbacks( 'action', false );
            }
            return $results;
        }

        /**
         *	This function is the intelligent part of the YDRequest class. Based on the action specified in the URL, it
         *	will execute the right function of this class. It performs quite a lot of error checking to see whether the
         *	action function is specified or not.
         *
         *  @returns 	Returns an array containing results returned from action and callback processing.
         *				Elements of this array are keyed by strings:
         *					action:before
         *					&lt;actionname&gt;:before
         *					&lt;actionname&gt;
         *					&lt;actionname&gt;:after
         *					action:after
         */
        function process() {
            $action = 'action' . $this->getActionName();
            return $this->_processAction( $action, true );
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
            echo( YD_CRLF );
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
         *	authentication if any authentication was needed. If this check fails, the code in the errorActionNotAllowed
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
        function errorActionNotAllowed() {
            trigger_error( 'You are not allow to access the action "' . $this->getActionName() . '"', YD_ERROR );
        }

        /**
         *  This function registers an action callback method.
         *
         *  'before' and 'after' callbacks can be registered for
         *  'action' (all actions) or for a specific YDRequest action.
         *
         *  By default no YDRequest action callbacks are defined.
         *
         *  Callbacks are disabled when an action forwards to another
         *  action. This is to prevent duplicated processing of callbacks.
         *  "redirectToAction" is a more compatible with callbacks.
         *
         *  Processing of callbacks and actions in the context of a request
         *  is executed in this order:
         *
         *    1. (optional) before 'action' callbacks - apply to all actions
         *    2. (optional) before callback for the current action
         *	  3. Current action
         *    4. (optional) after callback for the current action
         *    5. (optional) after 'action' callbacks - apply to all actions
         *
         *	example 1: will execute my_before_callback() before each action
         *		$this->registerCallback( 'my_before_callback', 'action', true );
         *
         *	example 2: will execute my_after_callback() after each action
         *		$this->registerCallback( 'my_after_callback', 'action', false );
         *
         *	example 3: will execute my_deafult_after_callback() after actionDefault
         *		$this->registerCallback( 'my_deafult_after_callback', 'actionDefault', false );
         *
         *  @param $method  The method name.
         *  @param $action  The action or array of actions that trigger the call.
         *  @param $before  (optional) Execute callback before the action. Default: false.
         */
        function registerCallback( $method, $action, $before=false ) {
            if ( strtolower( $action ) != 'action' && ! $this->_callbacks->exists( strtolower( $action ) ) ) {
                $this->_callbacks->set( strtolower( $action ),   array( 'before' => array(), 'after' => array() ) );
            }
            if ( strtolower( $action ) != 'action' && ! $this->hasMethod( $action ) ) {
                trigger_error(  $this->getClassName() . ' - ' .
                                    ' action "' . $action . '" for callback "' . $method . '" does not exist.', YD_ERROR );
            }
            $sub = $before ? 'before' : 'after';
            if ( ! is_array( $action ) ) {
                $action = array( $action );
            }
            foreach ( $action as $ac ) {
                if ( ! $this->_callbacks->exists( strtolower( $ac ) ) ) {
                    trigger_error(  $this->getClassName() . ' - ' . $ac .
                                    ' Incorrect action for callback "' . $method . '".', YD_ERROR );
                }
                $aclower = strtolower( $ac );
                $act = & $this->_callbacks->$aclower;
                $act[ $sub ][ $method ] = '';
            }
        }

        /**
         *  This function unregisters a callback method.
         *
         *  @param $method  The method name.
         *  @param $action  (optional) The action that triggers the call. If null, from all actions. Default: null.
         */
        function unregisterCallback( $method, $action=null ) {
            if ( ! is_null( $action ) && ! $this->_callbacks->exists( strtolower( $action ) ) ) {
                trigger_error(  $this->getClassName() . ' -
                                Incorrect action for unregistering callback method "' . $method . '".', YD_ERROR );
            }
            $actions = array( $action );
            if ( is_null( $action ) ) {
                $actions = array_keys( get_object_vars( $this->_callbacks ) );
            }
            foreach ( $actions as $action ) {
                $ac = strtolower ( $action );
                $act = & $this->_callbacks->$ac;
                unset( $act[ 'before' ][ $method ] );
                unset( $act[ 'after' ][ $method ] );
            }
        }

        /**
         *  This function executes all callbacks defined for the action.
         *
         *  @param  $action  The action name.
         *  @param  $before  (optional) Execute the before actions. Default: false.
         *  @param  $success (optional) Boolean indicating if the action was successful. Default: null - no result.
         *
         *  @internal
         */
        function _executeCallbacks( $action, $before=false, $success=null ) {
            $sub = $before ? 'before' : 'after';
            $actions = & $this->_callbacks->$action;
            if ( $actions[$sub] != null ) {
                foreach ( $actions[ $sub ] as $callback => $n ) {
                    if ( ! $this->hasMethod( $callback ) ) {
                        trigger_error(  $this->getClassName() . ' - The ' .
                                        $action . ' callback method "' . $callback . '" is not defined.', YD_ERROR );
                    }
                    $res = call_user_func( array( & $this, $callback ), $action, $before, $success );
                    if ( $res !== true && $res !== null ) {
                        return array(
                            'class'    => $this->getClassName(),
                            'callback' => $callback,
                            'action'   => $action,
                            'before'   => $before,
                            'success'  => $success,
                            'return'   => $res
                        );
                    }
                }
            }
            return true;
        }

        /**
         *  This function gets the value of a specific cookie.
         *
         *  @param  $name       The name of the cookie.
         *  @param  $default    (optional) The default value if the cookie is null or not set. Defaults to ''.
         *  @param  $values     (optional) The allowed values as an array.
         *
         *  @returns    The value of the cookie or the default if the cookie is not set or not in values.
         */
        function getCookie( $name, $default='', $values=null ) {
            if ( isset( $_COOKIE[$name] ) && ! empty( $_COOKIE[$name] ) ) {
                if ( ! is_null( $values ) ) {
                    if ( in_array( $_COOKIE[$name], $values ) ) {
                        return $_COOKIE[$name];
                    } else {
                        return $default;
                    }
                } else {
                    return $_COOKIE[$name];
                }
            } else {
                return $default;
            }
        }

        /**
         *  This function sets the value for a specific cookie
         *
         *  @param  $name       The name of the cookie to set.
         *  @param  $value      The value to set for the cookie.
         *  @param  $sessionOnly    (optional) If set to true, the cookie is only set for the current session,
         *                          otherwise, the default cookie lifetime is used.
         */
        function setCookie( $name, $value, $sessionOnly=false ) {
            $lifeTime = ( $sessionOnly ) ? 0 : $this->cookieLifeTime;
            setcookie( $name, $value, $lifeTime, $this->cookiePath );
        }

        /**
         *  This function deletes a cookie value.
         *
         *  @param  $name       The name of the cookie to delete.
         */
        function deleteCookie( $name ) {
            setcookie( $name, null, time() - 31536000, $this->cookiePath );
            unset( $_COOKIE[$name] );
        }


    }

?>
