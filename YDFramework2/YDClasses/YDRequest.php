<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die(  'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    require_once( 'YDBase.php' );
    require_once( 'YDTemplate.php' );
    require_once( 'YDObjectUtil.php' );

    /**
     *  This class implements a request. The request is the base class you can
     *  use to base your scripts on. The request class is the one taking care of
     *  the action dispatching when a script is executed. Also the
     *  authentication framework is implemented in this class.
     *
     *  The request class works as follows. When you request the url
     *  "http://localhost/sample1.php", the YDF2_Process include file will try
     *  to find a class called "sample1Request" which is derived from the
     *  YDRequest class. If this class is found, it will be instantiated.
     *
     *  Each request has a variable called "__requiresAuthentication" which
     *  indicates if this request needs authentication or not. If this is set to
     *  true, the "isAuthenticated" function will be executed. Based on the
     *  result, either "authenticationSucceeded" or "authenticationFailed" will
     *  be called.
     *
     *  Then, it will check if this class has a method called process. If so, it
     *  will be called, and this is where the action dispatching happens. This
     *  function will check the value of the "do" parameter specified in the URL
     *  to find out which action function it needs to call. If the do parameter
     *  specified "edit" as the action, the actionEdit function will be called.
     */
    class YDRequest extends YDBase {

        /**
         *  This is the class constructor for the YDRequest class. This sets the
         *  default action to 'actionDefault' (but can be overridden later on),
         *  and also initializes an YDTemplate object.
         *
         *  @remark
         *      By default, classes are set to no require authentication.
         */
        function YDRequest() {

            // Initialize YDBase
            $this->YDBase();

            // Set up the name of the default action
            $this->defaultAction = YD_ACTION_DEFAULT;

            // Create the template object
            $this->template = new YDTemplate();

            // Mark that the request is initialized
            $this->__isInitialized = true;

            // Variable that indicates if we need authentication
            $this->__requiresAuthentication = false;

        }

        /**
         *  This function defines the default action. This is the full name
         *  of the function that needs to be executed as the default action.
         *
         *  @param $action The name of the function that needs to be executed as
         *                 the default action.
         */
        function setDefaultAction( $action ) {
            $this->defaultAction = $action;
        }

        /**
         *  This function returns the full name the default action. This is the
         *  full name of the function that will be executed as the default
         *  action.
         *
         *  @returns The name of the function that will to be executed as the
         *           default action.
         */
        function getDefaultAction() {
            if ( empty( $this->defaultAction ) ) {
                return YD_ACTION_DEFAULT;
            } else {
                return $this->defaultAction;
            }
        }

        /**
         *  This function returns the name of the action that was specified by
         *  the do parameter specified in the URL. This does NOT include the
         *  action prefix.
         *
         *  @returns The name of the current action.
         */
        function getActionName() {

            // Check if an action parameter was given
            if ( empty( $_GET[ YD_ACTION_PARAM ] ) ) {
                $action = $this->getDefaultAction();
            } else {
                $action = $_GET[ YD_ACTION_PARAM ];
            }

            // Convert the action name to lowercase
            $action = strtolower( $action );

            // Remove the action prefix
            if ( strpos( $action, 'action' ) === 0 ) {
                $action = substr( $action, strlen( 'action' ) );
            }

            // Return the action name
            return strtolower( $action );

        }

        /**
         *  This function will return the full URL to the current request,
         *  including hostname, port and request URI.
         *
         *  @returns The full url of the current request.
         */
        function getCurrentUrl() {

            // Get the server name
            $url = 'http://' . $_SERVER['SERVER_NAME'];

            // Add the port if different than port 80
            if ( $_SERVER['SERVER_PORT'] != '80' ) {
                $url = $url . ':' . $_SERVER['SERVER_PORT'];
            }

            // Add the request URL
            $url = $url . $_SERVER['REQUEST_URI'];

            // Return the URL
            return $url;

        }

        /**
         *  This function will forward the current request to a different
         *  action. By using this function, you make sure the action names and
         *  so on are also update to reflect this.
         *
         *  @remark
         *      This function does a HTTP redirect in the background.
         *
         *  @param $action Name of the action to forward to.
         */
        function forward( $action ) {

            // Remove the action prefix
            if ( strpos( $action, 'action' ) === 0 ) {
                $action = substr( $action, strlen( 'action' ) );
            }

            // Construct the new URL
            $url = YD_SELF_SCRIPT . '?' . YD_ACTION_PARAM . '=' . $action;

            // Do a redirect to the right url
            $this->redirect( $url );

        }

        /**
         *  This function will redirect the current request to a new URL. This
         *  does an HTTP redirect. If you want to forward to a different action
         *  in the same request, please use the forward function instead.
         *
         *  @remark
         *      If you execute this function, it will do the redirect right away
         *      and will stop the processing of the current request.
         *
         *  @param $url The URL to redirect to.
         */
        function redirect( $url ) {
            header( 'Location: '. $url );
            die();
        }

        /**
         *  This is a shortcut for the $this->template->setVar function.
         *
         *  @param $name Name you want to use for this variable for referencing
         *               it in the template.
         *  @param $value The value of the variable you want to add.
         */
        function setVar( $name, $value ) {
            $this->template->setVar( $name, $value );
        }

        /**
         *  This function takes an associative array as it's argument, and
         *  assigns each individual key and value to the template.
         *
         *  Here's a code example of how it works:
         *
         *  @code
         *  // The associative array with the variables
         *  $vars = array(
         *      'name1' => 'value1', 'name2' => 'value2', 'name3' => 'value4'
         *  )
         *
         *  // Add them to the template
         *  $this->setVarsFromArray( $vars );
         *  @endcode
         *
         *  The code above has the same result as the following code:
         *
         *  @code
         *  // Add each variable to the template
         *  $this->setVar( 'name1', 'value1' );
         *  $this->setVar( 'name2', 'value2' );
         *  $this->setVar( 'name3', 'value3' );
         *  @endcode
         *
         *  This is a very handy function for single records that are returned
         *  from a database query. Database queries in the Yellow Duck Framework
         *  always return associative arrays.
         *
         *  @param $array An associative array.
         */
        function setVarsFromArray( $array ) {
            foreach( $array as $key=>$val ) {
                $this->setVar( $key, $val );
            }
        }

        /**
         *  This function will add a YDForm object to the template. It will
         *  automatically convert the form to an array using the template object
         *  so that you don't have to do it manually.
         *
         *  If the form specified when calling this function is not a class
         *  derived from the YDForm class, a fatal error will be thrown.
         *
         *  @param $name Name you want to use for this form for referencing
         *               it in the template.
         *  @param $form The form you want to add.
         */
        function addForm( $name, $form ) {

            // Check if the object is a form
            if ( ! YDObjectUtil::isSubclass( $form, 'YDForm' ) ) {
                YDFatalError(
                    'The form you have tried to add to the form is not a '
                    . 'subclass of the YDForm class.'
                );
            }

            // Add the form to the template
            $this->setVar( $name, $form->toArray( $this->template ) );

        }

        /**
         *  This function will get the contents of the processed template.
         *  Optionally, the name of the template can be specified. If no
         *  template is specified, the basename of the current script will be
         *  used as the template name.
         *
         *  @param $templateName (optional) Name of the template to use.
         *
         *  @return Returns the contents of the parsed template.
         */
        function getTemplateOutput( $templateName='' ) {

            // Add the name of the current action
            $this->setVar( 'YD_ACTION', $this->getActionName() );

            // Get the name of the template
            if ( empty( $templateName ) ) {
                $templateName = basename( YD_SELF_FILE, YD_SCR_EXT );
            }

            // Return the parsed template
            return $this->template->getOutput( $templateName );

        }

        /**
         *  This function will output the contents of the processed template.
         *  Optionally, the name of the template can be specified. If no
         *  template is specified, the basename of the current script will be
         *  used as the template name.
         *
         *  This function uses the getTemplateOuput function to parse and get
         *  the contents of the template. Once that is done, it will send the
         *  output straight to the browser.
         *
         *  @param $templateName Name of the template to use.
         */
        function outputTemplate( $templateName='' ) {
            echo( $this->getTemplateOutput( $templateName ) );
        }

        /**
         *  This function will check if the request was initialized properly.
         *  Please, do NOT override this function as the Yellow Duck Framework
         *  will not work properly anymore if you do so.
         *
         *  @return Returns a boolean indicating if the request was initialized
         *          properly or not.
         */
        function isInitialized() {
            if ( $this->__isInitialized == true ) {
                return true;
            } else {
                return false;
            };
        }

        /**
         *  This function is the intelligent part of the YDRequest class. Based
         *  on the action specified in the URL, it will execute the right
         *  function of this class.
         *
         *  It performs quite a lot of error checking to see whether the action
         *  function is specified or not.
         */
        function process() {

            // Check if an action parameter was given
            if ( empty( $_GET[ YD_ACTION_PARAM ] ) ) {
                $action = $this->getDefaultAction();
            } else {
                $action = 'action' . $_GET[ YD_ACTION_PARAM ];
            }

            // Check if the action exists
            if ( ! method_exists( $this, $action ) ) {
                $this->errorMissingAction( $action );
            }

            // We now have the action and the class, so execute
            call_user_func( array( $this, $action ) );

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
            YDFatalError(
                'Class ' . get_class( $this ) . ' does not contain an action '
                . 'called "' . strtolower( $action ) . '" (function name).'
            );
        }

        /**
         *  This is the placeholder for the default function. You need to
         *  override this function in the classes you derive from the YDRequest
         *  class. This function takes no arguments.
         */
        function actionDefault() {
            echo( 'action: actionDefault' );
        }

        /**
         *  This function indicates that this request needs authentication. If
         *  you change this to true, it will cause the Yellow Duck Framework to
         *  check the isAuthenticated function to see if you are authenticated
         *  or not.
         *
         *  @param $bool Boolean indicating if this request requires
         *               authentication or not.
         */
        function setRequiresAuthentication( $bool ) {
            if ( $bool == true ) {
                $this->__requiresAuthentication = true;
            } else {
                $this->__requiresAuthentication = false;
            }
        }

        /**
         *  This function will return true or false to indicate whether this
         *  class needs authentication or not.
         *
         *  @returns This function returns true or false to indicate if this
         *           requires authentication or not.
         */
        function getRequiresAuthentication() {
            if ( $this->__requiresAuthentication == false ) {
                return false;
            } else {
                return true;
            }
        }

        /**
         *  This function performs the actual authentication and returns either
         *  true or false. By default, this function is not implemented and
         *  simply returns true.
         *
         *  You will need to override this function in your own class to define
         *  how you want the authentication to happen. You can either do this
         *  based on the contents of session variables, but it can also be based
         *  on e.g. the IP number of the client.
         *
         *  @returns Boolean indicating is the client is authenticated or not.
         */
        function isAuthenticated() {
            return true;
        }

        /**
         *  If the authentication was succesful, this function is execute just
         *  before the actual processing of the request. You will need to
         *  override this function in the classes that implement the YDRequest
         *  class.
         */
        function authenticationSucceeded() {
        }

        /**
         *  If the authentication was unsuccesful, this function is execute just
         *  before the actual processing of the request. You will need to
         *  override this function in the classes that implement the YDRequest
         *  class.
         */
        function authenticationFailed() {
            YDFatalError( 'Authentication failed.' );
        }

        /**
         *  You can use this function to check wether the current request is
         *  allowed to access the specified action or not. This check is
         *  performed just before the actual processing of the request and just
         *  after the authentication if any authentication was needed.
         *
         *  If this check fails, the code in the actionNotAllowed method is
         *  performed.
         *
         *  You will need to override this function in your own class to define
         *  how you want the actions to be checked.
         *
         *  @returns Boolean indicating if the current request is allowed to
         *           perform the specified action or not.
         */
        function isActionAllowed() {
            return true;
        }

        /**
         *  If the current request is not allowed to execute the specified
         *  action, the code in this function gets executed. You will need to
         *  override this function in the classes that implement the YDRequest
         *  class.
         */
        function actionNotAllowed() {
            YDFatalError(
                'You are not allow to access the action "'
                . $this->getActionName() . '"'
            );
        }

    }

?>
