<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    /*! \mainpage Yellow Duck Framework
     *
     *  The Yellow Duck framework is web application framework created by Pieter
     *  Claerhout. More information can be found on http://www.yellowduck.be/.
     *
     *  The Yellow Duck Framework takes care of all the difficult work you
     *  normally have to perform manually when developing a web application. It
     *  is based on the idea of requests that can perform actions. By
     *  encapsulating all the programming in an object-oriented environment, you
     *  get a framework that is easy to use and understand, easy to extend and
     *  doesn't limit you in any way.
     *
     *  The Yellow Duck Framework provides you with the following options:
     *  - Clean separation of code and output
     *  - Templates for outputting HTML easily
     *  - Automatic action dispatching using URL parameters
     *  - Object oriented form construction and validation
     *  - PEAR based database connectivity which supports all major database
     *    engines such as MySQL, PostgreSQL, Oracle, Microsoft SQL Server, ...
     *  - Object oriented handling of authentication
     *
     *  To implement the base functionality, a lot of standard libraries were
     *  used. Included is a partial copy of the PEAR library from the PHP
     *  project, which provides things such as database connectivity. More
     *  information and documentation on the PEAR library can be found on
     *  http://pear.php.net/ .
     *
     *  (c) 2002-2004 Pieter Claerhout, pieter@yellowduck.be
     *
     *  Webpage: http://www.yellowduck.be/
     *
     *  Author: Pieter Claerhout, pieter@yellowduck.be
     */

    // Only execute this once
    if ( ! defined( 'YD_REQ_PROCESSED' ) ) {

        // Set the error reporting correctly.
        error_reporting( E_ALL ^ E_NOTICE );

        // Start the session
        session_start();

        /**
         *  @enum YD_FW_NAME
         *        Name of the framework, e.g. "Yellow Duck Framework".
         */
        define( 'YD_FW_NAME', 'Yellow Duck Framework' );

        /**
         *  @enum YD_FW_VERSION
         *        Version of the framework, e.g. "2.0.0".
         */
        define( 'YD_FW_VERSION', '2.0.0' );

        /**
         *  @enum YD_FW_NAMEVERS
         *        The combination of the two items above, e.g.
         *        "Yellow Duck Framework 2.0.0".
         */
        define( 'YD_FW_NAMEVERS', YD_FW_NAME . ' ' . YD_FW_VERSION );

        /**
         *  @enum YD_FW_HOMEPAGE
         *        The homepage of the Yellow Duck Framework.
         */
        define( 'YD_FW_HOMEPAGE', 'http://www.yellowduck.be/ydf2/' );

        /**
         *  @enum YD_DIR_HOME
         *        Home directory of the Yellow Duck Framework. This is the full
         *        path to the YDFramework2 directory.
         *
         *  @internal
         */
        define( 'YD_DIR_HOME', dirname( __FILE__ ) );

        /**
         *  @enum YD_DIR_CLSS
         *        Class directory of the Yellow Duck Framework. This is the full
         *        path to the YDFramework2/YDClasses directory.
         *
         *  @internal
         */
        define( 'YD_DIR_CLSS', YD_DIR_HOME . '/YDClasses' );

        /**
         *  @enum YD_DIR_SMPI
         *        Smarty plugins directory of the Yellow Duck Framework. This is
         *        the full path to the YDFramework2/YDSmartyPlugins directory.
         *
         *  @internal
         */
        define( 'YD_DIR_SMPI', YD_DIR_HOME . '/YDSmartyPlugins' );

        /**
         *  @enum YD_DIR_3RDP
         *        Third party code directory of the Yellow Duck Framework. This
         *        is the full path to the YDFramework2/3rdparty directory.
         *
         *  @internal
         */
        define( 'YD_DIR_3RDP', YD_DIR_HOME . '/3rdparty' );

        /**
         *  @enum YD_DIR_3RDP_PEAR
         *        PEAR code directory of the Yellow Duck Framework. This is the
         *        full path to the YDFramework2/3rdparty/PEAR directory.
         *
         *  @internal
         */
        define( 'YD_DIR_3RDP_PEAR', YD_DIR_3RDP . '/PEAR' );

        /**
         *  @enum YD_DIR_TEMP
         *        Temporary directory of the Yellow Duck Framework. This is the
         *        full path to the YDFramework2/temp directory. This is the
         *        directory where the compiled templates are stored. Make sure
         *        this directory is world writeable.
         *
         *  @internal
         */
        define( 'YD_DIR_TEMP', YD_DIR_HOME . '/temp' );

        /**
         *  @enum YD_ACTION_PARAM
         *        Name of the $_GET variable that is used to determine which
         *        action is requested. By default, this is the argument
         *        specified with the name "do".
         *
         *  @internal
         */
        define( 'YD_ACTION_PARAM', 'do' );

        /**
         *  @enum YD_ACTION_DEFAULT
         *        the name of the $_GET parameter that specifies which action
         *        needs to be executed. This is "do" by default.
         *
         *  @internal
         */
        define( 'YD_ACTION_DEFAULT', 'actionDefault' );

        /**
         *  @enum YD_SELF_SCRIPT
         *        Contains the current script's path, e.g. "/myapp/index.php".
         */
        define( 'YD_SELF_SCRIPT', $_SERVER['SCRIPT_NAME'] );

        // Add SCRIPT_FILENAME if not defined.
        if ( ! isset( $_SERVER['SCRIPT_FILENAME'] ) ) {
            $_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED'];
        }

        /**
         *  @enum YD_SELF_FILE
         *        Contains the physical script's path, e.g.
         *        "c:\myapp\index.php".
         */
        define( 'YD_SELF_FILE', $_SERVER['SCRIPT_FILENAME'] );

        /**
         *  @enum YD_SELF_DIR
         *        Contains the physical script's directory, e.g. "c:\myapp".
         */
        define( 'YD_SELF_DIR', dirname( YD_SELF_FILE ) );

        /**
         *  @enum YD_SELF_URI
         *        The URI which was given in order to access thisp age, e.g.
         *        "/myapp/index.php?do=edit"
         */
        define( 'YD_SELF_URI', $_SERVER['REQUEST_URI'] );

        /**
         *  @enum YD_TPL_EXT
         *        The extension used by the template files.
         *
         *  @internal
         */
        define( 'YD_TPL_EXT', '.tpl' );

        /**
         *  @enum YD_SCR_EXT
         *        The extension used by the script files.
         *
         *  @internal
         */
        define( 'YD_SCR_EXT', '.php' );

        /**
         *  @enum YD_TMP_PRE
         *        The prefix used by the temporary files.
         *
         *  @internal
         */
        define( 'YD_TMP_PRE', 'YDF_' );

        // Get debugging mode
        if ( ! defined( 'YD_DEBUG' ) ) {
            if ( $_GET['YD_DEBUG'] == 1 ) {
                define( 'YD_DEBUG', 1 )
            } else {
                define( 'YD_DEBUG', 0 );
            }
        }

        // Get the path delimiter
        if (
            strtoupper( PHP_OS ) == 'WINNT'
            ||
            strtoupper( PHP_OS ) == 'WINDOWS'
        ) {
            define( 'YD_PATHDELIM', ';' );
        } else {
            define( 'YD_PATHDELIM', ':' );
        }

        $includePath = YD_SELF_DIR;
        if ( is_dir( YD_SELF_DIR . '/includes' ) ) {
            $includePath .= YD_PATHDELIM . YD_SELF_DIR . '/includes';
        }
        $includePath .= YD_PATHDELIM . YD_DIR_CLSS;
        $includePath .= YD_PATHDELIM . YD_DIR_3RDP;
        $includePath .= YD_PATHDELIM . YD_DIR_3RDP_PEAR;
        ini_set( 'include_path', $includePath );

        // Include the basis of Yellow Duck framework
        require_once( 'YDBase.php' );
        require_once( 'YDError.php' );
        require_once( 'YDObjectUtil.php' );

        /**
         *  This function will return true if the current version is lower than
         *  the indicated version. Internally, this uses the phpversion()
         *  function to get the current version number.
         *
         *  @param $vercheck Minimum version required.
         *
         *  @return Boolean indicating if the current version number is higher
         *          or lower than the indicated PHP version.
         */
        function YDVersionCheck( $vercheck ) {
            $minver = explode( '.', $vercheck );
            $curver = explode( '.', phpversion() );
            if (
                ( $curver[0] <= $minver[0] ) && ( $curver[1] <= $minver[1] )
                && (
                $curver[1] <= $minver[1] ) && ( $curver[2][0] < $minver[2][0]
            ) ) {
                return true;
            } else {
                return false;
            }
        }


        // Check if we have the right PHP version
        if ( YDVersionCheck( '4.2.0' ) ) {
            new YDFatalError( 'PHP version 4.2.0 or greater is required.' );
        }

        // Check if running in debugging mode
        if ( YD_DEBUG == 1 ) {

            // Include the timer class
            require_once( 'YDTimer.php' );

            // Instantiate the timer
            $timer = new YDTimer();

        }

    }

?>
