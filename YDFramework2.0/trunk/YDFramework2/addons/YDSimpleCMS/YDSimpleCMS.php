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
     * @addtogroup YDSimpleCMS Addons - Simple CMS
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

    // Configuration
    YDConfig::set( 'YD_AUTO_EXECUTE', false );

    // Includes
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDDatabase.php' );
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDFileSystem.php' );
    YDInclude( 'YDF2_process.php' );
    YDInclude( 'YDSimpleCMS_CoreModules.php' );

    // Register the database instance and prefix
    YDDatabase::registerInstance(
        'default', 'mysql',
        YDConfig::get( 'db_name' ), YDConfig::get( 'db_user' ),
        YDConfig::get( 'db_pass' ), YDConfig::get( 'db_host' )
    );
    YDConfig::set( 'YD_DB_TABLEPREFIX', YDConfig::get( 'db_prefix' ) );

    // The global list with admin action
    $GLOBALS['YD_SIMPLECMS_ADMIN_MENU'] = array();

    // The database connection
    $GLOBALS['db'] = YDDatabase::getNamedInstance();

    /**
     *  This is the global YDSimpleCMS class that houses all cms related functions.
     *
     *  @ingroup YDSimpleCMS
     */
    class YDSimpleCMS {

        /**
         *  This is a static function that will run a request.
         *
         *  @param  $scope  The scope in which the request needs to run. You can choose between public and admin. If you
         *                  choose admin, authentication will be enforced, with public, it's not.
         *
         *  @static
         *
         *  @todo
         *      Replace scope with a defined constant (YD_SIMPLECMS_SCOPE_PUBLIC and YD_SIMPLECMS_SCOPE_ADMIN)
         */
        function run( $scope ) {
            $baseClass = ( strtolower( $scope ) == 'public' ) ? 'YDSimpleCMSPublicRequest' : 'YDSimpleCMSAdminRequest';
            $clsInst = new YDExecutor( $baseClass . '.php' );
            @session_start();
            $clsInst->execute();
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

    }

    /**
     *  Override the template class
     *
     *  @ingroup YDSimpleCMS
     */
    class YDSimpleCMSTemplate extends YDTemplate {

        // The scope of the template
        var $scope;

        // The constructor
        function YDSimpleCMSTemplate( $scope ) {

            // Initialize the parent
            $this->YDTemplate();

            // Set the scope
            $this->scope = $scope;

        }

        // Fetch the template using a master
        function fetchWithMaster( $template='', $master='__master' ) {

            // Set the variables
            $this->template_dir = YD_SIMPLECMS_SKINS_DIR . $this->scope;
            if ( $this->scope == 'public' ) {
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

        // Fetch the template using a master
        function displayWithMaster( $template='', $master='__master' ) {
            echo( $this->fetchWithMaster( $template, $master ) );
        }

    }

    /**
     *  Define the CMS request class.
     *
     *  @ingroup YDSimpleCMS
     */
    class YDSimpleCMSPublicRequest extends YDRequest {

        // Class variables
        var $requestScope  = 'public';
        var $defaultModule = 'page';

        // Default action
        function actionDefault() {
            $moduleManager = new YDSimpleCMSModuleManager();
            $moduleManager->loadAllModules();
            $module = $this->getQueryStringParameter( 'module', $this->defaultModule );
            $action = $this->getQueryStringParameter( 'action', 'show' );
            $moduleManager->runModule( $this->requestScope, $module, $action );
        }

    }

    /**
     *  Define the CMS request class.
     *
     *  @ingroup YDSimpleCMS
     */
    class YDSimpleCMSAdminRequest extends YDSimpleCMSPublicRequest {

        // Class variables
        var $requestScope  = 'admin';
        var $defaultModule = 'admin';

        // Constructor
        function YDSimpleCMSAdminRequest() {

            // Initialize the parent
            $this->YDSimpleCMSPublicRequest();

            // Indicate we require login
            $this->setRequiresAuthentication( true );

            // Instantiate the template object
            $this->tpl = new YDSimpleCMSTemplate( 'admin' );

            // Get the site id
            $this->siteId = YDConfig::get( 'YD_SIMPLECMS_SITEID', 'SAMPLESITE' );

            // The names of the cookies
            $this->cookieNameUser = 'YD_SIMPLECMS_' . $this->siteId . '_USER';
            $this->cookieNamePass = 'YD_SIMPLECMS_' . $this->siteId . '_PASS';

        }

        // Login function
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

        // Logout action
        function actionLogout() {
            $this->setCookie( $this->cookieNamePass, null );
            $this->redirectToAction( 'login' );
        }

        // Check for authentication
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

        // Redirect to the login if the authentication failed
        function authenticationFailed() {
            $this->forward( 'login' );
        }

        // Function to check the login
        function checkLogin( $fields, $md5=false ) {
            if ( $md5 === false ) {
                $fields['loginPass'] = md5( $fields['loginPass'] );
            }
            $db = YDDatabase::getNamedInstance();
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

        // Class variables
        var $name          = 'SimpleCMS Generic Module';
        var $description   = 'Base module for all the SimpleCMS modules.';
        var $version       = '1.0';
        var $authorName    = 'Pieter Claerhout';
        var $authorEmail   = 'pieter@yellowduck.be';
        var $authorUrl     = 'http://www.yellowduck.be';
        var $manager       = null;
        var $currentScope  = null;
        var $currentAction = null;

        // Constructor
        function YDSimpleCMSModule( $manager ) {
            $this->manager = & $manager;
            $this->tpl = new YDSimpleCMSTemplate( 'public' );
        }

        // Install the module
        function install() {
        }

        // Uninstall the module
        function uninstall() {
        }

        // Check if the module is required or not
        function isRequired() {
            return ! is_file( YD_SIMPLECMS_MODULE_DIR . '/' . $this->getClassName() . YD_SIMPLECMS_MODULE_EXT );
        }

        // Get the module name
        function getModuleName() {
            return substr( $this->getClassName(), strlen( YD_SIMPLECMS_MODULE_PREFIX ) );
        }

        // Display the module
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

        // Run an action in the module
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

        // Load the plugins
        function loadAllModules() {
            if ( is_dir( YD_SIMPLECMS_MODULE_DIR ) ) {
                $modulesDir = new YDFSDirectory( YD_SIMPLECMS_MODULE_DIR );
                foreach ( $modulesDir->getContents( YD_SIMPLECMS_MODULE_PATTERN ) as $module ) {
                    include_once( $module->getAbsolutePath() );
                }
            }
        }

        // Run a module
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
            if ( $scope == 'admin' ) {
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

        // Get the list of modules
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