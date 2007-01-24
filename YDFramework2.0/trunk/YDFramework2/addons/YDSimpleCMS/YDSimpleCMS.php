<?php

    /*

        Yellow Duck Framework version 2.1
        (c) Copyright 2002-2007 Pieter Claerhout

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

    /**
     *  @todo
     *      Add multiple levels of templates so that you can have default templates which can be overridden (ala cakephp)
     *
     *  @todo
     *      Add configuration option to define the error handling function. By default, it should just render an _error
     *      template with the debug stacktrace and so on. By overridding the template, you can customize it.
     *
     *  @todo
     *      Add options to supply multiple templates for each object type (e.g. page.tpl, page_homepage.tpl, ...)
     *
     *  @addtogroup YDSimpleCMS Addons - Simple CMS
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Constants
    define( 'YD_SIMPLECMS_PACKAGE_NAME',   'YD_SIMPLECMS' );
    define( YD_SIMPLECMS_PACKAGE_NAME . '_ACTION_PREFIX',  'action_' );
    define( YD_SIMPLECMS_PACKAGE_NAME . '_MODULE_PREFIX',  'module_' );
    define( YD_SIMPLECMS_PACKAGE_NAME . '_MODULE_EXT',     '.php' );
    define( YD_SIMPLECMS_PACKAGE_NAME . '_MODULE_PATTERN', YD_SIMPLECMS_MODULE_PREFIX . '*' . YD_SIMPLECMS_MODULE_EXT );
    define( YD_SIMPLECMS_PACKAGE_NAME . '_MODULE_DIR',     dirname( YD_SELF_FILE ) . '/includes/modules' );
    define( YD_SIMPLECMS_PACKAGE_NAME . '_SKINS_DIR',      dirname( YD_SELF_FILE ) . '/includes/skins_' );
    define( YD_SIMPLECMS_PACKAGE_NAME . '_SCOPE_PUBLIC',   'public' );
    define( YD_SIMPLECMS_PACKAGE_NAME . '_SCOPE_ADMIN',    'admin' );

    // Configuration
    YDConfig::set( 'YD_AUTO_EXECUTE', false );

    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDForm.php' );
    include_once( YD_DIR_HOME_CLS . '/YDRequest.php' );
    include_once( YD_DIR_HOME_CLS . '/YDDatabase.php' );
    include_once( YD_DIR_HOME_CLS . '/YDTemplate.php' );
    include_once( YD_DIR_HOME_CLS . '/YDFileSystem.php' );
    include_once( YD_DIR_HOME . '/YDF2_process.php' );
    include_once( dirname( __FILE__ ) . '/YDSimpleCMS_CoreModules.php' );

    /**
     *  This is the global YDSimpleCMS class that houses all cms related functions.
     *
     *  @todo
     *      Add the following functions:
     *          - getLanguages(): returns $GLOBALS['YD_SIMPLECMS']['languages'], but issues initialize first if not done
     *            yet (to make sure we are initialized properly).
     *      \n
     *      We could do all this in the request class, but that would mean the modules will not have access to it. This
     *      way, we kind of make a pseudo singleton class that can be accessed from anywhere in the code.
     *
     *  @ingroup YDSimpleCMS
     */
    class YDSimpleCMS {

        /**
         *  This function initializes the SimpleCMS package. It performs the following items:
         *      - Create a new global array under $GLOBALS['YD_SIMPLECMS']
         *      - Adds a database connection under $GLOBALS['YD_SIMPLECMS']['db']
         *      - Adds a language array under $GLOBALS['YD_SIMPLECMS']['languages']
         *      - Adds a module manager instance under $GLOBALS['YD_SIMPLECMS']['moduleManager']
         *      - Adds a array under $GLOBALS['YD_SIMPLECMS']['adminMenu'] to keep track of the admin menu
         *      - Adds a null value under $GLOBALS['YD_SIMPLECMS']['user'] to keep track of the current user
         *      - Stores the current scope in $GLOBALS['YD_SIMPLECMS']['scope']
         *
         *  Even if you call this function multiple times, the initialization will only be done once.
         *
         *  @todo
         *      Negotiate the language and put it as an array under $GLOBALS['YD_SIMPLECMS']['languages']
         *
         *  @static
         */
        function initialize() {

            // Check if the initialization is already done or not
            if ( ! isset( $GLOBALS[YD_SIMPLECMS_PACKAGE_NAME] ) || ! is_array( $GLOBALS[YD_SIMPLECMS_PACKAGE_NAME] ) ) {

                // Create the array
                $GLOBALS[YD_SIMPLECMS_PACKAGE_NAME] = array();

                // Setup the database connection
                YDConfig::set( 'YD_DB_TABLEPREFIX', YDConfig::get( 'db_prefix' ) );
                $GLOBALS[YD_SIMPLECMS_PACKAGE_NAME]['db'] = YDDatabase::getInstance(
                    'mysql',
                    YDConfig::get( 'db_name' ), YDConfig::get( 'db_user' ),
                    YDConfig::get( 'db_pass' ), YDConfig::get( 'db_host' )
                );

                // The global list with admin menu
                $GLOBALS[YD_SIMPLECMS_PACKAGE_NAME]['adminMenu'] = array();

                // The current user
                $GLOBALS[YD_SIMPLECMS_PACKAGE_NAME]['currentUser'] = null;

                // The module manager instance
                $GLOBALS[YD_SIMPLECMS_PACKAGE_NAME]['moduleManager'] = new YDSimpleCMSModuleManager();
                $GLOBALS[YD_SIMPLECMS_PACKAGE_NAME]['moduleManager']->loadAllModules();

                // Set the default scope
                $GLOBALS[YD_SIMPLECMS_PACKAGE_NAME]['scope'] = YD_SIMPLECMS_SCOPE_PUBLIC;

            }

        }

        /**
         *  This is a static function that will run a request.
         *
         *  @param  $scope  The scope in which the request needs to run. You can choose between
         *                  YD_SIMPLECMS_SCOPE_PUBLIC and YD_SIMPLECMS_SCOPE_ADMIN. If you choose
         *                  YD_SIMPLECMS_SCOPE_ADMIN, authentication will be enforced, with YD_SIMPLECMS_SCOPE_PUBLIC,
         *                  it's not.
         *
         *  @static
         */
        function run( $scope ) {
            YDSimpleCMS::setVar( 'scope', $scope );
            $clsInst = new YDExecutor( 'YDSimpleCMSRequest.php' );
            @session_start();
            $clsInst->execute();
        }

        /**
         *  This is a static function that will run a public request.
         *
         *  @static
         */
        function runPublicRequest() {
            YDSimpleCMS::run( YD_SIMPLECMS_SCOPE_PUBLIC );
        }

        /**
         *  This is a static function that will run an admin request.
         *
         *  @static
         */
        function runAdminRequest() {
            YDSimpleCMS::run( YD_SIMPLECMS_SCOPE_ADMIN );
        }

        /**
         *  This function will show an error for the CMS application. The parameters that are supported are the same
         *  ones as the sprintf and printf functions.
         *
         *  @static
         */
        function showError() {
            $args = func_get_args();
            if ( sizeof( $args > 0 ) ) {
                $args[0] = '<font color="red"><b>ERROR:</b> ' . $args[0] . '</font>';
            }
            call_user_func_array( 'printf', $args );
            die();
        }

        /**
         *  This function will add an entry to the administration menu.
         *
         *  @param  $title      The title of the menu in which this needs to appear.
         *  @param  $subtitle   The title of the submenu item that needs to be added. If you use a null value for this
         *                      parameter, only the main menu item is created.
         *  @param  $module     The module that is linked to this menu item.
         *  @param  $action     The module action that is linked to this menu item.
         *
         *  @static
         */
        function addAdminMenu( $title, $subtitle, $module, $action ) {

            // Link to the menu items
            $menu = & YDSimpleCMS::getAdminMenu();

            // Construct the URL
            $url = YD_SELF_SCRIPT . '?module=' . $module . '&action=' . $action;

            // Add the first level if needed
            if ( ! isset( $menu[$title] ) ) {
                $menu[$title] = array();
                $menu[$title]['title']    = $title;
                $menu[$title]['children'] = array();
            }

            // Check if we have a subname or not
            if ( $subtitle == null ) {
                $menu[$title]['url'] = $url;
            } else {
                if ( ! isset( $menu[$title][ $action ] ) ) {
                    $menu[$title]['children'][$subtitle] = array();
                    $menu[$title]['children'][$subtitle]['title'] = $subtitle;
                    $menu[$title]['children'][$subtitle]['url']   = $url;
                }
            }

        }

        /**
         *  This function returns a database connection.
         *
         *  @returns    A database connection.
         *
         *  @static
         */
        function & getDb() {
            return YDSimpleCMS::getVar( 'db' );
        }

        /**
         *  This function returns the array with the menu items for the admin menu.
         *
         *  @returns    The array with the admin menu.
         *
         *  @static
         */
        function & getAdminMenu() {
            $adminMenu = & YDSimpleCMS::getVar( 'adminMenu' );
            ksort( $adminMenu );
            foreach ( $adminMenu as $key=>$val ) {
                if ( isset( $adminMenu[$key]['children'] ) ) {
                    ksort( $adminMenu[$key]['children'] );
                }
            }
            return $adminMenu;
        }

        /**
         *  This function returns an instance of the module manager class.
         *
         *  @returns    An instance of the module manager class.
         *
         *  @static
         */
        function & getModuleManager() {
            return YDSimpleCMS::getVar( 'moduleManager' );
        }

        /**
         *  This function returns the current scope in which the request runs.
         *
         *  @returns    Returns YD_SIMPLECMS_SCOPE_PUBLIC or YD_SIMPLECMS_SCOPE_ADMIN.
         *
         *  @static
         */
        function & getScope() {
            return YDSimpleCMS::getVar( 'scope' );
        }

        /**
         *  This function returns the details of the current user which is logged in. If no one is logged in, it will
         *  return a null value.
         *
         *  @returns    Returns the details of the currently logged in user.
         *
         *  @static
         */
        function & getCurrentUser() {
            return YDSimpleCMS::getVar( 'currentUser' );
        }

        /**
         *  This function returns one of the named variables from the global CMS scope.
         *
         *  @param  $var    The name of the variable you want to retrieve.
         *
         *  @returns    The contents of that variable, false if the variable doesn't exist.
         *
         *  @static
         */
        function & getVar( $var ) {

            // Initialize the SimpleCMS package
            YDSimpleCMS::initialize();

            // Return the variable if it's set, otherwise return false
            if ( ! isset( $GLOBALS[YD_SIMPLECMS_PACKAGE_NAME][$var] ) ) {
                $var = false;
                return $var;
            }

            // Return a reference to the variable
            return $GLOBALS[YD_SIMPLECMS_PACKAGE_NAME][$var];

        }

        /**
         *  This function sets a YDSimpleCMS variable.
         *
         *  @param  $var    The variable to set.
         *  @param  $value  The value to set the variable to.
         *
         *  @static
         */
        function setVar( $var, $value ) {
            $GLOBALS[YD_SIMPLECMS_PACKAGE_NAME][$var] = $value;
        }

    }

    /**
     *  Custom template class for the SimpleCMS package. This is a customization of the stock YDTemplate class.
     *
     *  The main differences with the normal template class are:
     *      - Support for masterpages
     *      - Different handling of template directories
     *
     *  The template directories are determined as follows (depending of the scope of the template class:
     *      - scope public:
     *          - skins_{scope}/{currentSkinFromConfig}
     *      - scope admin:
     *          - skins_{scope}
     *
     *  @ingroup YDSimpleCMS
     */
    class YDSimpleCMSTemplate extends YDTemplate {

        /**
         *  This function will fetch the specified template, and surround it with the contents of the indicated master
         *  page, which should be in the skins directory.
         *
         *  The placeholder you can use in the master page to place the contents is \#\#content\#\#
         *
         *  @param  $template   (optional) The name of the template to use. If empty, the name of the module will be
         *                      used as the name of the template.
         *  @param  $master     (optional) The name of the masterpage to use. Defaults to '__master'.
         *
         *  @returns    The result from the parsed template.
         */
        function fetchWithMaster( $template='', $master='__master' ) {

            // Get the current scope
            $scope = YDSimpleCMS::getScope();

            // Set the variables
            $this->template_dir = YD_SIMPLECMS_SKINS_DIR . $scope;
            if ( $scope == YD_SIMPLECMS_SCOPE_PUBLIC ) {
                $this->template_dir .= '/' . YDConfig::get( 'YD_SIMPLECMS_PUBLIC_SKIN', 'default' );
            }

            // Get the output
            $output = $this->fetch( $template );

            // Check if there is a __standard page
            if ( is_file( $this->template_dir . '/' . $master . '.tpl' ) ) {
                $standard = $this->fetch( $master );
                $output = str_replace( '##content##', $output, $standard );
            }

            // Return the output
            return $output;

        }

        /**
         *  This function will output the specified template, and surround it with the contents of the indicated master
         *  page, which should be in the skins directory.
         *
         *  The placeholder you can use in the master page to place the contents is \#\#content\#\#
         *
         *  @param  $template   (optional) The name of the template to use. If empty, the name of the module will be
         *                      used as the name of the template.
         *  @param  $master     (optional) The name of the masterpage to use. Defaults to '__master'.
         */
        function displayWithMaster( $template='', $master='__master' ) {
            echo( $this->fetchWithMaster( $template, $master ) );
        }

    }

    /**
     *  Define the SimpleCMS request class. This is the base classes for all requests in the framework.
     *
     *  Depending on the current scope, authetication will be enforced or not. It works as follows:
     *      - YD_SIMPLECMS_SCOPE_PUBLIC: authetication is performed but not enforced
     *      - YD_SIMPLECMS_SCOPE_ADMIN: authetication is performed and enforced
     *
     *  It also supports modules and action via specific query string parameters:
     *      - module: the module to load (defaults to 'admin' )
     *      - action: the action to load from the module (defaults to 'show')
     *
     *  The authentication credentials are stored in cookies with the following name:
     *      - YD_SIMPLECMS_{siteId}_USER
     *      - YD_SIMPLECMS_{siteId}_PASS
     *
     *  @ingroup YDSimpleCMS
     */
    class YDSimpleCMSRequest extends YDRequest {

        /**
         *  The constructor for the admin CMS request.
         *
         *  This one forces authentication and requires a configuration variable called 'YD_SIMPLECMS_SITEID' to be set.
         */
        function YDSimpleCMSRequest() {

            // Initialize the parent
            $this->YDRequest();

            // Setup the default module based on the current scope
            if ( YDSimpleCMS::getScope() == YD_SIMPLECMS_SCOPE_PUBLIC ) {
                $this->defaultModule = 'page';
            } else {
                $this->defaultModule = 'admin';
            }

            // Indicate we require login
            $this->setRequiresAuthentication( true );

            // Instantiate the template object
            $this->tpl = new YDSimpleCMSTemplate();

            // Get the site id
            $this->siteId = YDConfig::get( 'YD_SIMPLECMS_SITEID', 'SAMPLESITE' );

            // The names of the cookies
            $this->cookieNameUser = 'YD_SIMPLECMS_' . $this->siteId . '_USER';
            $this->cookieNamePass = 'YD_SIMPLECMS_' . $this->siteId . '_PASS';

        }

        /**
         *  The default action for the public request.
         *
         *  Currently, it does the following items:
         *      - Creates an instance of YDSimpleCMSModuleManager
         *      - Loads all the modules using the instance
         *      - Runs the module and action specified by the query string
         *
         *  @todo
         *      Needs to be implemented properly so that it can do all it's stuff based on the ID passed in the qeury
         *      string of the request.
         */
        function actionDefault() {
            $module = $this->getQueryStringParameter( 'module', $this->defaultModule );
            $action = $this->getQueryStringParameter( 'action', 'show' );
            $moduleManager = & YDSimpleCMS::getModuleManager();
            $moduleManager->runModule( $module, $action );
        }

        /**
         *  This action takes care of handling the login form. It can show and validate a login form and redirects to
         *  default action of the request class when the user is authenticated.
         */
        function actionLogin() {

            // Redirect to default action if already logged in
            if ( $this->isAuthenticated() === true ) {
                $this->forward( 'default' );
                return;
            }

            // Create the login form
            $form = new YDForm( 'loginForm' );
            $form->addElement( 'text', 'loginName', t('username') );
            $form->addElement( 'password', 'loginPass', t('password') );
            $form->addElement( 'checkbox', 'loginRememberMe', t('remember_me') );
            $form->addElement( 'submit', 'cmdSubmit', 'Login' );
            $form->setDefault( 'loginRememberMe', true );

            // Add the rules
            $form->addFormRule( array( & $this, 'checkLogin' ) );

            // Process the form
            if ( $form->validate() ) {

                // Get the form values
                $values = $form->getValues();

                // Update the password
                $values['loginPass'] = md5( $values['loginPass'] );

                // Set the cookies
                if ( $values['loginRememberMe'] === 1 ) {
                    $this->setCookie( $this->cookieNameUser, $values['loginName'], false );
                    $this->setCookie( $this->cookieNamePass, $values['loginPass'], false );
                } else {
                    $this->setCookie( $this->cookieNameUser, $values['loginName'], true );
                    $this->setCookie( $this->cookieNamePass, $values['loginPass'], true );
                }

                // Forward to the main manage page
                $this->redirectToAction();

            }

            // Add the form to the template
            $this->tpl->assignForm( 'form', $form );

            // Output the template
            $this->tpl->displayWithMaster( 'login' );

        }

        /**
         *  This action takes care of logging out the current user. This is done by removing the authentication cookie.
         *
         *  After the logout, the user is redirected to the login page.
         */
        function actionLogout() {
            $this->setCookie( $this->cookieNamePass, null );
            $this->redirectToAction( 'login' );
        }

        /**
         *  This function will check if the current user is authenticated or not. It will check this against the
         *  authentication cookies.
         *
         *  @returns    True if the user is authenticated, false if the user is not authenticated.
         */
        function isAuthenticated() {
            $cookieUser = $this->getCookie( $this->cookieNameUser );
            $cookiePass = $this->getCookie( $this->cookieNamePass );
            if ( ! empty( $cookieUser ) && ! empty( $cookiePass ) ) {
                $fields = array( 'loginName' => $cookieUser, 'loginPass' => $cookiePass );
                if ( $this->checkLogin( $fields, true ) === true ) {
                    $this->authenticationSucceeded();
                    return true;
                }
            }
            if ( YDSimpleCMS::getScope() == YD_SIMPLECMS_SCOPE_PUBLIC ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *  This function is executec if the authenticated failed. It redirects the user to the login page.
         */
        function authenticationFailed() {
            $this->forward( 'login' );
        }

        /**
         *  This function verifies the user credentials against the database.
         *
         *  @param  $fields     The fields which are containing the loginName and loginPass attributes.
         *  @param  $md5        (optional) If sett to false, it means that the password is passed as clear text. If set
         *                      to true, the password is passed to this function as an MD5 checksum.
         *
         *  @returns    If the login is valid, this function will return true. If not, it will return an array with the
         *              error message.
         *
         *  @todo
         *      Need model class to encapsulate the database calls.
         */
        function checkLogin( $fields, $md5=false ) {
            if ( $md5 === false ) {
                $fields['loginPass'] = md5( $fields['loginPass'] );
            }
            $db = YDSimpleCMS::getDb();
            $result = $db->getRecord(
                'SELECT * FROM #_users WHERE name = \'' . $db->escape( $fields['loginName'] ) . '\' AND password = \'' . $db->escape( $fields['loginPass'] ) . '\''
            );
            if ( $result === false ) {
                return array( '__ALL__' => t( 'err_login_all' ) );
            } else {
                YDSimpleCMS::setVar( 'currentUser', $result );
                return true;
            }
        }

    }

    /**
     *  Define a CMS module
     *
     *  @ingroup YDSimpleCMS
     */
    class YDSimpleCMSModule extends YDBase {

        /**
         *  The nice name of the CMS module.
         */
        var $name          = 'SimpleCMS Generic Module';

        /**
         *  The full description of the CMS module.
         */
        var $description   = 'Base module for all the SimpleCMS modules.';

        /**
         *  The version number of the CMS module.
         */
        var $version       = '1.0';

        /**
         *  The name of the author of the CMS module.
         */
        var $authorName    = 'Pieter Claerhout';

        /**
         *  The email address of the author of the CMS module.
         */
        var $authorEmail   = 'pieter@yellowduck.be';

        /**
         *  The website address of the author of the CMS module.
         */
        var $authorUrl     = 'http://www.yellowduck.be';

        /**
         *  The name of the action that is currently being executed.
         */
        var $currentAction = null;

        /**
         *  The class constructor the the YDSimpleCMSModule class.
         */
        function YDSimpleCMSModule() {
            $this->tpl = new YDSimpleCMSTemplate();
        }

        /**
         *  Placeholder function that gets called if the module is installed.
         *
         *  @todo
         *      Still needs to be implemented
         */
        function install() {
        }

        /**
         *  Placeholder function that gets called if the module is uninstalled.
         *
         *  @todo
         *      Still needs to be implemented
         */
        function uninstall() {
        }

        /**
         *  This function will indicate if the module is a required one or not. The required modules are basically not
         *  in the modules directory.
         *
         *  @returns    True if the module is required, false otherwise.
         */
        function isRequired() {
            return ! is_file( YD_SIMPLECMS_MODULE_DIR . '/' . $this->getClassName() . YD_SIMPLECMS_MODULE_EXT );
        }

        /**
         *  This function returns the short name of the module.
         *
         *  @returns    The short name of the module.
         */
        function getModuleName() {
            return substr( $this->getClassName(), strlen( YD_SIMPLECMS_MODULE_PREFIX ) );
        }

        /**
         *  This function will output the template associated with this module. It will also assign the following
         *  standard variables to the template instance:
         *      - currentAction: the name of the current action
         *      - currentScope: the current scope of the module (YD_SIMPLECMS_SCOPE_PUBLIC or YD_SIMPLECMS_SCOPE_ADMIN)
         *      - adminMenu: an array containing all the menus and menu items for the admin section.
         *      - currentUser: the full user details for the user which is currently logged in. If no user is logged in,
         *                     this variable will contain a null value.
         */
        function display( $name='' ) {
            $this->tpl->assign( 'currentAction', $this->currentAction );
            $this->tpl->assign( 'currentScope',  $this->currentScope );
            $this->tpl->assign( 'adminMenu',     YDSimpleCMS::getAdminMenu() );
            if ( YDSimpleCMS::getCurrentUser() ) {
                $this->tpl->assign( 'currentUser', YDSimpleCMS::getCurrentUser() );
            }
            $name = ( $name == '' ) ? $this->getModuleName() : $name;
            $this->tpl->displayWithMaster( $name );
        }

        /**
         *  This function triggers the indicated action in the indicated scope.
         *
         *  This function gets triggered by the module manager when this one wants to trigger an action.
         *
         *  @param  $action The action to run.
         */
        function runAction( $action ) {
            $scope = YDSimpleCMS::getScope();
            $moduleFunctionName = YD_SIMPLECMS_ACTION_PREFIX . $scope . '_' . $action;
            if ( ! $this->hasMethod( $moduleFunctionName ) ) {
                YDSimpleCMS::showError(
                    'Module "%s" does not have a function called "%s"',
                    $this->getModuleName(), $moduleFunctionName
                );
            }
            $this->currentScope  = $scope;
            $this->currentAction = $action;
            call_user_func( array( $this, $moduleFunctionName ) );
        }

        /**
         *  This function forces the user to logout.
         */
        function action_admin_logout() {
            YDRequest::redirect( YD_SELF_SCRIPT . '?do=logout' );
        }

        /**
         *  This function forces the user to logout.
         */
        function action_public_logout() {
            $this->action_admin_logout();
        }

    }

    /**
     *  The module manager class
     *
     *  @ingroup YDSimpleCMS
     */
    class YDSimpleCMSModuleManager extends YDBase {

        /**
         *  This function will load all modules from the modules directory. Loading a module only means that the file
         *  containing the module is included.
         *
         *  @static
         */
        function loadAllModules() {
            if ( is_dir( YD_SIMPLECMS_MODULE_DIR ) ) {
                $modulesDir = new YDFSDirectory( YD_SIMPLECMS_MODULE_DIR );
                foreach ( $modulesDir->getContents( YD_SIMPLECMS_MODULE_PATTERN ) as $module ) {
                    include_once( $module->getAbsolutePath() );
                }
            }
        }

        /**
         *  This function will run the specified module and action in the indicated scope.
         *
         *  @attention
         *      This function should only be called after the loadAllModules function has been executed. If not, this
         *      function will fail as the include files are not loaded yet.
         *
         *  @param  $module The name of the module to run.
         *  @param  $action The name of the action to run.
         */
        function runModule( $module, $action ) {

            // Convert everything to lowercase
            $scope  = YDSimpleCMS::getScope();
            $module = strtolower( $module );
            $action = strtolower( $action );

            // Get the classname for the module
            $moduleClassName = YD_SIMPLECMS_MODULE_PREFIX . $module;

            // Check if the class exists
            if ( ! class_exists( $moduleClassName ) ) {
                YDSimpleCMS::showError( 'Module class not found: %s', $moduleClassName );
            }

            // Create the class instance
            $moduleInstance = new $moduleClassName();

            // Set the correct template scope
            $moduleInstance->tpl->scope = $scope;

            // Sort the admin menu items
            if ( $scope == YD_SIMPLECMS_SCOPE_ADMIN ) {
                $adminMenu = & YDSimpleCMS::getAdminMenu();
                $moduleInstance->tpl->assign( 'adminMenu', $adminMenu );
            }

            // Create a link to ourselves
            $moduleInstance->manager = & $this;

            // Run the action
            $moduleInstance->runAction( $action );

        }

        /**
         *  This function returns a list with an instance of each loaded module.
         *
         *  @returns    A list with an instance of each loaded module.
         */
        function getModuleList() {
            $modules = array();
            foreach ( get_declared_classes() as $class ) {
                if ( YDStringUtil::startsWith( $class, YD_SIMPLECMS_MODULE_PREFIX ) ) {
                    $modules[$class] = new $class( $this );
                }
            }
            return $modules;
        }

    }

    // Initialize SimpleCMS
    YDSimpleCMS::initialize();

    // Add the default admin menu items
    YDSimpleCMS::addAdminMenu( 'Admin',   null,       'admin', 'show' );
    YDSimpleCMS::addAdminMenu( 'Admin',   'Logout',   'admin', 'logout' );
    YDSimpleCMS::addAdminMenu( 'Content', 'Pages',    'page',  'show' );
    YDSimpleCMS::addAdminMenu( 'Options', 'Modules',  'admin', 'modules' );
    YDSimpleCMS::addAdminMenu( 'Options', 'Settings', 'admin', 'settings' );

?>