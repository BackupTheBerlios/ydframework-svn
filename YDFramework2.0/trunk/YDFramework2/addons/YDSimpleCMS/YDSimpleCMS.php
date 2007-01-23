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

    /*! @page page_YDSimpleCMS Addons - Simple CMS

      The Simple CMS addon can be used to build a basic content management system.

      @section page_YDSimpleCMS_01 A generic overview

      Not written yet...

      @section page_YDSimpleCMS_02 The module approach

      Not written yet...
    */

    /**
     *  @addtogroup YDSimpleCMS Addons - Simple CMS
     *
     *  @ref page_YDSimpleCMS More information about the Simple CMS addon.
     *
     *  @todo
     *      Consolidate the two request classes into one. Based on the scope, it should know if you need to force
     *      authentication or not.
     *
     *  @todo
     *      Make a link from the module to the request instance
     *
     *  @todo
     *      Make a link from the template to the module instance and module manager (so that you can access these from
     *      the template)
     *
     *  @todo
     *      Make login module based?
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Constants
    define( 'YD_SIMPLECMS_ACTION_PREFIX',  'action_' );
    define( 'YD_SIMPLECMS_MODULE_PREFIX',  'module_' );
    define( 'YD_SIMPLECMS_MODULE_EXT',     '.php' );
    define( 'YD_SIMPLECMS_MODULE_PATTERN', YD_SIMPLECMS_MODULE_PREFIX . '*' . YD_SIMPLECMS_MODULE_EXT );
    define( 'YD_SIMPLECMS_MODULE_DIR',     dirname( YD_SELF_FILE ) . '/includes/modules' );
    define( 'YD_SIMPLECMS_SKINS_DIR',      dirname( YD_SELF_FILE ) . '/includes/skins_' );
    define( 'YD_SIMPLECMS_SCOPE_PUBLIC',   'public' );
    define( 'YD_SIMPLECMS_SCOPE_ADMIN',    'admin' );

    // Configuration
    YDConfig::set( 'YD_AUTO_EXECUTE', false );

    // The global list with admin menu
    $GLOBALS['YD_SIMPLECMS_ADMIN_MENU'] = array();

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
     *  @ingroup YDSimpleCMS
     */
    class YDSimpleCMS {

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
            $baseClass = ( strtolower( $scope ) == YD_SIMPLECMS_SCOPE_PUBLIC ) ? 'YDSimpleCMSPublicRequest' : 'YDSimpleCMSAdminRequest';
            $clsInst = new YDExecutor( $baseClass . '.php' );
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
         *  This function will show an error for the CMS application.
         *
         *  The parameters that are supported are the same ones as the sprintf and printf functions.
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
            $menu = & $GLOBALS['YD_SIMPLECMS_ADMIN_MENU'];

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
        function getDbConnection() {

            // Register the database instance and prefix
            YDDatabase::registerInstance(
                'default', 'mysql',
                YDConfig::get( 'db_name' ), YDConfig::get( 'db_user' ),
                YDConfig::get( 'db_pass' ), YDConfig::get( 'db_host' )
            );
            YDConfig::set( 'YD_DB_TABLEPREFIX', YDConfig::get( 'db_prefix' ) );
        
            // The database connection
            return YDDatabase::getNamedInstance();

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
         *  The scope which is going to be used for the template.
         *
         *  Can be either YD_SIMPLECMS_SCOPE_PUBLIC or YD_SIMPLECMS_SCOPE_ADMIN.
         */
        var $scope;

        /**
         *  The class constructor the the YDSimpleCMSTemplate class.
         *
         *  @param  $scope  The scope which is going to be used for the template. Can be either
         *                  YD_SIMPLECMS_SCOPE_PUBLIC or YD_SIMPLECMS_SCOPE_ADMIN.
         */
        function YDSimpleCMSTemplate( $scope ) {

            // Initialize the parent
            $this->YDTemplate();

            // Set the scope
            $this->scope = $scope;

        }

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

            // Set the variables
            $this->template_dir = YD_SIMPLECMS_SKINS_DIR . $this->scope;
            if ( $this->scope == YD_SIMPLECMS_SCOPE_PUBLIC ) {
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
     *  Define the CMS public request class.
     *
     *  The public request class doesn't perform any user authentication.
     *
     *  @ingroup YDSimpleCMS
     */
    class YDSimpleCMSPublicRequest extends YDRequest {

        /**
         *  The scope which is going to be used for the template.
         *
         *  Can be either YD_SIMPLECMS_SCOPE_PUBLIC or YD_SIMPLECMS_SCOPE_ADMIN.
         */
        var $requestScope  = YD_SIMPLECMS_SCOPE_PUBLIC;

        /**
         *  The default module for the request class.
         *
         *  Currently defaults to 'page' for the public request class.
         */
        var $defaultModule = 'page';

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
            $moduleManager = new YDSimpleCMSModuleManager();
            $moduleManager->loadAllModules();
            $module = $this->getQueryStringParameter( 'module', $this->defaultModule );
            $action = $this->getQueryStringParameter( 'action', 'show' );
            $moduleManager->runModule( $this->requestScope, $module, $action );
        }

    }

    /**
     *  Define the CMS admin request class.
     *
     *  This class forces authentication against the database (the \#_users table). Only if the authentication succeeds,
     *  you will be able to access the request.
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
    class YDSimpleCMSAdminRequest extends YDSimpleCMSPublicRequest {

        /**
         *  The scope which is going to be used for the template.
         *
         *  Can be either YD_SIMPLECMS_SCOPE_PUBLIC or YD_SIMPLECMS_SCOPE_ADMIN.
         */
        var $requestScope  = YD_SIMPLECMS_SCOPE_ADMIN;

        /**
         *  The default module for the request class.
         *
         *  Currently defaults to 'admin' for the admin request class.
         */
        var $defaultModule = 'admin';

        /**
         *  The constructor for the admin CMS request.
         *
         *  This one forces authentication and requires a configuration variable called 'YD_SIMPLECMS_SITEID' to be set.
         */
        function YDSimpleCMSAdminRequest() {

            // Initialize the parent
            $this->YDSimpleCMSPublicRequest();

            // Indicate we require login
            $this->setRequiresAuthentication( true );

            // Instantiate the template object
            $this->tpl = new YDSimpleCMSTemplate( YD_SIMPLECMS_SCOPE_ADMIN );

            // Get the site id
            $this->siteId = YDConfig::get( 'YD_SIMPLECMS_SITEID', 'SAMPLESITE' );

            // The names of the cookies
            $this->cookieNameUser = 'YD_SIMPLECMS_' . $this->siteId . '_USER';
            $this->cookieNamePass = 'YD_SIMPLECMS_' . $this->siteId . '_PASS';

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
            return false;
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
            $db = YDSimpleCMS::getDbConnection();
            $result = $db->getRecord(
                'SELECT * FROM #_users WHERE name = \'' . $db->escape( $fields['loginName'] ) . '\' AND password = \'' . $db->escape( $fields['loginPass'] ) . '\''
            );
            if ( $result === false ) {
                return array( '__ALL__' => t( 'err_login_all' ) );
            } else {
                $GLOBALS['user'] = $result;
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
         *  A reference to the module manager instance that loaded this module.
         */
        var $manager       = null;

        /**
         *  The current scope of the CMS module.
         *
         *  Currently, the value can only be YD_SIMPLECMS_SCOPE_PUBLIC or YD_SIMPLECMS_SCOPE_ADMIN.
         */
        var $currentScope  = null;

        /**
         *  The name of the action that is currently being executed.
         */
        var $currentAction = null;

        /**
         *  The class constructor the the YDSimpleCMSModule class.
         *
         *  @param  $manager    A reference to the module manager instance that loaded this module.
         */
        function YDSimpleCMSModule( $manager ) {
            $this->manager = & $manager;
            $this->tpl = new YDSimpleCMSTemplate( YD_SIMPLECMS_SCOPE_PUBLIC );
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
            $this->tpl->assign( 'adminMenu',     $GLOBALS['YD_SIMPLECMS_ADMIN_MENU'] );
            if ( isset( $GLOBALS['user'] ) ) {
                $this->tpl->assign( 'currentUser', $GLOBALS['user'] );
            }
            if ( $name == '' ) {
                $name = $this->getModuleName();
            }
            $this->tpl->displayWithMaster( $name );
        }

        /**
         *  This function triggers the indicated action in the indicated scope.
         *
         *  This function gets triggered by the module manager when this one wants to trigger an action.
         *
         *  @param  $scope  The scope in which to run the action. Can be YD_SIMPLECMS_SCOPE_PUBLIC or
         *                  YD_SIMPLECMS_SCOPE_ADMIN
         *  @param  $action The action to run.
         */
        function runAction( $scope, $action ) {
            $moduleFunctionName = YD_SIMPLECMS_ACTION_PREFIX . $scope . '_' . $action;
            if ( ! $this->hasMethod( $moduleFunctionName ) ) {
                YDSimpleCMS::showError( 'Module %s function not found: %s', $this->getModuleName(), $moduleFunctionName );
            }
            $this->currentScope  = $scope;
            $this->currentAction = $action;
            call_user_func( array( $this, $moduleFunctionName ) );
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
         *  @param  $scope  The scope in which to run the module and action. Can be YD_SIMPLECMS_SCOPE_PUBLIC or
         *                  YD_SIMPLECMS_SCOPE_ADMIN.
         *  @param  $module The name of the module to run.
         *  @param  $action The name of the action to run.
         */
        function runModule( $scope, $module, $action ) {

            // Convert everything to lowercase
            $scope  = strtolower( $scope );
            $module = strtolower( $module );
            $action = strtolower( $action );

            // Get the classname for the module
            $moduleClassName = YD_SIMPLECMS_MODULE_PREFIX . $module;

            // Check if the class exists
            if ( ! class_exists( $moduleClassName ) ) {
                YDSimpleCMS::showError( 'Module class not found: %s', $moduleClassName );
            }

            // Create the class instance
            $moduleInstance = new $moduleClassName( $this );

            // Set the correct template scope
            $moduleInstance->tpl->scope = $scope;

            // Sort the admin menu items
            if ( $scope == YD_SIMPLECMS_SCOPE_ADMIN ) {
                ksort( $GLOBALS['YD_SIMPLECMS_ADMIN_MENU'] );
                foreach ( $GLOBALS['YD_SIMPLECMS_ADMIN_MENU'] as $key=>$val ) {
                    if ( isset( $GLOBALS['YD_SIMPLECMS_ADMIN_MENU'][$key]['children'] ) ) {
                        ksort( $GLOBALS['YD_SIMPLECMS_ADMIN_MENU'][$key]['children'] );
                    }
                }
                $moduleInstance->tpl->assign( 'adminMenu', $GLOBALS['YD_SIMPLECMS_ADMIN_MENU'] );
            }

            // Create a link to ourselves
            $moduleInstance->manager = & $this;

            // Run the action
            $moduleInstance->runAction( $scope, $action );

        }

        /**
         *  This function returns a list with an instance of each loaded module.
         *
         *  @returns    A list with an instance of each loaded module.
         */
        function getModuleList() {
            $modules = array();
            foreach ( get_declared_classes() as $class ) {
                if ( substr( $class, 0, strlen( YD_SIMPLECMS_MODULE_PREFIX ) ) == YD_SIMPLECMS_MODULE_PREFIX ) {
                    $modules[$class] = new $class( $this );
                }
            }
            return $modules;
        }

    }

    // Add menu items
    YDSimpleCMS::addAdminMenu( 'Admin',   null,       'admin', 'show' );
    YDSimpleCMS::addAdminMenu( 'Admin',   'Logout',   'admin', 'logout' );
    YDSimpleCMS::addAdminMenu( 'Content', 'Pages',    'page',  'show' );
    YDSimpleCMS::addAdminMenu( 'Options', 'Modules',  'admin', 'modules' );
    YDSimpleCMS::addAdminMenu( 'Options', 'Settings', 'admin', 'settings' );

?>